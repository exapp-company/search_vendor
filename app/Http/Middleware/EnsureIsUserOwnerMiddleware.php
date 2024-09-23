<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsUserOwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //dd($request->user());
        $model = $request->route('office') ?? $request->route('shop');
        if (is_null($model) || $request->user()->isAdmin()) {
            return $next($request);
        }
        $supplier = $model->supplier;
        if (!is_null($supplier) && $supplier->id == $request->user()->id) {
            return $next($request);
        }
        return response()->json(['message' => "Нет доступа"], 403);
    }
}
