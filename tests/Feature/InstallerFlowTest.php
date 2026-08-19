<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase as BaseTestCase;

class InstallerFlowTest extends BaseTestCase
{
    use DatabaseMigrations;

    public function test_complete_installation_flow(): void
    {
        @unlink(base_path('.installed'));

        $response = $this->post('/install', [
            'db_host' => '127.0.0.1',
            'db_port' => '3306',
            'db_database' => 'radio_cms_test',
            'db_username' => 'root',
            'db_password' => 'radio@2026',
            'station_name' => 'Rádio Teste PHPUnit',
            'frequency' => '104.9 FM',
            'slogan' => 'Sempre no ar',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'country' => 'Brasil',
            'timezone' => 'America/Sao_Paulo',
            'email' => 'radio@teste.com',
            'admin_name' => 'Admin Teste',
            'admin_email' => 'admin@teste.com',
            'admin_password' => 'admin12345',
            'admin_password_confirmation' => 'admin12345',
        ]);

        $response->assertRedirect(route('installer.complete'));
        $this->assertTrue(file_exists(base_path('.installed')));

        $station = \App\Models\Station::where('email', 'radio@teste.com')->first();
        $this->assertNotNull($station);
        $this->assertTrue((bool) $station->is_installed);

        $admin = \App\Models\User::where('email', 'admin@teste.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('super-admin'));

        $tables = DB::select('SELECT COUNT(*) AS t FROM information_schema.tables WHERE table_schema = ?', ['radio_cms_test']);
        $this->assertGreaterThan(20, (int) $tables[0]->t);

        // Redireciona para home quando já instalado
        $this->get('/install')->assertRedirect(route('home'));
    }
}