<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;


    protected $fillable = [
        'shop_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }


    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function dialogs(): HasMany
    {
        return $this->hasMany(Dialog::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
