<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isAdmin == 1) {
            return $next($request);
        }
        return redirect()->route('home')->with('error', 'Access denied. You are not an admin.');
        // Debug thông tin về request
    dd($request->all());

    // Hoặc debug thông tin về user đang đăng nhập
    dd(auth()->user());

    // Logic kiểm tra quyền admin

    return $next($request);
    }
}
