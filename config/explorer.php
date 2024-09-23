<?php

declare(strict_types=1);

use App\Models\Log;
use App\Models\Product;
use App\Models\Suggest;
use Illuminate\Support\Facades\Log as FacadesLog;

return [
    /*
     * There are different options for the connection. Since Explorer uses the Elasticsearch PHP SDK
     * under the hood, all the host configuration options of the SDK are applicable here. See
     * https=>//www.elastic.co/guide/en/elasticsearch/client/php-api/current/configuration.html
     */
    'connection' => [
        'host' => env('ELASTICSEARCH_HOST', 'es01'),
        'port' => env('ELASTICSEARCH_PORT', '9200'),
        'scheme' => env('ELASTICSEARCH_SCHEME', 'https'),
        'auth' => [
            'username' => env('ELASTICSEARCH_USER', 'elastic'),
            'password' => env('ELASTICSEARCH_PASSWORD', 'PXlVWgDa6FXXmnOAbFDR')
        ],
        'ssl' => [
            'verify' => false
        ]
    ],

    /**
     * The default index settings used when creating a new index. You can override these settings
     * on a per-index basis by implementing the IndexSettings interface on your model or defining
     * them in the index configuration below.
     */
    'default_index_settings' => [
        'index' => [
            "max_ngram_diff" => 50,
            'max_result_window' => 100000,
        ],
        //'analysis' => [],
    ],

    /**
     * An index may be defined on an Eloquent model or inline below. A more in depth explanation
     * of the mapping possibilities can be found in the documentation of Explorer's repository.
     */
    'indexes' => [
        // Suggest::class,
        'suggests' => [
            "settings" => [

                "analysis" => [
                    "filter" => [
                        "russian_keywords" => [
                            "type" => "keyword_marker",
                            "keywords" => ["дисплей"]
                        ],
                        "russian_stop" => [
                            "type" => "stop",
                            "stopwords" => "_russian_"
                        ],
                        "russian_stemmer" => [
                            "type" => "stemmer",
                            "language" => "russian"
                        ]
                    ],
                    "tokenizer" => [
                        "my_tokenizer" => [
                            "type" => "ngram",
                            "min_gram" => 1,
                            "max_gram" => 2,
                            "token_chars" => [
                                "letter",
                                "digit"
                            ]
                        ],
                    ],
                    "analyzer" => [
                        "sug_tokenizer" => [
                            "tokenizer" => "my_tokenizer",
                            "filter" => [
                                "lowercase",
                            ]
                        ],
                        "rebuilt_russian" => [
                            "tokenizer" => "standard",
                            "filter" => [
                                "lowercase",
                                "russian_stop",
                                "russian_keywords",
                                "russian_stemmer",
                            ]
                        ]
                    ]
                ]

            ],
            "properties" => [
                "id" => [
                    "type" => "keyword",

                ],
                "query" => [
                    "type" => "completion",

                    "analyzer" => "sug_tokenizer",


                ],
                "count" => [
                    "type" => "integer"
                ]
            ]
        ],
        Product::class
    ],

    /**
     * You may opt to keep the old indices after the alias is pointed to a new index.
     * A model is only using index aliases if it implements the Aliased interface.
     */
    'prune_old_aliases' => true,

    /**
     * When set to true, sends all the logs (requests, responses, etc.) from the Elasticsearch PHP SDK
     * to a PSR-3 logger. Disabled by default for performance.
     */
    'logging' => env('EXPLORER_ELASTIC_LOGGER_ENABLED', false),
    'logger' => 'daily',
];