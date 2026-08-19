<?php

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InstallerController extends Controller
{
    public function index()
    {
        if ($this->alreadyInstalled()) {
            return redirect()->route('home');
        }

        return inertia('Installer/Index', [
            'requirements' => $this->checkRequirements(),
            'permissions' => $this->checkPermissions(),
            'step' => 1,
        ]);
    }

    public function requirements()
    {
        return $this->index();
    }

    public function checkDatabase(Request $request)
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|numeric',
            'database' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
        ]);

        $config = [
            'driver' => 'mysql',
            'host' => $request->host,
            'port' => (int) $request->port,
            'database' => $request->database,
            'username' => $request->username,
            'password' => $request->password ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ];

        try {
            Config::set('database.connections.installer', $config);
            DB::purge('installer');
            DB::connection('installer')->getPdo();

            return response()->json([
                'success' => true,
                'message' => 'Conexão com o banco de dados realizada com sucesso!',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível conectar ao banco de dados: '.$e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        if ($this->alreadyInstalled()) {
            return redirect()->route('home');
        }

        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',

            'station_name' => 'required|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'timezone' => 'required|string|max:255',
            'email' => 'required|email',

            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            /*
             * IMPORTANTE:
             * durante a instalação, sessão/cache/fila não podem depender
             * de tabelas que ainda não existem.
             */
            $this->writeEnvFile([
                'APP_NAME' => '"'.str_replace('"', '', $request->station_name).'"',
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'APP_URL' => url('/'),
                'APP_LOCALE' => 'pt_BR',

                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $request->db_host,
                'DB_PORT' => $request->db_port,
                'DB_DATABASE' => $request->db_database,
                'DB_USERNAME' => $request->db_username,
                'DB_PASSWORD' => '"'.($request->db_password ?? '').'"',

                'SESSION_DRIVER' => 'file',
                'CACHE_STORE' => 'file',
                'QUEUE_CONNECTION' => 'sync',
            ]);

            $this->setDatabaseConnection($request);

            config()->set('session.driver', 'file');
            config()->set('cache.default', 'file');
            config()->set('queue.default', 'sync');

            if (empty(config('app.key'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            /*
             * In-process: não usa Symfony Process, proc_open, shell_exec,
             * exec ou qualquer comando externo.
             */
            $this->runArtisanCommand('migrate', ['--force' => true]);

            $seeders = [
                'Database\\Seeders\\RolesAndPermissionsSeeder',
                'Database\\Seeders\\SettingsSeeder',
                'Database\\Seeders\\MenusSeeder',
                'Database\\Seeders\\NewsCategoriesSeeder',
            ];

            foreach ($seeders as $seeder) {
                $this->runArtisanCommand('db:seed', [
                    '--class' => $seeder,
                    '--force' => true,
                ]);
            }

            Station::create([
                'name' => $request->station_name,
                'frequency' => $request->frequency,
                'slogan' => $request->slogan,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country ?: 'Brasil',
                'timezone' => $request->timezone,
                'email' => $request->email,
                'is_installed' => true,
            ]);

            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'is_active' => true,
            ]);

            $admin->assignRole(Role::findByName('super-admin'));

            /*
             * Só agora as tabelas necessárias existem.
             */
            $this->writeEnvFile([
                'SESSION_DRIVER' => 'database',
                'CACHE_STORE' => 'database',
                'QUEUE_CONNECTION' => 'database',
            ]);

            $this->markInstalled();

            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return redirect()->route('installer.complete');
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Erro durante a instalação: '.$e->getMessage()
            );
        }
    }

    public function complete()
    {
        if (! $this->alreadyInstalled()) {
            return redirect()->route('installer.index');
        }

        return inertia('Installer/Complete');
    }

    protected function alreadyInstalled(): bool
    {
        if (file_exists(base_path('.installed'))) {
            return true;
        }

        try {
            DB::connection()->getPdo();

            return Station::where('is_installed', true)->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    protected function checkRequirements(): array
    {
        $checks = [
            'PHP Version >= 8.3' => version_compare(PHP_VERSION, '8.3.0', '>='),
            'PDO' => extension_loaded('pdo'),
            'PDO MySQL' => extension_loaded('pdo_mysql'),
            'OpenSSL' => extension_loaded('openssl'),
            'Mbstring' => extension_loaded('mbstring'),
            'Tokenizer' => extension_loaded('tokenizer'),
            'XML' => extension_loaded('xml'),
            'Ctype' => extension_loaded('ctype'),
            'JSON' => extension_loaded('json'),
            'Fileinfo' => extension_loaded('fileinfo'),
            'BCMath' => extension_loaded('bcmath'),
            'GD ou Imagick' => extension_loaded('gd') || extension_loaded('imagick'),
            'cURL' => extension_loaded('curl'),
            'Zip' => extension_loaded('zip'),
        ];

        $results = [];

        foreach ($checks as $name => $passed) {
            $results[] = [
                'name' => $name,
                'passed' => $passed,
                'current' => $this->getRequirementValue($name),
            ];
        }

        return $results;
    }

    protected function getRequirementValue(string $name): string
    {
        return match ($name) {
            'PHP Version >= 8.3' => PHP_VERSION,
            'PDO' => extension_loaded('pdo') ? 'carregado' : 'ausente',
            'PDO MySQL' => extension_loaded('pdo_mysql') ? 'carregado' : 'ausente',
            'OpenSSL' => extension_loaded('openssl') ? 'carregado' : 'ausente',
            'Mbstring' => extension_loaded('mbstring') ? 'carregado' : 'ausente',
            'Tokenizer' => extension_loaded('tokenizer') ? 'carregado' : 'ausente',
            'XML' => extension_loaded('xml') ? 'carregado' : 'ausente',
            'Ctype' => extension_loaded('ctype') ? 'carregado' : 'ausente',
            'JSON' => extension_loaded('json') ? 'carregado' : 'ausente',
            'Fileinfo' => extension_loaded('fileinfo') ? 'carregado' : 'ausente',
            'BCMath' => extension_loaded('bcmath') ? 'carregado' : 'ausente',
            'GD ou Imagick' => extension_loaded('gd')
                ? 'carregado'
                : (extension_loaded('imagick') ? 'carregado' : 'ausente'),
            'cURL' => extension_loaded('curl') ? 'carregado' : 'ausente',
            'Zip' => extension_loaded('zip') ? 'carregado' : 'ausente',
            default => '',
        };
    }

    protected function checkPermissions(): array
    {
        $paths = [
            'storage' => storage_path(),
            'bootstrap/cache' => base_path('bootstrap/cache'),
            'public/uploads' => public_path('uploads'),
            'public/storage' => public_path('storage'),
        ];

        $results = [];

        foreach ($paths as $name => $path) {
            if (! File::exists($path)) {
                File::makeDirectory($path, 0755, true, true);
            }

            $results[] = [
                'name' => $name,
                'path' => $path,
                'writable' => is_writable($path),
            ];
        }

        return $results;
    }

    protected function writeEnvFile(array $values): void
    {
        $envPath = base_path('.env');

        $content = File::exists($envPath)
            ? File::get($envPath)
            : File::get(base_path('.env.example'));

        foreach ($values as $key => $value) {
            $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

            if (preg_match($pattern, $content)) {
                $content = preg_replace(
                    $pattern,
                    "{$key}={$value}",
                    $content
                );
            } else {
                $content .= PHP_EOL."{$key}={$value}";
            }
        }

        File::put($envPath, $content);
    }

    protected function setDatabaseConnection(Request $request): void
    {
        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql.host', $request->db_host);
        config()->set('database.connections.mysql.port', (int) $request->db_port);
        config()->set('database.connections.mysql.database', $request->db_database);
        config()->set('database.connections.mysql.username', $request->db_username);
        config()->set('database.connections.mysql.password', $request->db_password ?? '');

        DB::purge('mysql');
        DB::reconnect('mysql')->getPdo();
    }

    protected function runArtisanCommand(
        string $command,
        array $parameters = []
    ): void {
        $exitCode = Artisan::call($command, $parameters);

        if ($exitCode !== 0) {
            throw new \RuntimeException(
                'Falha ao executar "artisan '.$command.'": '.
                trim(Artisan::output())
            );
        }
    }

    protected function markInstalled(): void
    {
        File::put(
            base_path('.installed'),
            now()->toDateTimeString()
        );
    }
}
