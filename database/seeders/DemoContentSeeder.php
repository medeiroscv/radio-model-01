<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Chart;
use App\Models\ChartEntry;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Podcast;
use App\Models\PodcastEpisode;
use App\Models\Presenter;
use App\Models\Program;
use App\Models\Promotion;
use App\Models\Schedule;
use App\Models\Song;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            ['name' => 'Administrador', 'password' => bcrypt('password')]
        );
        $admin->assignRole('super-admin');

        $presenters = [];
        foreach (['Fernanda Alves', 'Carlos Mota', 'Bia Nunes', 'Rafael Lima'] as $i => $name) {
            $presenters[] = Presenter::create([
                'name' => $name,
                'slug' => \Str::slug($name),
                'biography' => 'Apresentador(a) da rádio.',
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }

        $programs = [];
        foreach (['Bom Dia Brasil', 'Tarde Quente', 'Festival de Sucessos', 'Madrugada Musical'] as $i => $name) {
            $programs[] = Program::create([
                'name' => $name,
                'slug' => \Str::slug($name),
                'description' => 'Programa da rádio.',
                'category' => 'Entretenimento',
                'presenter_id' => $presenters[$i % count($presenters)]->id,
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }

        $days = [1, 2, 3, 4, 5, 6, 7];
        foreach ($programs as $i => $program) {
            Schedule::create([
                'program_id' => $program->id,
                'presenter_id' => $program->presenter_id,
                'start_time' => sprintf('%02d:00:00', ($i * 6)),
                'end_time' => sprintf('%02d:00:00', ($i * 6 + 5) % 24),
                'days_of_week' => $days,
                'is_active' => true,
            ]);
        }

        $artists = [];
        foreach (['Artista Um', 'Artista Dois', 'Artista Três'] as $name) {
            $artists[] = Artist::create(['name' => $name, 'slug' => \Str::slug($name)]);
        }

        $songs = [];
        foreach (['Música Teste Um', 'Música Teste Dois', 'Música Teste Três', 'Música Teste Quatro'] as $i => $title) {
            $songs[] = Song::create([
                'title' => $title,
                'slug' => \Str::slug($title),
                'artist_id' => $artists[$i % count($artists)]->id,
                'is_release' => $i === 0,
                'is_featured' => $i < 2,
            ]);
        }

        $chart = Chart::create([
            'name' => 'Ranking da Semana',
            'period' => 'weekly',
            'starts_at' => now()->startOfWeek(),
            'ends_at' => now()->endOfWeek(),
            'is_active' => true,
        ]);

        foreach ($songs as $i => $song) {
            ChartEntry::create([
                'chart_id' => $chart->id,
                'song_id' => $song->id,
                'position' => $i + 1,
                'plays' => 100 - ($i * 10),
            ]);
        }

        $category = NewsCategory::firstOrCreate(['name' => 'Entretenimento']);
        for ($i = 1; $i <= 6; $i++) {
            News::create([
                'news_category_id' => $category->id,
                'user_id' => $admin->id,
                'title' => "Notícia de exemplo número {$i}",
                'slug' => "noticia-de-exemplo-numero-{$i}",
                'summary' => 'Resumo da notícia de exemplo.',
                'content' => '<p>Conteúdo da notícia de exemplo.</p>',
                'is_featured' => $i === 1,
                'is_published' => true,
                'published_at' => now()->subDays($i - 1),
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            Promotion::create([
                'title' => "Promoção de exemplo {$i}",
                'slug' => "promocao-de-exemplo-{$i}",
                'call_to_action' => 'Participe agora',
                'description' => 'Descrição da promoção de exemplo.',
                'rules' => 'Regras da promoção.',
                'regulations' => 'Regulamento da promoção.',
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'is_active' => true,
                'is_featured' => $i === 1,
            ]);
        }

        $podcast = Podcast::create([
            'name' => 'Podcast da Rádio',
            'slug' => 'podcast-da-radio',
            'description' => 'Podcast de exemplo.',
            'host' => 'Fernanda Alves',
            'is_active' => true,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            PodcastEpisode::create([
                'podcast_id' => $podcast->id,
                'title' => "Episódio {$i}",
                'slug' => "episodio-{$i}",
                'description' => 'Descrição do episódio.',
                'duration' => '15:30',
                'is_published' => true,
                'published_at' => now()->subDays($i * 2),
            ]);
        }

        for ($i = 1; $i <= 3; $i++) {
            Video::create([
                'title' => "Vídeo de exemplo {$i}",
                'slug' => "video-de-exemplo-{$i}",
                'description' => 'Descrição do vídeo.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'platform' => 'youtube',
                'video_id' => 'dQw4w9WgXcQ',
                'news_category_id' => $category->id,
                'is_featured' => $i === 1,
                'is_published' => true,
                'published_at' => now()->subDays($i),
            ]);
        }
    }
}