<?php

namespace App\Services;

use App\Enums\ShopStatus;
use Illuminate\Database\Eloquent\Model;

class StatusService
{
    public function changeStatus(Model $model, ShopStatus $status)
    {
        $model->status = $status;
        $model->save();
    }
}
