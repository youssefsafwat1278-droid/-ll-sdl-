<?php

// app/Http/Middleware/UserMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        if (auth()->user()->role !== 'user') {
            return redirect('/admin/dashboard')->with('error', 'ليس لديك صلاحية الوصول');
        }

        return $next($request);
    }
}