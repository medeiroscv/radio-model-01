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

    protected const PROTECTED_TOP_LEVEL = [
        '.env',
        '.installed',
        'storage',
        'node_modules',
        '.git',
        'public/uploads',
        'public/storage',
    ];

    protected const DEFERRED_RUNTIME_FILES = [
        'app/Services/Updater/UpdateService.php',
        'app/Http/Controllers/Admin/UpdateController.php',
        'routes/admin.php',
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

    public function currentCommit(): ?string
    {
        $file = base_path('.update_commit');

        if (! File::exists($file)) {
            return null;
        }

        $sha = trim((string) File::get($file));

        return $sha !== '' ? $sha : null;
    }

    public function latestRelease(): ?array
    {
        $repo = $this->repo();

        if (! $repo) {
            return null;
        }

        $response = Http::withHeaders($this->headers())
            ->connectTimeout(10)
            ->timeout(20)
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
                'commit' => null,
            ];
        }

        $response = Http::withHeaders($this->headers())
            ->connectTimeout(10)
            ->timeout(20)
            ->get(self::GITHUB_API.'/repos/'.$repo);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();
        $branch = (string) ($data['default_branch'] ?? 'main');

        $commitResponse = Http::withHeaders($this->headers())
            ->connectTimeout(10)
            ->timeout(20)
            ->get(self::GITHUB_API.'/repos/'.$repo.'/commits/'.rawurlencode($branch));

        if (! $commitResponse->successful()) {
            return null;
        }

        $commit = $commitResponse->json();
        $sha = (string) ($commit['sha'] ?? '');

        if ($sha === '') {
            return null;
        }

        return [
            'version' => $branch,
            'tag' => $branch,
            'published_at' => $commit['commit']['committer']['date'] ?? null,
            'html_url' => $data['html_url'] ?? null,
            'body' => '',
            'source' => 'branch',
            'commit' => $sha,
        ];
    }

    public function hasUpdate(?array $latest = null): bool
    {
        $latest = $latest ?? $this->latestRelease();

        if (! $latest) {
            return false;
        }

        if (($latest['source'] ?? '') === 'branch') {
            $remote = (string) ($latest['commit'] ?? '');

            return $remote !== '' && ! hash_equals($remote, (string) $this->currentCommit());
        }

        return version_compare(
            ltrim(trim($latest['version']), 'v'),
            ltrim(trim($this->currentVersion()), 'v')
        ) > 0;
    }

    public function status(): array
    {
        $state = $this->readState();

        if (! $state) {
            return [
                'status' => 'idle',
                'phase' => null,
                'processed' => 0,
                'total' => 0,
                'progress' => 0,
                'message' => 'Nenhuma atualização em andamento.',
            ];
        }

        return $this->publicState($state);
    }

    public function prepare(): array
    {
        $repo = $this->repo();

        if (! $repo) {
            throw new RuntimeException('Repositório de atualização não configurado (UPDATE_REPO).');
        }

        $this->releaseStaleLock();

        if (File::exists($this->lockPath())) {
            $state = $this->readState();

            if ($state && in_array($state['status'] ?? '', ['running', 'prepared'], true)) {
                return $this->publicState($state);
            }

            throw new RuntimeException('Já existe uma atualização em andamento.');
        }

        File::ensureDirectoryExists($this->updateDir());
        File::put($this->lockPath(), now()->toDateTimeString());

        try {
            $latest = $this->latestRelease();

            if (! $latest) {
                throw new RuntimeException('Não foi possível consultar o GitHub. Verifique o UPDATE_REPO.');
            }

            if (! $this->hasUpdate($latest)) {
                File::delete($this->lockPath());

                return [
                    'status' => 'complete',
                    'phase' => 'complete',
                    'processed' => 0,
                    'total' => 0,
                    'progress' => 100,
                    'version' => $latest['version'],
                    'message' => 'O sistema já está na versão mais recente.',
                ];
            }

            $id = date('YmdHis').'-'.bin2hex(random_bytes(4));
            $zipPath = $this->updateDir().'/'.$id.'.zip';
            $extractDir = $this->updateDir().'/'.$id;

            File::ensureDirectoryExists($extractDir);

            $this->download($repo, $latest['tag'], $zipPath);
            $root = $this->extract($zipPath, $extractDir);
            $manifest = $this->buildManifest($root);
            $manifestPath = $this->updateDir().'/'.$id.'.manifest.json';

            File::put(
                $manifestPath,
                json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)
            );

            $state = [
                'id' => $id,
                'status' => 'prepared',
                'phase' => 'copy',
                'message' => 'Pacote preparado. Aplicando arquivos...',
                'repo' => $repo,
                'latest' => $latest,
                'zip_path' => $zipPath,
                'extract_dir' => $extractDir,
                'source_root' => $root,
                'manifest_path' => $manifestPath,
                'offset' => 0,
                'processed' => 0,
                'total' => count($manifest),
                'started_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ];

            $this->writeState($state);

            return $this->publicState($state);
        } catch (\Throwable $e) {
            File::delete($this->lockPath());
            throw $e;
        }
    }

    public function step(int $batchSize = 0): array
    {
        $state = $this->requireState();

        if (($state['phase'] ?? '') !== 'copy') {
            return $this->publicState($state);
        }

        $batchSize = $batchSize > 0
            ? min($batchSize, 500)
            : (int) config('updater.batch_size', 180);

        $batchSize = max(25, min($batchSize, 500));

        $manifest = json_decode(
            (string) File::get($state['manifest_path']),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $total = count($manifest);
        $offset = (int) ($state['offset'] ?? 0);
        $end = min($offset + $batchSize, $total);

        $state['status'] = 'running';

        try {
            for ($i = $offset; $i < $end; $i++) {
                $this->copyOne($state['source_root'], $manifest[$i]);
            }

            $state['offset'] = $end;
            $state['processed'] = $end;
            $state['total'] = $total;
            $state['updated_at'] = now()->toIso8601String();

            if ($end >= $total) {
                $state['phase'] = 'finalize';
                $state['message'] = 'Arquivos aplicados. Finalizando banco e caches...';
            } else {
                $state['message'] = 'Aplicando arquivos...';
            }

            $this->writeState($state);

            return $this->publicState($state);
        } catch (\Throwable $e) {
            $state['status'] = 'error';
            $state['message'] = 'Falha ao aplicar arquivos: '.$e->getMessage();
            $state['updated_at'] = now()->toIso8601String();
            $this->writeState($state);

            throw $e;
        }
    }

    public function finalize(): array
    {
        $state = $this->requireState();

        if (($state['phase'] ?? '') === 'complete') {
            return $this->publicState($state);
        }

        if (($state['phase'] ?? '') !== 'finalize') {
            throw new RuntimeException('A atualização ainda não terminou de aplicar os arquivos.');
        }

        try {
            $this->copyDeferredFiles($state['source_root']);

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

            $latest = $state['latest'];

            if (($latest['source'] ?? '') === 'release') {
                File::put(base_path('VERSION'), (string) $latest['version']);
            } elseif (! empty($latest['commit'])) {
                File::put(base_path('.update_commit'), (string) $latest['commit']);
            }

            $state['status'] = 'complete';
            $state['phase'] = 'complete';
            $state['processed'] = $state['total'];
            $state['message'] = 'Atualização concluída com sucesso.';
            $state['updated_at'] = now()->toIso8601String();

            $this->writeState($state);
            $this->cleanupWorkingFiles($state);
            File::delete($this->lockPath());

            return $this->publicState($state);
        } catch (\Throwable $e) {
            $state['status'] = 'error';
            $state['message'] = 'Falha na finalização: '.$e->getMessage();
            $state['updated_at'] = now()->toIso8601String();
            $this->writeState($state);

            throw $e;
        }
    }

    public function resetFailed(): array
    {
        $state = $this->readState();

        if ($state) {
            $this->cleanupWorkingFiles($state);
        }

        File::delete($this->statePath());
        File::delete($this->lockPath());

        return $this->status();
    }

    protected function buildManifest(string $root): array
    {
        $files = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));

            if ($this->isProtected($rel, self::PROTECTED_TOP_LEVEL)) {
                continue;
            }

            if (in_array($rel, self::DEFERRED_RUNTIME_FILES, true)) {
                continue;
            }

            $files[] = $rel;
        }

        sort($files, SORT_STRING);

        return $files;
    }

    protected function copyOne(string $sourceRoot, string $rel): void
    {
        $from = $sourceRoot.'/'.$rel;
        $to = base_path($rel);

        if (! is_file($from)) {
            return;
        }

        File::ensureDirectoryExists(dirname($to), 0755, true);

        if (! @copy($from, $to)) {
            throw new RuntimeException('Não foi possível atualizar: '.$rel);
        }

        @chmod($to, 0644);
    }

    protected function copyDeferredFiles(string $sourceRoot): void
    {
        foreach (self::DEFERRED_RUNTIME_FILES as $rel) {
            if (is_file($sourceRoot.'/'.$rel)) {
                $this->copyOne($sourceRoot, $rel);
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

    protected function download(string $repo, string $ref, string $path): void
    {
        $url = self::GITHUB_API.'/repos/'.$repo.'/zipball/'.rawurlencode($ref);

        $response = Http::withHeaders($this->headers())
            ->connectTimeout(15)
            ->timeout(120)
            ->sink($path)
            ->get($url);

        if ($response->failed() || ! file_exists($path) || filesize($path) === 0) {
            @unlink($path);
            throw new RuntimeException(
                'Falha ao baixar a atualização do GitHub (HTTP '.$response->status().').'
            );
        }
    }

    protected function extract(string $zipPath, string $extractDir): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Extensão PHP "zip" não está disponível.');
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('Não foi possível abrir o pacote baixado.');
        }

        if (! $zip->extractTo($extractDir)) {
            $zip->close();
            throw new RuntimeException('Não foi possível extrair o pacote de atualização.');
        }

        $count = $zip->numFiles;
        $zip->close();

        if ($count === 0) {
            throw new RuntimeException('Pacote de atualização vazio.');
        }

        foreach (File::directories($extractDir) as $dir) {
            return $dir;
        }

        throw new RuntimeException('Pacote de atualização inválido.');
    }

    protected function publicState(array $state): array
    {
        $total = max(0, (int) ($state['total'] ?? 0));
        $processed = max(0, (int) ($state['processed'] ?? 0));
        $progress = $total > 0 ? (int) floor(($processed / $total) * 90) : 0;

        if (($state['phase'] ?? '') === 'finalize') {
            $progress = 95;
        }

        if (($state['phase'] ?? '') === 'complete') {
            $progress = 100;
        }

        return [
            'status' => $state['status'] ?? 'idle',
            'phase' => $state['phase'] ?? null,
            'processed' => $processed,
            'total' => $total,
            'progress' => $progress,
            'version' => $state['latest']['version'] ?? ($state['version'] ?? null),
            'message' => $state['message'] ?? '',
        ];
    }

    protected function requireState(): array
    {
        $state = $this->readState();

        if (! $state) {
            throw new RuntimeException('Nenhuma atualização preparada.');
        }

        return $state;
    }

    protected function readState(): ?array
    {
        if (! File::exists($this->statePath())) {
            return null;
        }

        try {
            return json_decode(
                (string) File::get($this->statePath()),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\Throwable) {
            return null;
        }
    }

    protected function writeState(array $state): void
    {
        File::put(
            $this->statePath(),
            json_encode(
                $state,
                JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
            )
        );
    }

    protected function cleanupWorkingFiles(array $state): void
    {
        if (! empty($state['extract_dir']) && is_dir($state['extract_dir'])) {
            File::deleteDirectory($state['extract_dir']);
        }

        foreach (['zip_path', 'manifest_path'] as $key) {
            if (! empty($state[$key])) {
                File::delete($state[$key]);
            }
        }
    }

    protected function releaseStaleLock(): void
    {
        if (! File::exists($this->lockPath())) {
            return;
        }

        $mtime = @filemtime($this->lockPath());

        if ($mtime && (time() - $mtime) > 1800) {
            $state = $this->readState();

            if ($state && ($state['status'] ?? '') === 'error') {
                $this->cleanupWorkingFiles($state);
            }

            File::delete($this->lockPath());
            File::delete($this->statePath());
        }
    }

    protected function updateDir(): string
    {
        return storage_path('app/updates');
    }

    protected function lockPath(): string
    {
        return $this->updateDir().'/.lock';
    }

    protected function statePath(): string
    {
        return $this->updateDir().'/state.json';
    }

    protected function headers(): array
    {
        $headers = [
            'User-Agent' => 'RadioCMS-Updater/'.$this->currentVersion(),
            'Accept' => 'application/vnd.github+json',
        ];

        $token = config('updater.token');

        if (filled($token)) {
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $headers;
    }
}
