<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    public $timestamps = false;
    //protected $touches = ['product'];
    protected function casts(): array
    {
        return [
            'in_stock' => 'boolean',
        ];
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
    public function hashCode()
    {
        return "$this->office_id |$| $this->product_id |$| $this->amount |$| $this->in_stock";
    }
}
