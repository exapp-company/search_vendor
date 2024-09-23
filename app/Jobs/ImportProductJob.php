<?php

namespace App\Jobs;

use App\Enums\ShopStatus;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Services\Documents\JSONParser;
use App\Services\Documents\Parser;
use App\Services\Documents\XMLParser;
use App\Services\ProductService;
use Carbon\Carbon;
use Database\Seeders\SlugSeeder;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;
use Throwable;


class ImportProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $feeds = [];
    private $offices_count;
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 7200;
    private Parser $parser;
    private $start_time;
    public $failOnTimeout = true;
    private ProductService $productService;
    /**
     * Create a new job instance.
     */
    public function __construct(public Shop $shop)
    {
        switch ($shop->feed_type) {
            case 'xml':
                $this->parser = new XMLParser($shop);
                break;
            case 'json':
                $this->parser = new JSONParser($shop);
                break;
        }
        $this->productService = new ProductService();
        $this->start_time = Carbon::now();
        $feeds = [];

        $this->offices_count = $this->shop->offices->where('status', ShopStatus::active->value)->count();
        if ($shop->feed_url) {
            $feeds[] = [
                'url' => $shop->feed_url,
                'mapping' => $shop->feed_mapping,
                'item' => $shop->feed_item
            ];
        }
        foreach ($shop->offices->where('status', ShopStatus::active->value) as $office) {
            $mapping = null;
            if (!$office->use_parent_feed && $office->feed_url) {
                $mapping = $office->use_parent_mapping ? $shop->feed_mapping : $office->feed_mapping;
            }
            if ($mapping) {
                $feeds[] = [
                    'url' => $office->feed_url,
                    'mapping' => $mapping,
                    'item' => $shop->feed_item
                ];
            }
        }

        $office_feeds = $shop->offices->pluck('feed')->filter()->all();
        $feeds = array_merge($feeds, $office_feeds);
        $this->feeds = $feeds;
    }
    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->shop->id;
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info($this->shop->name, ['feeds' => $this->feeds]);
        Log::info($this->offices_count);
        if (!count($this->feeds) || $this->shop->import_progress == 'active' || !$this->offices_count) {
            $this->shop->import_progress = 'canceled';
            $this->shop->save();
            return;
        }
        $this->shop->import_progress = 'active';
        $this->shop->save();
        Log::info("начинаю собирать коллекцию");
        $products = $this->parser->parse($this->feeds);
        $products = $this->parser->setStocks($products, $this->shop);
        $currentProducts = Product::where('shop_id', $this->shop->id)->with('stocks')->get()->mapWithKeys(fn($item, $key) => [$item['url'] => $item]);
        $unSearch = [];
        $toSearch = [];
        $newProducts = [];
        $updatedProducts = [];
        Log::info("коллекция собрана нажинаю удалять лишнее");
        foreach ($currentProducts as $url => $product) {
            if (!Arr::has($products, $url) && $product->in_stock) {
                $product->in_stock = false;
                $product->amount = null;
                $product->stocks()->delete();
                Product::withoutSyncingToSearch(fn() => $product->save());
                $unSearch[$product->id] = true;
            }
        }
        Log::info("удалил лишнее");
        $updated = 0;
        $new = 0;
        Log::info("начинаю импорт");
        foreach ($products as $url => $product) {
            if (Arr::has($currentProducts, $url)) {
                $curProduct = Arr::get($currentProducts, $url);
                $oldHash = $curProduct->hashCode();
                $curProduct->fill($product);
                $curProduct->in_stock  = (int) !! $curProduct->in_stock;
                if ($oldHash != $curProduct->hashCode()) {
                    $updated++;
                    $toSearch[$curProduct->id] = true;
                    $updatedProducts[] = $curProduct;
                }
            } else {
                $curProduct = new Product($product);
                $curProduct->in_stock = (int) !!$curProduct->in_stock;
                $curProduct->shop_id = $this->shop->id;
                $newProducts[] = $curProduct;
                $toSearch[$curProduct->id] = true;
                $new++;
            }
        }
        Log::info("к удалению из поиска " . count($unSearch));
        $unSearch = collect(array_keys($unSearch));
        foreach ($unSearch->chunk(500) as $items) {
            Product::whereIn('id', $items)->searchable();
        }
        $newProducts = collect($newProducts);
        foreach ($newProducts->chunk(500) as $items) {
            Product::withoutSyncingToSearch(fn() => Product::insert($items->toArray()));
        }
        Log::info("добавлено " . $new);
        $updatedProducts = collect($updatedProducts);
        foreach ($updatedProducts->chunk(500) as $items) {
            Product::withoutSyncingToSearch(
                fn() => Product::upsert(
                    $items
                        ->map(function ($item) {
                            return $item->getAttributes();
                        })->toArray(),
                    ['id'],
                    ['picture', 'name', 'sku', 'price', 'amount', 'wholesale_price', 'in_stock']
                )
            );
        }
        Log::info("обновлено " . $updated);
        $uStocks = 0;
        $nStocks = 0;
        $dStocks = 0;
        $updatedStocks = [];
        $newStocks = [];
        $deletedStocks = collect([]);
        $currentProducts = Product::where('shop_id', $this->shop->id)->with('stocks')->get()->mapWithKeys(fn($item, $key) => [$item['url'] => $item]);
        foreach ($products as $url => $product) {
            $curProduct = $currentProducts->get($url);
            $actualStocks = [];
            foreach ($product['stocks'] as $stock) {
                if (!$stock["in_stock"]) {
                    continue;
                }
                $currentStock = $curProduct->stocks->where('office_id', $stock['office_id'])->first();
                if ($currentStock) {
                    $oldHash = $currentStock->hashCode();
                    $currentStock->amount = Arr::get($stock, 'amount');
                    $currentStock->in_stock = (int) !!$stock['in_stock'];
                    $actualStocks[] = $currentStock->id;
                    if ($oldHash != $currentStock->hashCode()) {
                        $toSearch[$curProduct->id] = true;
                        $updatedStocks[] = $currentStock;
                        $uStocks++;
                    }
                } else {
                    $currentStock = new Stock();
                    $currentStock->amount = Arr::get($stock, 'amount');
                    $currentStock->office_id = $stock['office_id'];
                    $currentStock->product_id = $curProduct->id;
                    $currentStock->in_stock = 1;
                    $newStocks[] = $currentStock;
                    $nStocks++;
                    $toSearch[$curProduct->id] = true;
                }
            }
            $delStocks = $curProduct->stocks->whereNotIn('id', $actualStocks)->pluck('id');

            if ($delStocks->count()) {
                $toSearch[$curProduct->id] = true;
                $deletedStocks->merge($delStocks);
                $dStocks += $delStocks->count();
            }
        }
        foreach ($deletedStocks->chunk(1000) as $items) {
            Stock::whereIn('id', $items)->delete();
        }
        Log::info("удалено остатков " . $dStocks);
        $newStocks = collect($newStocks);
        foreach ($newStocks->chunk(500) as $items) {
            Stock::insert($items->toArray());
        }
        Log::info("добавлено остатков " . $nStocks);
        $updatedStocks = collect($updatedStocks);
        foreach ($updatedStocks->chunk(500) as $items) {
            Stock::upsert($items->toArray(), ['id'], ['in_stock', 'amount']);
        }
        Log::info("обновлено остатков " . $uStocks);


        Log::info("к индексированию " . count($toSearch));
        $toSearch = collect(array_keys($toSearch));

        foreach ($toSearch->chunk(500) as $items) {
            Product::whereIn('id', $items)->searchable();
        }

        (new SlugSeeder())->run();

        $this->shop->import_progress = 'success';
        $this->shop->save();
        Log::info("Завершено\n\n");
    }
    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        $this->shop->import_progress = 'failed';
        $this->shop->save();

        // Send user notification of failure, etc...
    }
}
