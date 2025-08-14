<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class InstallController extends Controller
{
    public function showInstall(Request $request)
    {
        if ($request->session()->get('db_configured')) {
            if ($request->session()->get('admin_registered')) {
                return view('complete');
            }
            return view('admin');
        }
        return view('welcome');
    }

    public function showDatabaseForm() {

        Log::info('Under database configuration process.');
        return view('database');
    }

    public function showAdminForm() {

        Log::info('Under admin configuration process.');
        return view('admin');
    }

    // public function configureDatabase(Request $request)
    // {
    //     Log::info('Starting database configuration process.');

    //     $validated = $request->validate([
    //         'db_host' => 'required',
    //         'db_name' => 'required',
    //         'db_user' => 'required',
    //         'db_pass' => 'nullable',
    //     ]);

    //     Log::info('Validation passed.', $validated);

    //     // Write to .env
    //     $envPath = base_path('.env');
    //     Log::info('ENV path resolved: ' . $envPath);

    //     try {
    //         file_put_contents($envPath, preg_replace('/DB_HOST=.*/', 'DB_HOST=' . $validated['db_host'], file_get_contents($envPath)));
    //         Log::info('DB_HOST updated in .env: ' . $validated['db_host']);

    //         file_put_contents($envPath, preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . $validated['db_name'], file_get_contents($envPath)));
    //         Log::info('DB_DATABASE updated in .env: ' . $validated['db_name']);

    //         file_put_contents($envPath, preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=' . $validated['db_user'], file_get_contents($envPath)));
    //         Log::info('DB_USERNAME updated in .env: ' . $validated['db_user']);

    //         file_put_contents($envPath, preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . $validated['db_pass'], file_get_contents($envPath)));
    //         Log::info('DB_PASSWORD updated in .env: ' . $validated['db_pass']);

    //         // Update config
    //         config([
    //             'database.connections.mysql.host' => $validated['db_host'],
    //             'database.connections.mysql.database' => $validated['db_name'],
    //             'database.connections.mysql.username' => $validated['db_user'],
    //             'database.connections.mysql.password' => $validated['db_pass'],
    //         ]);

    //         Log::info('Database configuration updated in runtime config.');

    //         // Try to connect
    //         DB::connection()->getPdo();
    //         Log::info('Database connection successful.');

    //         // Migrate
    //         Artisan::call('migrate:fresh');
    //         Log::info('Database migration completed.');

    //         $request->session()->put('db_configured', true);
    //         Log::info('db_configured flag set in session.');

    //         return redirect()->route('install.admin');
    //     } catch (\Exception $e) {
    //         Log::error('Database configuration failed: ' . $e->getMessage());
    //         return back()->withErrors(['db' => 'Database connection failed: ' . $e->getMessage()]);
    //     }
    // }

   public function configureDatabase(Request $request)
    {
        Log::info('--- Installation process started ---');

        // 1. Validate DB credentials
        Log::info('Validating request data', $request->all());
        $validated = $request->validate([
            'db_host' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
            'db_pass' => 'nullable',
        ]);
        Log::info('Validation successful', $validated);

        // 2. Use file session driver during installation
        config(['session.driver' => 'file']);
        Log::info('Session driver set to file temporarily');

        // 3. Create dynamic DB connection
        Config::set('database.connections.dynamic_install', [
            'driver'    => 'mysql',
            'host'      => $validated['db_host'],
            'database'  => $validated['db_name'],
            'username'  => $validated['db_user'],
            'password'  => $validated['db_pass'],
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null,
        ]);
        DB::purge('dynamic_install');
        Log::info('Dynamic database connection configured', [
            'host' => $validated['db_host'],
            'database' => $validated['db_name'],
            'username' => $validated['db_user']
        ]);

        // 4. Test DB connection
        try {
            DB::connection('dynamic_install')->getPdo();
            Log::info('Database connection successful');
        } catch (\Exception $e) {
            Log::error('Database connection failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['db' => 'Database connection failed: '.$e->getMessage()]);
        }

        // 5. Ensure session migration exists
        $migrationFile = collect(glob(database_path('migrations/*.php')))
            ->first(fn($file) => str_contains(file_get_contents($file), 'Schema::create(\'sessions\''));
        if (!$migrationFile) {
            Artisan::call('session:table');
            Log::info('Session migration created');
        } else {
            Log::info('Session migration already exists');
        }

        // 6. Run migrations on blank DB
        try {
            Artisan::call('migrate:fresh', [
                '--database' => 'dynamic_install',
                '--force' => true,
            ]);
            Log::info('Migrations executed successfully', ['output' => Artisan::output()]);
        } catch (\Exception $e) {
            Log::error('Migration failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['db' => 'Migration failed: '.$e->getMessage()]);
        }

        // 7. Store DB credentials in session
        $request->session()->put('db_host', $validated['db_host']);
        $request->session()->put('db_name', $validated['db_name']);
        $request->session()->put('db_user', $validated['db_user']);
        $request->session()->put('db_pass', $validated['db_pass']);
        Log::info('Database credentials stored in session');

        // 8. Update .env file with DB credentials and session driver
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $envContent = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=mysql', $envContent);
        $envContent = preg_replace('/DB_HOST=.*/', 'DB_HOST='.$validated['db_host'], $envContent);
        $envContent = preg_replace('/DB_PORT=.*/', 'DB_PORT=3306', $envContent);
        $envContent = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE='.$validated['db_name'], $envContent);
        $envContent = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME='.$validated['db_user'], $envContent);
        $envContent = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD='.$validated['db_pass'], $envContent);
        $envContent = preg_replace('/SESSION_DRIVER=.*/', 'SESSION_DRIVER=database', $envContent);

        file_put_contents($envPath, $envContent);
        Log::info('.env file updated with database credentials and session driver');

        // 9. Set DB configured flag
        $request->session()->put('db_configured', true);
        Log::info('Database configuration flag set in session');

        Log::info('--- Installation process completed ---');

        return redirect()->route('install.admin');
    }


    public function registerAdmin(Request $request)
    {
        Log::info('registerAdmin: Starting admin registration process.');

        // 1) Validation
        $validated = $request->validate([
            'username' => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        Log::info('registerAdmin: Validation successful.', $validated);

        // 2) Create admin
        $admin = User::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => 1,
        ]);
        Log::info('registerAdmin: Admin user created.', ['admin_id' => $admin->id]);

        // 3) Auto-login (web guard)
        Auth::login($admin);
        Log::info('registerAdmin: Admin logged in.', ['admin_id' => $admin->id]);

        $request->session()->regenerate(); // prevent session fixation
        Log::info('registerAdmin: Session regenerated.');

        // 4) Flags + safe .env updates
        $request->session()->put('admin_registered', true);
        Log::info('registerAdmin: Session flag "admin_registered" set.');

        $this->updateEnvVariable('DB_HOST',     $request->session()->get('db_host'));
        $this->updateEnvVariable('DB_DATABASE', $request->session()->get('db_name'));
        $this->updateEnvVariable('DB_USERNAME', $request->session()->get('db_user'));
        $this->updateEnvVariable('DB_PASSWORD', $request->session()->get('db_pass'));
        $this->updateEnvVariable('SESSION_DRIVER', 'database');
        Log::info('registerAdmin: Environment variables updated.', [
            'DB_HOST'     => $request->session()->get('db_host'),
            'DB_DATABASE' => $request->session()->get('db_name'),
            'DB_USERNAME' => $request->session()->get('db_user'),
            'DB_PASSWORD' => str_repeat('*', strlen($request->session()->get('db_pass'))), // mask password
            'SESSION_DRIVER' => 'database',
        ]);

        // 5) Lock installer
        File::put(storage_path('installed'), 'installed');
        Log::info('registerAdmin: Installer locked.');

        // 6) Make sure the app picks up new env on next request
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Log::info('registerAdmin: Config and cache cleared.');

        // 7) Redirect to dashboard
        Log::info('registerAdmin: Redirecting to dashboard.');
        return redirect()
            ->route('admin.dashboard')
            ->with('message', 'Setup complete! Welcome, you are now logged in as Admin.');
    }


    protected function updateEnvVariable($key, $value)
    {
        if ($value !== null && $value !== '') {
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            // Replace or append
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= PHP_EOL."{$key}={$value}";
            }
            file_put_contents($envPath, $envContent);
        }
    }
}
