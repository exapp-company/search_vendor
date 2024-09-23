<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kblais\QueryFilter\Filterable;

class Office extends Model
{
    use HasFactory, Filterable, HasFiles;

    public $timestamps = false;

    public $fillable = [
        'name',
        'address',
        'phone',
        'feed_url',
        'feed_type',
        'email',
        'lat',
        'lon',
        'feed_mapping',
        'use_parent_feed',
        'use_parent_mapping',
        'city_id',
        'shop_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'use_parent_feed' => 'boolean',
            'use_parent_mapping' => 'boolean',
            'feed_mapping' => 'array',
        ];
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function locations()
    {
        return $this->belongsToMany(Location::class);
    }
    public function products()
    {
        return $this->hasManyThrough(Product::class, Stock::class);
    }
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
    public function supplier(): Attribute
    {
        //dd($this->shop);
        return Attribute::make(get: fn() => $this->shop?->supplier);
    }
}
