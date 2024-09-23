<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kblais\QueryFilter\Filterable;

class Shop extends Model
{
    use SoftDeletes, Filterable, HasFiles;

    protected $fillable = [
        'supplier_id',
        'city_id',
        'name',
        'description',
        'website',
        'logo_id',
        'phone',
        'email',
        'address',
        'status',
        'feed_mapping',
        'feed_url',
        'feed_type',
        'has_subdomain',
        'feed_item',
        "recommended"
    ];

    protected array $dates = ['deleted_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'feed_mapping' => 'array',
            'recommended' => 'boolean'
        ];
    }


    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }


    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }


    public function feeds(): HasMany
    {
        return $this->hasMany(Feed::class);
    }


    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(File::class, 'logo_id');
    }

    public function offices()
    {
        return $this->hasMany(Office::class);
    }

    public function stocks()
    {
        return $this->hasManyThrough(Stock::class, Product::class);
    }

    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class);
    }
}
