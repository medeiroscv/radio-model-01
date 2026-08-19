<?php

namespace App\Http\Controllers;

use App\Models\Chart;
use App\Models\News;
use App\Models\Podcast;
use App\Models\Promotion;
use App\Models\Schedule;
use App\Models\Song;
use App\Models\StreamHistory;
use App\Models\Video;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = $request->get('q');

        $featuredNews = News::published()
            ->featured()
            ->with(['category', 'author:id,name'])
            ->orderByDesc('published_at')
            ->take(5)
            ->get();

        $latestNews = News::published()
            ->with(['category', 'author:id,name'])
            ->when($query, fn ($q) => $q->where('title', 'like', "%{$query}%"))
            ->orderByDesc('published_at')
            ->take(12)
            ->get();

        $promotions = Promotion::active()
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $latestChart = Chart::with('entries.song.artist')
            ->orderByDesc('starts_at')
            ->first();

        $topSongs = $latestChart?->entries
            ?->sortBy('position')
            ?->take(10)
            ?->map(fn ($entry) => [
                'position' => $entry->position,
                'song' => $entry->song?->title,
                'artist' => $entry->song?->artist?->name,
                'cover' => $entry->song?->cover,
            ])
            ?->values();

        $releases = Song::where('is_release', true)
            ->whereNotNull('released_at')
            ->with('artist')
            ->orderByDesc('released_at')
            ->take(6)
            ->get();

        $videos = Video::published()
            ->with('category')
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        $podcasts = Podcast::where('is_active', true)
            ->with('episodes')
            ->take(3)
            ->get();

        $now = now();
        $currentSchedule = Schedule::where('is_active', true)
            ->whereRaw('JSON_CONTAINS(days_of_week, ?)', [(string) $now->dayOfWeekIso])
            ->whereTime('start_time', '<=', $now->format('H:i:s'))
            ->whereTime('end_time', '>', $now->format('H:i:s'))
            ->with(['program.presenter', 'presenter'])
            ->first();

        $recentTracks = StreamHistory::orderByDesc('played_at')->take(10)->get();

        return Inertia::render('Home', [
            'featuredNews' => $featuredNews,
            'latestNews' => $latestNews,
            'promotions' => $promotions,
            'topSongs' => $topSongs ?? [],
            'releases' => $releases,
            'videos' => $videos,
            'podcasts' => $podcasts,
            'currentSchedule' => $currentSchedule ? [
                'program' => $currentSchedule->program?->name,
                'program_slug' => $currentSchedule->program?->slug,
                'presenter' => $currentSchedule->presenter?->name ?? $currentSchedule->program?->presenter?->name,
                'start_time' => $currentSchedule->start_time,
                'end_time' => $currentSchedule->end_time,
            ] : null,
            'recentTracks' => $recentTracks,
        ]);
    }
}