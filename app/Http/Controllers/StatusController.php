<?php

namespace App\Http\Controllers;

use App\Enums\ShopStatus;
use App\Services\StatusService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

class StatusController extends Controller

{
    public function __construct(public StatusService $statusService)
    {
    }
    public function change($type, $id, $status)
    {
        $model = Relation::getMorphedModel($type);
        $instance = $model::findOrFail($id);
        $this->statusService->changeStatus($instance, ShopStatus::from($status));
        return response()->noContent();
    }
}
