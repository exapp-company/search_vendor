<?php

namespace App\Http\Middleware;

use App\Enums\HttpStatus;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminMiddleware
{
    use ApiResponse;

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            return $this->error('Вы не являетесь администратором', HttpStatus::forbidden);
        }
        return $next($request);
    }
}
