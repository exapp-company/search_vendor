<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feed extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'url',
        'status_id',
        'parsed',
        'processing',
        'shop_id',
        'offers_count',
        'processed_offers_count',
    ];

    protected function casts(): array
    {
        return [
            'parsed' => 'boolean',
            'processing' => 'boolean',
        ];
    }


    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(FeedStatus::class, 'status_id');
    }
}
