<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictPublicDemo
{
    private const READ_ONLY_ROUTES = [
        'dashboard',
        'clientes.index',
        'clientes.show',
        'mis-clientes',
        'mis-clientes.show',
        'reporte.index',
        'detalle.kpi',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled')) {
            abort_if($request->routeIs('demo.start'), 404);

            return $next($request);
        }

        abort_unless(config('database.default') === 'public_demo', 503);

        if ($request->isMethod('POST') && $request->routeIs('demo.start', 'logout')) {
            return $next($request);
        }

        abort_unless($request->isMethod('GET') || $request->isMethod('HEAD'), 403);

        if ($request->path() === '/' || $request->routeIs('login')) {
            return $next($request);
        }

        abort_unless($request->routeIs(...self::READ_ONLY_ROUTES), 403);

        return $request->user() ? $next($request) : redirect()->route('login');
    }
}
