<?php

namespace Tests\Unit;

use App\Services\Updater\UpdateService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UpdateServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('framework/testing/upd-src'));
        File::deleteDirectory(storage_path('framework/testing/upd-dst'));

        parent::tearDown();
    }

    public function test_current_version_reads_version_file(): void
    {
        $service = new UpdateService();

        $this->assertSame('1.0.0', $service->currentVersion());
    }

    public function test_compare_versions(): void
    {
        $service = new UpdateService();

        $this->assertGreaterThan(0, $service->compareVersions('1.0.1', '1.0.0'));
        $this->assertSame(0, $service->compareVersions('1.0.0', 'v1.0.0'));
        $this->assertLessThan(0, $service->compareVersions('1.0.0', '1.1.0'));
    }

    public function test_synchronize_copies_updates_and_preserves_protected(): void
    {
        $src = storage_path('framework/testing/upd-src');
        $dst = storage_path('framework/testing/upd-dst');

        File::makeDirectory($src.'/app', 0755, true);
        File::makeDirectory($src.'/storage', 0755, true);
        File::makeDirectory($src.'/public/uploads', 0755, true);
        File::put($src.'/index.php', 'new index');
        File::put($src.'/app/X.php', 'new x');
        File::put($src.'/storage/log.txt', 'new log');
        File::put($src.'/.env', 'NEW ENV');
        File::put($src.'/.htaccess', 'new htaccess');
        File::put($src.'/public/uploads/img.png', 'img');

        File::makeDirectory($dst.'/app', 0755, true);
        File::makeDirectory($dst.'/storage', 0755, true);
        File::makeDirectory($dst.'/public/uploads', 0755, true);
        File::put($dst.'/index.php', 'old index');
        File::put($dst.'/app/X.php', 'old x');
        File::put($dst.'/app/OLD.php', 'old file to prune');
        File::put($dst.'/storage/log.txt', 'KEEP ME');
        File::put($dst.'/.env', 'KEEP ENV');
        File::put($dst.'/public/uploads/img.png', 'KEEP IMG');
        File::put($dst.'/legacy.php', 'old legacy');

        $service = new UpdateService();
        $service->synchronizeDirectories($src, $dst);

        $this->assertSame('new index', File::get($dst.'/index.php'));
        $this->assertSame('new x', File::get($dst.'/app/X.php'));
        $this->assertSame('new htaccess', File::get($dst.'/.htaccess'));
        $this->assertFalse(File::exists($dst.'/app/OLD.php'));
        $this->assertFalse(File::exists($dst.'/legacy.php'));
        $this->assertSame('KEEP ME', File::get($dst.'/storage/log.txt'));
        $this->assertSame('KEEP ENV', File::get($dst.'/.env'));
        $this->assertSame('KEEP IMG', File::get($dst.'/public/uploads/img.png'));
    }
}