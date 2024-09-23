<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dialog extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'user_1',
        'user_2',
        'title',
        'description',
    ];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_1');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_2');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
