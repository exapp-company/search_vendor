<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
    ];

    protected $dates = ['deleted_at'];


    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
