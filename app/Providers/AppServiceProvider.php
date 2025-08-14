<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot()
    {
        // Only force 'file' session driver if not installed
        if (!file_exists(storage_path('installed'))) {
            config(['session.driver' => 'file']);
            // Optional: if env is database, force back to file (only needed in rare cases)
            if (env('SESSION_DRIVER') !== 'file') {
                $envPath = base_path('.env');
                file_put_contents($envPath, preg_replace(
                    '/SESSION_DRIVER=.*/',
                    'SESSION_DRIVER=file',
                    file_get_contents($envPath)
                ));
            }
        }
    }
}
