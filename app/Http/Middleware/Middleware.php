<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\Middleware as BaseMiddleware;

class Middleware extends BaseMiddleware
{
    /**
     * The application's route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'installer.session' => \App\Http\Middleware\ForceFileSessionForInstall::class,
        'installer.lock' => \App\Http\Middleware\InstallerLock::class,
        'check.installation' => \App\Http\Middleware\CheckInstallation::class,
    ];
}
