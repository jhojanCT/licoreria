<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInitialSetupCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        if (AppSetting::initialSetupCompleted()) {
            return $next($request);
        }

        if (
            $request->routeIs('initial-setup.*')
            || $request->routeIs('logout')
            || $request->routeIs('products.index')
            || $request->routeIs('products.create')
            || $request->routeIs('products.store')
            || $request->routeIs('products.edit')
            || $request->routeIs('products.update')
        ) {
            return $next($request);
        }

        return redirect()->route('initial-setup.create');
    }
}

