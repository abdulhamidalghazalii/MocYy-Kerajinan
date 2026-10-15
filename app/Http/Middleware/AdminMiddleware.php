<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak. Tindakan ini hanya untuk administrator.'], 403);
            }
            abort(403, 'Akses Ditolak. Halaman ini khusus untuk Administrator Toko.');
        }

        return $next($request);
    }
}
