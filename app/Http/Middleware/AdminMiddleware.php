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
        \Log::info('AdminMiddleware triggered', [
            'user_id' => $request->user()?->id,
            'user_role' => $request->user()?->role,
            'path' => $request->path(),
        ]);

        if (!$request->user() || $request->user()->role !== 'admin') {
            \Log::warning('Admin middleware denied access', [
                'user_id' => $request->user()?->id,
                'user_role' => $request->user()?->role,
            ]);
            abort(403, 'Akses hanya untuk admin.');
        }

        return $next($request);
    }
}
