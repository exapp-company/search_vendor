<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'type',
        'is_active',
    ];


    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
    public function imagable()
    {
        return $this->morphTo('imagable');
    }
}
