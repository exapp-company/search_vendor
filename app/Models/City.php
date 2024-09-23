<?php

namespace App\Models;

use App\Traits\HasGeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{

    use HasFactory, SoftDeletes, HasGeo;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'country_id',
        'lat',
        'lon'
    ];

    protected $dates = ['deleted_at'];



    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }


    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'offices')
            ->where('shops.status', 'active')
            //->select("offices.shop_id")
            ->distinct("shops.id")
        ;
    }
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
    public function getGeoName()
    {
        return "{$this->country->name}, {$this->name}";
    }
}
