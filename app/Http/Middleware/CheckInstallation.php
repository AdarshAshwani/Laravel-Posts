<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CheckInstallation
{
    public function handle(Request $request, Closure $next)
    {
        $lockFile = storage_path('installed');
        if (! file_exists($lockFile)) {
            if (! $request->is('install*')) {
                return redirect()->route('install');
            }
            return $next($request);
        }

        try {
            if (! Schema::hasTable('users')) {
                if (! $request->is('install*')) {
                    return redirect()->route('install');
                }
            }
        } catch (\Throwable $e) {
            if (! $request->is('install*')) {
                return redirect()->route('install');
            }
        }

        return $next($request);
    }
}
