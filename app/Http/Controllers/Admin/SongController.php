<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    public function index(Request $request): Response
    {
        $songs = Song::with('artist')
            ->when($request->get('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Songs/Index', [
            'songs' => $songs,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Songs/Form', [
            'artists' => Artist::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        Song::create($data);

        return redirect()->route('admin.songs.index')->with('success', 'Música criada.');
    }

    public function edit(Song $song): Response
    {
        return Inertia::render('Admin/Songs/Form', [
            'song' => $song,
            'artists' => Artist::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Song $song): \Illuminate\Http\RedirectResponse
    {
        $song->update($this->validated($request));

        return redirect()->route('admin.songs.index')->with('success', 'Música atualizada.');
    }

    public function destroy(Song $song): \Illuminate\Http\RedirectResponse
    {
        $song->delete();

        return redirect()->route('admin.songs.index')->with('success', 'Música excluída.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'artist_id' => ['nullable', 'exists:artists,id'],
            'title' => ['required', 'string', 'max:255'],
            'cover' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string'],
            'spotify_url' => ['nullable', 'url', 'max:2048'],
            'youtube_url' => ['nullable', 'url', 'max:2048'],
            'deezer_url' => ['nullable', 'url', 'max:2048'],
            'apple_music_url' => ['nullable', 'url', 'max:2048'],
            'external_url' => ['nullable', 'url', 'max:2048'],
            'is_featured' => ['boolean'],
            'is_release' => ['boolean'],
            'released_at' => ['nullable', 'date'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_release'] = $request->boolean('is_release');

        return $data;
    }
}