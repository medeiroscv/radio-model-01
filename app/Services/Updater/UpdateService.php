<?php

namespace App\Services\Updater;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use ZipArchive;

class UpdateService
{
    protected const GITHUB_API = 'https://api.github.com';

    /**
     * Caminhos que nunca sao tocados durante a atualizacao.
     */
    protected const PROTECTED_TOP_LEVEL = [
        '.env',
        '.installed',
        'storage',
        'node_modules',
        '.git',
        'public/uploads',
        'public/storage',
    ];

    public function isConfigured(): bool
    {
        return filled($this->repo());
    }

    public function repo(): ?string
    {
        $repo = config('updater.repo');

        return filled($repo) ? trim($repo, " \t\n\r\0\x0B/") : null;
    }

    public function currentVersion(): string
    {
        $file = base_path('VERSION');

        if (File::exists($file)) {
            $version = trim((string) File::get($file));

            if ($version !== '') {
                return $version;
            }
        }

        return '1.0.0';
    }

    public function latestRelease(): ?array
    {
        $repo = $this->repo();

        if (! $repo) {
            return null;
        }

        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->get(self::GITHUB_API.'/repos/'.$repo.'/releases/latest');

        if ($response->successful()) {
            $data = $response->json();

            return [
                'version' => ltrim((string) ($data['tag_name'] ?? ''), 'v'),
                'tag' => (string) ($data['tag_name'] ?? ''),
                'published_at' => $data['published_at'] ?? null,
                'html_url' => $data['html_url'] ?? null,
                'body' => (string) ($data['body'] ?? ''),
                'source' => 'release',
            ];
        }

        // Sem releases: usa o branch padrao como fonte de atualizacao.
        $response = Http::withHeaders($this->headers())
            ->timeout(30)
            ->get(self::GITHUB_API.'/repos/'.$repo);

        if ($response->successful()) {
            $data = $response->json();
            $branch = (string) ($data['default_branch'] ?? 'main');

            return [
                'version' => $branch,
                'tag' => $branch,
                'published_at' => null,
                'html_url' => $data['html_url'] ?? null,
                'body' => '',
                'source' => 'branch',
            ];
        }

        return null;
    }

    public function hasUpdate(?array $latest = null): bool
    {
        $latest = $latest ?? $this->latestRelease();

        if (! $latest) {
            return false;
        }

        if (($latest['source'] ?? '') === 'branch') {
            return true;
        }

        return $this->compareVersions($latest['version'], $this->currentVersion()) > 0;
    }

    public function compareVersions(string $a, string $b): int
    {
        $a = ltrim(trim($a), 'v');
        $b = ltrim(trim($b), 'v');

        return version_compare($a, $b);
    }

