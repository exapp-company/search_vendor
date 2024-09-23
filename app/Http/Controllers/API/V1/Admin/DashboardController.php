<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Enums\HttpStatus;
use App\Http\Controllers\ApiController;
use App\Http\Requests\ShopRequest;
use App\Http\Resources\Collections\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\DashboardRepository;

class DashboardController extends ApiController
{
    public function __construct()
    {
    }

    public function index()
    {
        $users = User::get();
        $shops = Shop::groupBy('city_id');

        return [
            'users_count' => $users->count(),
            'supplier_count' => $users->where('role', 'supplier')->count(),
            'cities_count' => $shops->count(),
            'chart_query' => [],
            'top_query' => []
        ];
    }
}
