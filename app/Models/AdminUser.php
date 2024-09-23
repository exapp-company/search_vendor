<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use MoonShine\Models\MoonshineUser;

class AdminUser extends MoonshineUser
{
    use HasApiTokens;
    //public $table = "moonshine_users";
    public function isAdmin()
    {
        return true;
    }
}
