<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

class TestController extends Controller
{
    public function __invoke()
    {
        return view('chat');
    }
}
