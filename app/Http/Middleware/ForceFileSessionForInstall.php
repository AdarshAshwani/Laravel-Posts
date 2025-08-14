<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class ForceFileSessionForInstall
{
    public function handle($request, Closure $next)
    {
        Config::set('session.driver', 'file');
        return $next($request);
    }
}

