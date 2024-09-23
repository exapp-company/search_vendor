<?php

namespace App\Traits;

use App\Models\City;

trait HasCity
{
    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }
}
