<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Station;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\SettingsSeeder::class);

        Station::create([
            'name' => 'Rádio Teste',
            'slug' => 'radio-teste',
            'slogan' => 'Sempre no ar',
            'is_installed' => true,
        ]);
    }

    public function test_home_page_returns_200(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_public_pages_return_200(): void
    {
        $this->get('/noticias')->assertOk();
        $this->get('/musicas')->assertOk();
        $this->get('/programacao')->assertOk();
        $this->get('/podcasts')->assertOk();
        $this->get('/rankings')->assertOk();
        $this->get('/promocoes')->assertOk();
        $this->get('/a-radio')->assertOk();
        $this->get('/contato')->assertOk();
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
    }

    public function test_news_show_page_returns_200(): void
    {
        $category = NewsCategory::first();
        $news = News::create([
            'news_category_id' => $category?->id,
            'user_id' => null,
            'title' => 'Notícia Pública',
            'slug' => 'noticia-publica',
            'content' => '<p>Texto</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->get('/noticias/'.$news->slug)->assertOk();
        $this->assertDatabaseHas('news', ['id' => $news->id, 'views' => 1]);
    }

    public function test_newsletter_subscribe(): void
    {
        $this->post('/newsletter', [
            'name' => 'João',
            'email' => 'joao@teste.com',
            'consent' => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('newsletters', ['email' => 'joao@teste.com', 'status' => 'subscribed']);
    }

    public function test_newsletter_requires_consent(): void
    {
        $this->post('/newsletter', [
            'email' => 'joao@teste.com',
        ])->assertSessionHasErrors(['consent']);
    }

    public function test_contact_store(): void
    {
        $this->post('/contato', [
            'name' => 'Maria',
            'email' => 'maria@teste.com',
            'message' => 'Olá rádio!',
        ])->assertRedirect();

        $this->assertDatabaseHas('contacts', ['email' => 'maria@teste.com']);
    }

    public function test_sitemap_contains_main_urls(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/noticias')
            ->assertSee('/podcasts')
            ->assertSee('/rankings');
    }
}