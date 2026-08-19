<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ArtistController extends Controller
{
    public function index(Request $request): Response
    {
        $artists = Artist::withCount('songs')
            ->when($request->get('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Artists/Index', [
            'artists' => $artists,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Artists/Form');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'url', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));

        Artist::create($data);

        return redirect()->route('admin.artists.index')->with('success', 'Artista criado.');
    }

    public function edit(Artist $artist): Response
    {
        return Inertia::render('Admin/Artists/Form', [
            'artist' => $artist,
        ]);
    }

    public function update(Request $request, Artist $artist): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'url', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));

        $artist->update($data);

        return redirect()->route('admin.artists.index')->with('success', 'Artista atualizado.');
    }

    public function destroy(Artist $artist): \Illuminate\Http\RedirectResponse
    {
        $artist->delete();

        return redirect()->route('admin.artists.index')->with('success', 'Artista excluído.');
    }
}