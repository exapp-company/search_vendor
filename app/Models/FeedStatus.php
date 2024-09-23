<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedStatus extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
    ];


    public function feeds(): HasMany
    {
        return $this->hasMany(Feed::class);
    }
}
