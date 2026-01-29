<?php

// app/Http/Middleware/AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'ليس لديك صلاحية الوصول لهذه الصفحة');
        }

        return $next($request);
    }
}