<?php

namespace App\Services\Search;

use App\DTO\ProductSearch;
use App\Events\QueryCompleted;
use App\Filters\ProductSearchFilter;
use App\Filters\ProductSearchSorting;
use App\Models\Product;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use JeroenG\Explorer\Domain\Query\QueryProperties\QueryProperty;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\MatchPhrase;
use JeroenG\Explorer\Domain\Syntax\Nested;
use JeroenG\Explorer\Domain\Syntax\Range;
use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;
use JeroenG\Explorer\Domain\Syntax\Term;
use JeroenG\Explorer\Domain\Syntax\Terms;
use JeroenG\Explorer\Infrastructure\Console\ElasticSearch;

class MinimumShouldMatch implements QueryProperty
{
    private $value = 2;
    public function __construct($value = '50%')
    {
        $this->value = $value;
    }
    public function build(): array
    {
        return [
            'minimum_should_match' => $this->value
        ];
    }
}

class Suggest implements QueryProperty
{
    public function __construct(public $query = '') {}
    public function build(): array
    {
        return [
            "_source" => ["query"],
            "suggest" => [
                "my_suggest" => [
                    "prefix" => $this->query,
                    "completion" => [
                        "field" => "query",
                    ]
                ]

            ]
        ];
    }
}

class SearchService
{


    public function searchProducts(ProductSearch $searchParams)
    {
        $result = $this->executeSearch($searchParams);
        //QueryCompleted::dispatch($searchParams->query);
        return $result;
        // $cacheKey = $this->generateCacheKey('search', $searchParams);

        // return Cache::remember($cacheKey, 3600, function () use ($searchParams) {

        // });
    }


    public function quickSearchProducts(ProductSearch $searchParams, int $limit = 5): mixed
    {
        return $this->executeQuickSearch($searchParams, $limit);
    }


    private function generateCacheKey(string $prefix, ProductSearch $searchParams): string
    {
        return $prefix . '_' . md5(json_encode($searchParams));
    }


    public function executeSearch(ProductSearch $searchParams)
    {

        //dd($searchParams);
        $match = new Matching('name', $searchParams->query, 0);
        $match->setMinimumShouldMatch('100%');
        //$match->setFuzziness);
        //$matchPhrase = new MatchPhrase('name',  $searchParams->query);
        // $match->setAnalyzer("rebuilt_russian");
        $search = Product::search()
            ->must($match);
        if (!is_null($searchParams->sortBy)) {
            $search->orderBy($searchParams->sortBy, $searchParams->sortDirection);
        }

        if (!is_null($searchParams->priceMin)) {

            $search->filter(new Range('price', ['gte' => (int) $searchParams->priceMin]));
        }
        if (!is_null($searchParams->priceMax)) {
            $search->filter(new Range('price', ['lte' => $searchParams->priceMax]));
        }
        $search->must(new Term('in_stock', true));
        if ($searchParams->city_id) {
            // $nested1 = new Nested('stocks', new Term('stocks.in_stock', true));
            $nested = new Nested('stocks', new Term('stocks.city_id_' . $searchParams->city_id, true));
            //$search->filter($nested1);
            $search->filter($nested);
        }
        if (count($searchParams->suppliers)) {
            $search->filter(new Terms('shop_id', $searchParams->suppliers));
        }
        if (count($searchParams->locations)) {
            foreach ($searchParams->locations as $key => $value) {
                $searchParams->locations[$key] = Arr::whereNotNull(array_values($value));
                if (count($searchParams->locations[$key])) {
                    $nested = new Nested('locations',  new Terms('locations.' . $key, $searchParams->locations[$key]));
                    $search->filter($nested);
                }
            }
        }
        return $search;
    }


    private function executeQuickSearch(ProductSearch $searchParams, int $limit)
    {
        $client = ClientBuilder::create()
            ->setHosts([['host' => config('explorer.connection.host'), 'scheme' => config('explorer.connection.scheme'), 'port' => config('explorer.connection.port')]])
            ->setBasicAuthentication(config('explorer.connection.auth.username'), config('explorer.connection.auth.password'))
            ->setSSLVerification(false)
            ->build();
        $result = $client->search(
            [
                'index' => 'suggests',
                'body'  => (new Suggest($searchParams->query))->build()
            ]
        );
        //dd($result['suggest']['my_suggest'][0]['options']);
        return Arr::pluck($result['suggest']['my_suggest'][0]['options'],  'text');
    }
}
