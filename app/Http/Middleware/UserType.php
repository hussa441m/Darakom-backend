<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $type): Response
    {
        if ($request->user() && $request->user()->type === $type) {
            return $next($request);
        }

        return apiError('غير مصرح لك بالوصول لهذا الإجراء', null, 403);
    }
}