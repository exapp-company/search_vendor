<?php

namespace App\Console\Commands;

use App\Jobs\ImportProductJob;
use App\Models\Feed;
use App\Models\Product;
use App\Models\Shop;
use App\Services\Documents\XMLParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ImportProductsCommand extends Command
{
    protected $signature = 'import:products {shop_id?}';

    protected $description = 'Импорт продуктов из XML-файла в базу данных';
    public function handle(): void
    {
        try {
            $shops = Shop::where('status', 'active');

            if ($this->argument("shop_id")) {
                $shops = $shops->where("id", $this->argument("shop_id"));
            }
            $shops = $shops->get();
            foreach ($shops as $shop) {
                ImportProductJob::dispatch($shop);
            }
        } catch (Throwable $e) {
            Log::error("Произошла ошибка при импорте продуктов: {$e->getMessage()}");
        }
    }

    private function importProduct($offer, $shopId): void
    {
        Product::create([
            'name' => $offer['name'] ?? null,
            'brand' => $offer['vendor'] ?? null,
            'price' => $offer['price'] ?? null,
            'description' => $offer['description'] ?? null,
            'url' => $offer['url'] ?? null,
            'picture' => $offer['picture'] ?? null,
            'shop_id' => $shopId,
        ]);
    }
}
