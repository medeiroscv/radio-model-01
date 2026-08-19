<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use App\Models\PodcastEpisode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PodcastEpisodeController extends Controller
{
    public function index(Podcast $podcast): Response
    {
        $episodes = $podcast->episodes()
            ->when(request()->get('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Podcasts/Episodes/Index', [
            'podcast' => $podcast,
            'episodes' => $episodes,
            'filters' => request()->only(['search']),
        ]);
    }

    public function create(Podcast $podcast): Response
    {
        return Inertia::render('Admin/Podcasts/Episodes/Form', [
            'podcast' => $podcast,
        ]);
    }

    public function store(Request $request, Podcast $podcast): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request, $podcast);
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));

        $podcast->episodes()->create($data);

        return redirect()->route('admin.podcasts.episodes.index', $podcast)
            ->with('success', 'Episódio criado.');
    }

    public function edit(Podcast $podcast, PodcastEpisode $episode): Response
    {
        return Inertia::render('Admin/Podcasts/Episodes/Form', [
            'podcast' => $podcast,
            'episode' => $episode,
        ]);
    }

    public function update(Request $request, Podcast $podcast, PodcastEpisode $episode): \Illuminate\Http\RedirectResponse
    {
        $episode->update($this->validated($request, $podcast));

        return redirect()->route('admin.podcasts.episodes.index', $podcast)
            ->with('success', 'Episódio atualizado.');
    }

    public function destroy(Podcast $podcast, PodcastEpisode $episode): \Illuminate\Http\RedirectResponse
    {
        $episode->delete();

        return redirect()->route('admin.podcasts.episodes.index', $podcast)
            ->with('success', 'Episódio excluído.');
    }

    protected function validated(Request $request, Podcast $podcast): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'audio_url' => ['nullable', 'url', 'max:2048'],
            'duration' => ['nullable', 'string', 'max:20'],
            'image' => ['nullable', 'url', 'max:2048'],
            'external_url' => ['nullable', 'url', 'max:2048'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->filled('published_at')) {
            $data['published_at'] = $request->input('published_at');
        } elseif ($data['is_published']) {
            $data['published_at'] = now();
        }

        return $data;
    }
}