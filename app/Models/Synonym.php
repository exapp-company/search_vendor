<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Synonym extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = ['title', 'synonyms'];

    protected function casts(): array
    {
        return [
            'synonyms' => 'array',
        ];
    }
}
