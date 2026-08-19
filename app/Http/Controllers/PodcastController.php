<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use Inertia\Inertia;
use Inertia\Response;

class PodcastController extends Controller
{
    public function index(): Response
    {
        $podcasts = Podcast::where('is_active', true)
            ->withCount(['episodes as episodes_count' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return Inertia::render('Podcasts', [
            'podcasts' => $podcasts,
        ]);
    }

    public function show(string $slug): Response
    {
        $podcast = Podcast::where('is_active', true)->where('slug', $slug)->firstOrFail();

        $episodes = $podcast->episodes()->published()->paginate(12);

        return Inertia::render('Podcasts/Show', [
            'podcast' => $podcast,
            'episodes' => $episodes,
            'pageTitle' => $podcast->name,
            'pageDescription' => $podcast->description,
            'ogImage' => $podcast->cover,
        ]);
    }
}