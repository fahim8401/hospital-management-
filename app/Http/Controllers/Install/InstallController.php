<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class InstallController extends Controller
{
    // ──────────────────────────────────────────────
    // Step 1 – Welcome & requirements check
    // ──────────────────────────────────────────────
    public function requirements(): View
    {
        $requirements = $this->checkRequirements();
        $allPassed    = collect($requirements)->every(fn ($r) => $r['status']);

        return view('install.requirements', compact('requirements', 'allPassed'));
    }

    // ──────────────────────────────────────────────
    // Step 2 – Database configuration
    // ──────────────────────────────────────────────
    public function database(): View
    {
        return view('install.database');
    }

    public function saveDatabase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'db_connection' => ['required', 'in:mysql,pgsql,sqlite'],
            'db_host'       => ['required_unless:db_connection,sqlite', 'nullable', 'string'],
            'db_port'       => ['required_unless:db_connection,sqlite', 'nullable', 'integer'],
            'db_database'   => ['required', 'string'],
            'db_username'   => ['required_unless:db_connection,sqlite', 'nullable', 'string'],
            'db_password'   => ['nullable', 'string'],
        ]);

        // Test connection before saving
        try {
            $this->testDatabaseConnection($validated);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'db_connection' => 'Database connection failed: ' . $e->getMessage(),
            ]);
        }

        session(['install_db' => $validated]);

        return redirect()->route('install.app-config');
    }

    // ──────────────────────────────────────────────
    // Step 3 – Application configuration
    // ──────────────────────────────────────────────
    public function appConfig(): View
    {
        return view('install.app-config');
    }

    public function saveAppConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name'  => ['required', 'string', 'max:100'],
            'app_url'   => ['required', 'url', 'max:255'],
            'app_env'   => ['required', 'in:production,local'],
            'app_debug' => ['boolean'],
        ]);

        session(['install_app' => $validated]);

        return redirect()->route('install.migrate');
    }

    // ──────────────────────────────────────────────
    // Step 4 – Run migrations
    // ──────────────────────────────────────────────
    public function migrate(): View
    {
        return view('install.migrate');
    }

    public function runMigrations(): RedirectResponse
    {
        $dbConfig  = session('install_db');
        $appConfig = session('install_app');

        if (! $dbConfig || ! $appConfig) {
            return redirect()->route('install.requirements')
                ->withErrors(['error' => 'Session expired. Please restart the installation.']);
        }

        // Write .env and clear config cache (done inside writeEnv)
        $this->writeEnv($dbConfig, $appConfig);

        // Run migrations
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Migration failed: ' . $e->getMessage()]);
        }

        return redirect()->route('install.admin');
    }

    // ──────────────────────────────────────────────
    // Step 5 – Create admin user
    // ──────────────────────────────────────────────
    public function admin(): View
    {
        return view('install.admin');
    }

    public function saveAdmin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            DB::table('users')->insert([
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'password'   => Hash::make($validated['password']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'email' => 'Could not create admin: ' . $e->getMessage(),
            ]);
        }

        // Mark installation as complete
        file_put_contents(storage_path('installed'), date('Y-m-d H:i:s'));

        // Clear install session data
        session()->forget(['install_db', 'install_app']);

        return redirect()->route('install.finish');
    }

    // ──────────────────────────────────────────────
    // Step 6 – Finish
    // ──────────────────────────────────────────────
    public function finish(): View
    {
        return view('install.finish');
    }

    // ──────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────
    private function checkRequirements(): array
    {
        $phpExtensions = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'curl', 'fileinfo'];

        $requirements = [
            [
                'name'    => 'PHP >= 8.2',
                'status'  => version_compare(PHP_VERSION, '8.2.0', '>='),
                'current' => PHP_VERSION,
            ],
            [
                'name'    => 'Storage writable',
                'status'  => is_writable(storage_path()),
                'current' => is_writable(storage_path()) ? 'Writable' : 'Not writable',
            ],
            [
                'name'    => '.env writable',
                'status'  => is_writable(base_path('.env')) || (! file_exists(base_path('.env')) && is_writable(base_path())),
                'current' => (is_writable(base_path('.env')) || (! file_exists(base_path('.env')) && is_writable(base_path()))) ? 'Writable' : 'Not writable',
            ],
        ];

        foreach ($phpExtensions as $ext) {
            $requirements[] = [
                'name'    => "PHP ext: {$ext}",
                'status'  => extension_loaded($ext),
                'current' => extension_loaded($ext) ? 'Loaded' : 'Missing',
            ];
        }

        return $requirements;
    }

    private function testDatabaseConnection(array $config): void
    {
        if ($config['db_connection'] === 'sqlite') {
            $path = $config['db_database'] === ':memory:' ? ':memory:' : base_path($config['db_database']);
            $pdo  = new \PDO("sqlite:{$path}");
        } elseif ($config['db_connection'] === 'mysql') {
            $dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_database']}";
            $pdo = new \PDO($dsn, $config['db_username'], $config['db_password'] ?? '');
        } elseif ($config['db_connection'] === 'pgsql') {
            $dsn = "pgsql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_database']}";
            $pdo = new \PDO($dsn, $config['db_username'], $config['db_password'] ?? '');
        } else {
            return;
        }

        // Run a simple query to confirm the connection is fully operational
        $pdo->query('SELECT 1');
    }

    private function writeEnv(array $db, array $app): void
    {
        $template = file_get_contents(base_path('.env.example'));

        $appDebug = isset($app['app_debug']) && $app['app_debug'] ? 'true' : 'false';

        // Generate a fresh application key
        $appKey = 'base64:' . base64_encode(random_bytes(32));

        // Determine session/cache drivers based on DB type
        $sessionDriver = $db['db_connection'] !== 'sqlite' ? 'database' : 'file';
        $cacheStore    = $db['db_connection'] !== 'sqlite' ? 'database' : 'file';
        $queueConn     = $db['db_connection'] !== 'sqlite' ? 'database' : 'sync';

        // Escape a value for use in a .env file (quote if it contains special chars)
        $envEscape = function (string $value): string {
            if (preg_match('/[\s"\'\\\\#]/', $value) || $value === '') {
                return '"' . str_replace(['"', '\\'], ['\\"', '\\\\'], $value) . '"';
            }

            return $value;
        };

        $replacements = [
            '/^APP_NAME=.*/m'         => 'APP_NAME=' . $envEscape($app['app_name']),
            '/^APP_ENV=.*/m'          => 'APP_ENV=' . $app['app_env'],
            '/^APP_KEY=.*/m'          => 'APP_KEY=' . $appKey,
            '/^APP_DEBUG=.*/m'        => 'APP_DEBUG=' . $appDebug,
            '/^APP_URL=.*/m'          => 'APP_URL=' . $app['app_url'],
            '/^DB_CONNECTION=.*/m'    => 'DB_CONNECTION=' . $db['db_connection'],
            '/^SESSION_DRIVER=.*/m'   => 'SESSION_DRIVER=' . $sessionDriver,
            '/^CACHE_STORE=.*/m'      => 'CACHE_STORE=' . $cacheStore,
            '/^QUEUE_CONNECTION=.*/m' => 'QUEUE_CONNECTION=' . $queueConn,
        ];

        if ($db['db_connection'] !== 'sqlite') {
            // Handle both commented (# DB_HOST=) and uncommented (DB_HOST=) variants
            $replacements['/^#?\s*DB_HOST=.*/m']     = 'DB_HOST=' . ($db['db_host'] ?? '127.0.0.1');
            $replacements['/^#?\s*DB_PORT=.*/m']     = 'DB_PORT=' . ($db['db_port'] ?? 3306);
            $replacements['/^#?\s*DB_DATABASE=.*/m'] = 'DB_DATABASE=' . ($db['db_database'] ?? 'laravel');
            $replacements['/^#?\s*DB_USERNAME=.*/m'] = 'DB_USERNAME=' . ($db['db_username'] ?? 'root');
            $replacements['/^#?\s*DB_PASSWORD=.*/m'] = 'DB_PASSWORD=' . ($db['db_password'] ?? '');
        }

        $env = $template;
        foreach ($replacements as $pattern => $value) {
            $env = preg_replace($pattern, $value, $env);
        }

        file_put_contents(base_path('.env'), $env);

        // Clear config cache so the new .env values take effect on next request
        Artisan::call('config:clear');
    }
}