    public function update(): array
    {
        $repo = $this->repo();

        if (! $repo) {
            throw new RuntimeException('Repositório de atualização não configurado (UPDATE_REPO).');
        }

        $lock = $this->lockPath();

        if (File::exists($lock)) {
            throw new RuntimeException('Já existe uma atualização em andamento. Aguarde e tente novamente.');
        }

        @set_time_limit(0);

        File::ensureDirectoryExists($this->updateDir());
        File::put($lock, now()->toDateTimeString());

        try {
            $latest = $this->latestRelease();

            if (! $latest) {
                throw new RuntimeException('Não foi possível consultar o GitHub. Verifique o UPDATE_REPO.');
            }

            $zipPath = $this->updateDir().'/update-'.time().'.zip';

            $this->download($repo, $latest['tag'], $zipPath);

            $extractDir = $this->updateDir().'/extract-'.time();
            File::ensureDirectoryExists($extractDir);

            $root = $this->extract($zipPath, $extractDir);

            $this->applyFiles($root);

            File::deleteDirectory($extractDir);
            File::delete($zipPath);

            $exitCode = Artisan::call('migrate', ['--force' => true]);

            if ($exitCode !== 0) {
                throw new RuntimeException('Falha ao rodar migrations: '.trim(Artisan::output()));
            }

            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }

            if ($latest['source'] === 'release') {
                File::put(base_path('VERSION'), $latest['version']);
            }

            return $latest;
        } finally {
            File::delete($lock);
        }
    }

    public function synchronizeDirectories(string $src, string $dst, array $protected = self::PROTECTED_TOP_LEVEL): void
    {
        $this->copyTree($src, $dst, '', $protected);
        $this->prune($dst, $src, '', $protected);
    }

    protected function copyTree(string $src, string $dst, string $rel, array $protected): void
    {
        foreach ($this->children($src) as $name) {
            $childRel = $rel === '' ? $name : $rel.'/'.$name;

            if ($this->isProtected($childRel, $protected)) {
                continue;
            }

            $from = $src.'/'.$name;
            $to = $dst.'/'.$name;

            if (is_dir($from)) {
                if (is_file($to)) {
                    @unlink($to);
                }

                if (! is_dir($to)) {
                    File::makeDirectory($to, 0755, true);
                }

                $this->copyTree($from, $to, $childRel, $protected);
            } elseif (is_file($from)) {
                @copy($from, $to);
            }
        }
    }

    protected function prune(string $dst, string $src, string $rel, array $protected): void
    {
        foreach ($this->children($dst) as $name) {
            $childRel = $rel === '' ? $name : $rel.'/'.$name;

            if ($this->isProtected($childRel, $protected)) {
                continue;
            }

            $from = $src.'/'.$name;
            $to = $dst.'/'.$name;

            if (is_dir($to)) {
                if (! is_dir($from)) {
                    File::deleteDirectory($to);
                } else {
                    $this->prune($to, $from, $childRel, $protected);
                }
            } elseif (is_file($to) && ! is_file($from)) {
                @unlink($to);
            }
        }
    }

    protected function isProtected(string $rel, array $protected): bool
    {
        foreach ($protected as $path) {
            if ($rel === $path || str_starts_with($rel, $path.'/')) {
                return true;
            }
        }

        return false;
    }

    protected function children(string $dir): array
    {
        $items = @scandir($dir);

        if ($items === false) {
            return [];
        }

        return array_values(array_filter($items, fn (string $item): bool => $item !== '.' && $item !== '..'));
    }

    protected function download(string $repo, string $ref, string $path): void
    {
        File::ensureDirectoryExists(dirname($path));

        $url = self::GITHUB_API.'/repos/'.$repo.'/zipball/'.rawurlencode($ref);

        $response = Http::withHeaders($this->headers())
            ->timeout(600)
            ->sink($path)
            ->get($url);

        if ($response->failed() || ! file_exists($path) || filesize($path) === 0) {
            @unlink($path);

            throw new RuntimeException('Falha ao baixar a atualização do GitHub (HTTP '.$response->status().').');
        }
    }

    protected function extract(string $zipPath, string $extractDir): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Extensão PHP "zip" não está disponível.');
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('Não foi possível abrir o arquivo baixado.');
        }

        $zip->extractTo($extractDir);
        $count = $zip->numFiles;
        $zip->close();

        $root = null;

        foreach ($this->children($extractDir) as $name) {
            if (is_dir($extractDir.'/'.$name)) {
                $root = $extractDir.'/'.$name;
                break;
            }
        }

        if (! $root) {
            throw new RuntimeException('Arquivo de atualização inválido.');
        }

        if ($count === 0) {
            throw new RuntimeException('Arquivo de atualização vazio.');
        }

        return $root;
    }

    protected function applyFiles(string $sourceRoot): void
    {
        $this->synchronizeDirectories($sourceRoot, base_path());
    }

    protected function updateDir(): string
    {
        return storage_path('app/updates');
    }

    protected function lockPath(): string
    {
        return $this->updateDir().'/.lock';
    }

    protected function headers(): array
    {
        $headers = ['User-Agent' => 'RadioCMS-Updater/'.$this->currentVersion()];

        $token = config('updater.token');

        if (filled($token)) {
            $headers['Authorization'] = 'token '.$token;
        }

        return $headers;
    }
}