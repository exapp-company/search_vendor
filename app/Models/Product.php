<?php

namespace App\Models;

use App\Services\ProductService;
use App\Services\SynonymService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;
use Laravel\Scout\Searchable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class Product extends Model implements Explored, IndexSettings, Sitemapable
{
    use Searchable;

    protected $fillable = [
        'name',
        'brand',
        'price',
        'description',
        'url',
        'picture',
        'shop_id',
        'in_stock',
        'amount',
        'wholesale_price',
        'sku'
    ];

    // protected function casts(): array
    // {
    //     return [
    //         'in_stock' => 'boolean',
    //     ];
    // }
    public static function booted()
    {
        $productService = new ProductService();
        static::creating(function (Product $product) use ($productService) {
            $slug = $productService->makeSlug($product);
            $product->slug = $slug;
            //Product::withoutSyncingToSearch(fn () => $productService->createSlug($product));
        });
    }
    public function toSearchableArray(): array
    {
        $stocks = $this->stocks;
        $stocks->load('office.locations');
        $stocksResult = $stocks->map(function ($item, $key) {
            $result = [
                'amount' => (int) $item->amount,
                'in_stock' => (bool) $item->in_stock,
                'city_id' => (int) $item->office->city_id,
                'city_id_' . $item->office->city_id => (bool) $item->in_stock,
                'location' => [
                    'lat' => $this->office?->lat,
                    'lon' => $this->office?->lon
                ]
            ];
            return $result;
        });
        $locations = $stocks->pluck('office')->pluck('locations')->flatten()->groupBy('type');
        $locationsTypes = [];
        foreach (['metro', 'district', 'transport_stop', 'mart'] as $key) {
            $locationsTypes[$key] = $locations->get($key, collect([]))->pluck('id')->toArray();
        }
        $result = [
            'name' => (string) $this->name,
            'price' => (float) $this->price,
            'url' => (string) $this->url,
            'picture' => $this->picture,
            'in_stock' => (bool) $this->in_stock,
            'amount' => (int) $this->amount,
            'shop_id' => (int) $this->shop_id,
            'wholesale_price' => (float) $this->wholesale_price,
            'sku' => (string) $this->sku,
            'product_numbers' => (string) $this->name,
            'stocks' => $stocksResult,
            'locations' => $locationsTypes
        ];
        return $result;
    }
    public function indexSettings(): array
    {
        $synonymService = new SynonymService();
        $setting = [
            "analysis" => [

                "filter" => [
                    "russian_stop" => [
                        "type" => "stop",
                        "stopwords" => "_russian_"
                    ],
                    "russian_stemmer" => [
                        "type" => "stemmer",
                        "language" => "russian"
                    ],
                    "product_synonyms" => [
                        "type" => "synonym_graph",
                        "synonyms_set" => "products-synonym",
                        "updateable" => true
                    ],
                    "trigrams_filter" => [
                        "type" => "ngram",
                        "min_gram" => 3,
                        "max_gram" => 4
                    ],
                    "product_numbers" => [
                        "type" => "pattern_capture",
                        "preserve_original" => true,
                        "patterns" => [
                            "(\\b((?=[A-Za-z\\-]{0,19}\\d)[A-Za-z0-9\\-]{4,20})\\b)"
                        ]
                    ]
                ],
                "analyzer" => [
                    "rebuilt_russian" => [
                        "tokenizer" => "standard",
                        "filter" => [
                            "lowercase",
                            //"trigrams_filter"
                        ]
                    ],
                    "rebuilt_search_russian" => [
                        "tokenizer" => "standard",
                        "filter" => [
                            "lowercase",
                            "product_synonyms",
                            //
                        ]
                    ],
                    "product_numbers_analyzer" => [
                        "tokenizer" => "standard",
                        "filter" => [
                            "lowercase",
                            "product_numbers",
                            "trigrams_filter"
                        ]
                    ]
                ],

            ],
            // "defaults" => [
            //     "index" => [
            //         "max_ngram_diff" => 50,
            //         'max_result_window' => 100000,
            //     ],
            // ]

        ];
        if (!$synonymService->checkSet("products-synonym")) {
            $synonymService->refreshSynonymSet("products-synonym", Synonym::all());
        }
        return $setting;
    }
    public function getModel()
    {
        return 'products';
    }
    /**
     * Get the key name used to index the model.
     */
    public function mappableAs(): array
    {
        return [
            "id" => [
                "type" => "keyword"
            ],
            "name" => [
                "type" => "text",
                "analyzer" => "rebuilt_russian",
                "search_analyzer" => "rebuilt_search_russian",
                "fields" => [
                    "keyword" => [
                        "type" => "keyword",
                    ]
                ]
            ],
            "product_numbers" => [
                "type" => "text",
                "analyzer" => "product_numbers_analyzer",
                "search_analyzer" => "product_numbers_analyzer",
            ],
            "picture" => [
                "type" => "text",
                "index" => false
            ],
            "price" => [
                "type" => "integer",
            ],
            "wholesale_price" => [
                "type" => "integer",
            ],
            "shop_id" => [
                "type" => "long",

            ],
            "url" => [
                "type" => "text",
                "index" => false

            ],
            "sku" => [
                "type" => "text",

            ],
            "stocks" => [
                "type" => "nested",
                "properties" => [
                    "in_stock" => [
                        "type" => "boolean"
                    ],
                    "amount" => [
                        "type" => "integer",
                    ],
                    "city_id" => "integer",
                    "city_id_*" => "boolean",
                    "location" => [
                        "type" => "geo_point"
                    ]
                ]
            ],
            "locations" => [
                "type" => "nested",
                "properties" => [
                    "district" => "integer",
                    "metro" => "integer",
                    "mart" => "integer",
                    "transport_stop" => "integer",
                ]
            ]
        ];
    }

    public function searchableAs(): string
    {
        return 'products';
    }
    protected function casts(): array
    {
        return [
            'in_stock' => 'boolean',
            'price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'shop_id' => 'integer',
        ];
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
    public function available_stocks()
    {
        return $this->stocks()->where('in_stock', true)->whereHas('office', function (Builder $query) {
            $query->where('city_id', request('city_id'));
        })->get();
    }

    public function favoriteByUser()
    {
        return $this->hasMany(FavoriteProduct::class, 'product_id');
    }



    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {

        $product = null;
        Log::info($value);
        if (is_numeric($value)) {
            Log::info("int");
            $product = $this->where('id', $value)->firstOrFail();
        } else {
            $product = $this->where('slug', $value)->firstOrFail();
        }
        return $product;
    }

    public function offices()
    {
        return $this->hasManyThrough(Office::class, Stock::class);
    }
    public function hashCode()
    {
        return $this->name . "|$|" . $this->price . "|$|" . $this->whosale_price . "|$|" . $this->amount . "|$|" . $this->picture . "|$|" . $this->in_stock . "|$|" . $this->sku;
    }

    public function toSitemapTag(): Url|string|array
    {
        return Url::create('https://poiskzip.ru/product/' . $this->slug)
            ->setLastModificationDate(Carbon::create($this->updated_at))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(0.9);
    }


    public function productLists()
    {
        return $this->belongsToMany(ProductList::class, 'product_product_list')->withPivot('count');
    }
}
