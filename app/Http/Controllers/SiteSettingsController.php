<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\Setting;

class SiteSettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings.branding', [
            'site_name'     => setting('site_name', config('app.name', 'Posts')),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:30'],
        ]);

        $name = trim($data['site_name']);

        // 1) Save wherever you already store it
        Setting::set('site_name', $name);

        // 2) Persist to .env -> APP_NAME
        $this->setEnvValue('APP_NAME', $name);

        // 3) Make it take effect immediately
        config(['app.name' => $name]);
        try {
            Artisan::call('config:clear');     // clear cached config
            // If you use config caching in prod, re-cache it:
            if (file_exists(base_path('bootstrap/cache/config.php'))) {
                Artisan::call('config:cache');
            }
        } catch (\Throwable $e) {
            // ignore on hosts that disallow these commands
        }

        return back()->with('message', 'Branding updated');
    }

    /**
     * Safely set or add a KEY="value" line in the .env file.
     */
    protected function setEnvValue(string $key, string $value): void
    {
        $envPath = base_path('.env');
        if (!is_file($envPath) || !is_writable($envPath)) {
            return; // optionally throw or log
        }

        $env = file_get_contents($envPath);
        $escaped = str_replace('"', '\"', $value); // keep quotes safe
        $line = $key . '="' . $escaped . '"';
        $pattern = '/^' . preg_quote($key, '/') . '=.*/m';

        if (preg_match($pattern, $env)) {
            $env = preg_replace($pattern, $line, $env);
        } else {
            $env = rtrim($env) . PHP_EOL . $line . PHP_EOL;
        }

        file_put_contents($envPath, $env);
    }
}
