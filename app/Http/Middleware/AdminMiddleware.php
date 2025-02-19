<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra nếu người dùng là admin
        if (auth()->user() && auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403); // Trả về lỗi nếu không phải admin
        }

        return $next($request);
    }
}
