<?php

namespace App\Listeners;

use App\DTO\ProductSearch;
use App\Events\QueryCompleted;
use App\Models\Suggest;
use App\Services\Search\SearchService;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use JeroenG\Explorer\Domain\Aggregations\TermsAggregation;
use JeroenG\Explorer\Domain\Query\QueryProperties\TrackTotalHits;
use JeroenG\Explorer\Infrastructure\Scout\ElasticEngine;
use PDO;

class QueryComletedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(public SearchService $searchService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(QueryCompleted $event): void
    {

        $client = ClientBuilder::create()
            ->setHosts([['host' => config('explorer.connection.host'), 'scheme' => config('explorer.connection.scheme'), 'port' => config('explorer.connection.port')]])
            ->setBasicAuthentication(config('explorer.connection.auth.username'), config('explorer.connection.auth.password'))
            ->setSSLVerification(false)
            ->build();
        $suggest = new Suggest();
        Suggest::withoutSyncingToSearch(function () use ($event, &$suggest) {
            $suggest = Suggest::firstOrCreate(['query' => $event->query], ['count' => 0, 'result_count' => $event->count]);
            $suggest->count += 1;
            $suggest->result_count = $event->count;

            $suggest->save();
        });

        if ($event->count) {
            $client->update([
                'index' => 'suggests',
                'id' => $suggest->id,
                'body' => [

                    "script" => "ctx._source.query.weight += 1",
                    "upsert" => [
                        "query" => [
                            "input" => $event->query,
                            "weight" => 1
                        ]
                    ]

                ]
            ]);
        }
    }
}
