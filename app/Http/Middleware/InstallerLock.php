<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallerLock
{
    public function handle(Request $request, Closure $next)
    {
        if (file_exists(storage_path('installed'))) {
            // You can choose to abort, or redirect to login/dashboard, or show a custom view.
            return redirect()->route('login')->with('message', 'Installer is already locked. Please login.');
            // Or: abort(403, 'Installer is locked.');
        }
        return $next($request);
    }
}
