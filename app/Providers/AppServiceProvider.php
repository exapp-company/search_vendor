<?php

namespace App\Providers;

use App\Models\AdminUser;
use App\Models\Office;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use MoonShine\Models\MoonshineUser;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //        if (env('APP_DEBUG')) {
        //            DB::listen(function ($query) {
        //                Log::debug('SQL', ['query' => $query->sql, 'bindings' => $query->bindings, 'time' => $query->time]);
        //            });
        //        }
        Relation::enforceMorphMap([
            'shop' => Shop::class,
            'office' => Office::class,
            'user' => User::class,
            'muser' => MoonshineUser::class,
            'admin' => AdminUser::class,
        ]);
        JsonResource::wrap('items');

        // DB::listen(function ($query) {
        //     File::append(
        //         storage_path('/logs/query.log'),
        //         '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL
        //     );
        // });

        //        resolve(EngineManager::class)->extend('elasticsearch', function () {
        //            return new ElasticDriver(
        //                ClientBuilder::create()->build(),
        //                config('scout.elasticsearch.index')
        //            );
        //        });
    }
}
