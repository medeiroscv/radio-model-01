<?php

namespace Tests\Feature;

use App\Models\Advertiser;
use App\Models\Artist;
use App\Models\Banner;
use App\Models\Chart;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Podcast;
use App\Models\Program;
use App\Models\Promotion;
use App\Models\Song;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\NewsCategoriesSeeder::class);

        $role = Role::findByName('super-admin');
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'email_verified_at' => now(),
        ]);
        $this->admin->assignRole($role);
        $this->actingAs($this->admin);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $this->get('/admin')->assertOk();
    }

    public function test_news_crud(): void
    {
        $category = NewsCategory::first();

        $this->post('/admin/news', [
            'title' => 'Notícia de teste',
            'summary' => 'Resumo',
            'content' => '<p>Conteúdo</p>',
            'news_category_id' => $category->id,
            'is_published' => true,
            'published_at' => now()->format('Y-m-d H:i'),
        ])->assertRedirect();

        $this->assertDatabaseHas('news', ['title' => 'Notícia de teste']);

        $news = News::where('title', 'Notícia de teste')->first();

        $this->put('/admin/news/'.$news->id, [
            'title' => 'Notícia editada',
            'summary' => 'Resumo',
            'content' => '<p>Conteúdo</p>',
            'news_category_id' => $category->id,
            'is_published' => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('news', ['title' => 'Notícia editada']);

        $this->delete('/admin/news/'.$news->id)->assertRedirect();
        $this->assertSoftDeleted('news', ['id' => $news->id]);
    }

    public function test_news_validation(): void
    {
        $this->post('/admin/news', [])->assertSessionHasErrors(['title']);
    }

    public function test_artist_crud(): void
    {
        $this->post('/admin/artists', ['name' => 'Artista Teste'])->assertRedirect();
        $this->assertDatabaseHas('artists', ['name' => 'Artista Teste']);

        $artist = Artist::where('name', 'Artista Teste')->first();
        $this->put('/admin/artists/'.$artist->id, ['name' => 'Artista Editado'])->assertRedirect();
        $this->assertDatabaseHas('artists', ['name' => 'Artista Editado']);

        $this->delete('/admin/artists/'.$artist->id)->assertRedirect();
        $this->assertDatabaseMissing('artists', ['id' => $artist->id]);
    }

    public function test_song_crud(): void
    {
        $artist = Artist::create(['name' => 'Artista Song', 'slug' => 'artista-song']);

        $this->post('/admin/songs', [
            'title' => 'Música Teste',
            'artist_id' => $artist->id,
            'is_playable' => true,
        ])->assertRedirect();

        $song = Song::where('title', 'Música Teste')->first();
        $this->assertNotNull($song);

        $this->delete('/admin/songs/'.$song->id)->assertRedirect();
        $this->assertSoftDeleted('songs', ['id' => $song->id]);
    }

    public function test_podcast_crud(): void
    {
        $this->post('/admin/podcasts', [
            'name' => 'Podcast Teste',
            'is_active' => true,
        ])->assertRedirect();

        $podcast = Podcast::where('name', 'Podcast Teste')->first();
        $this->assertNotNull($podcast);

        $this->post('/admin/podcasts/'.$podcast->id.'/episodes', [
            'title' => 'Episódio 1',
            'audio_url' => 'https://example.com/audio.mp3',
            'is_published' => true,
            'published_at' => now()->format('Y-m-d H:i'),
        ])->assertRedirect();

        $this->assertDatabaseHas('podcast_episodes', ['title' => 'Episódio 1']);

        $this->delete('/admin/podcasts/'.$podcast->id)->assertRedirect();
        $this->assertSoftDeleted('podcasts', ['id' => $podcast->id]);
    }

    public function test_chart_crud_with_entries(): void
    {
        $song = Song::create([
            'title' => 'Música Ranking',
            'slug' => 'musica-ranking',
            'is_playable' => true,
        ]);

        $this->post('/admin/charts', [
            'name' => 'Ranking Teste',
            'period' => 'weekly',
            'is_active' => true,
        ])->assertRedirect();

        $chart = Chart::where('name', 'Ranking Teste')->first();
        $this->assertNotNull($chart);

        $this->post('/admin/charts/'.$chart->id.'/entries', [
            'entries' => [
                ['song_id' => $song->id, 'position' => 1, 'plays' => 100],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('chart_entries', ['chart_id' => $chart->id, 'position' => 1]);

        $this->delete('/admin/charts/'.$chart->id)->assertRedirect();
        $this->assertDatabaseMissing('charts', ['id' => $chart->id]);
    }

    public function test_promotion_crud(): void
    {
        $this->post('/admin/promotions', [
            'title' => 'Promoção Teste',
            'is_published' => true,
        ])->assertRedirect();

        $promotion = Promotion::where('title', 'Promoção Teste')->first();
        $this->assertNotNull($promotion);

        $this->delete('/admin/promotions/'.$promotion->id)->assertRedirect();
        $this->assertSoftDeleted('promotions', ['id' => $promotion->id]);
    }

    public function test_video_crud(): void
    {
        $this->post('/admin/videos', [
            'title' => 'Vídeo Teste',
            'video_url' => 'https://www.youtube.com/watch?v=abc123',
            'is_published' => true,
        ])->assertRedirect();

        $video = Video::where('title', 'Vídeo Teste')->first();
        $this->assertNotNull($video);

        $this->delete('/admin/videos/'.$video->id)->assertRedirect();
        $this->assertSoftDeleted('videos', ['id' => $video->id]);
    }

    public function test_program_crud(): void
    {
        $this->post('/admin/programs', [
            'name' => 'Programa Teste',
            'is_active' => true,
        ])->assertRedirect();

        $program = Program::where('name', 'Programa Teste')->first();
        $this->assertNotNull($program);

        $this->delete('/admin/programs/'.$program->id)->assertRedirect();
        $this->assertSoftDeleted('programs', ['id' => $program->id]);
    }

    public function test_advertiser_and_banner_crud(): void
    {
        $this->post('/admin/advertisers', [
            'name' => 'Anunciante Teste',
            'email' => 'comercial@teste.com',
        ])->assertRedirect();

        $advertiser = Advertiser::where('name', 'Anunciante Teste')->first();
        $this->assertNotNull($advertiser);

        $this->post('/admin/banners', [
            'advertiser_id' => $advertiser->id,
            'title' => 'Banner Teste',
            'position' => 'home_leaderboard',
            'is_active' => true,
        ])->assertRedirect();

        $banner = Banner::where('title', 'Banner Teste')->first();
        $this->assertNotNull($banner);
        $this->assertSame('home_leaderboard', $banner->position);

        $this->delete('/admin/banners/'.$banner->id)->assertRedirect();
        $this->delete('/admin/advertisers/'.$advertiser->id)->assertRedirect();
    }

    public function test_guest_cannot_access_admin(): void
    {
        auth()->logout();

        $this->get('/admin')->assertRedirect('/login');
    }
}