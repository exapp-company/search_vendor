<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $fillable = ['city_id', 'type', 'title', 'parent_id'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }
    // public function parents()
    // {
    //     return $this->parent()->with('parent');
    // }
    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }
    public function deep_chieldren()
    {
        return $this->children()->with('deep_chieldren');
    }
    public function offices()
    {
        return $this->belongsToMany(Office::class);
    }
}
