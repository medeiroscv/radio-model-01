<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PodcastController extends Controller
{
    public function index(Request $request): Response
    {
        $podcasts = Podcast::withCount(['episodes' => fn ($q) => $q->where('is_published', true)])
            ->when($request->get('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Podcasts/Index', [
            'podcasts' => $podcasts,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Podcasts/Form');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));

        Podcast::create($data);

        return redirect()->route('admin.podcasts.index')->with('success', 'Podcast criado.');
    }

    public function edit(Podcast $podcast): Response
    {
        return Inertia::render('Admin/Podcasts/Form', [
            'podcast' => $podcast,
        ]);
    }

    public function update(Request $request, Podcast $podcast): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));

        $podcast->update($data);

        return redirect()->route('admin.podcasts.index')->with('success', 'Podcast atualizado.');
    }

    public function destroy(Podcast $podcast): \Illuminate\Http\RedirectResponse
    {
        $podcast->delete();

        return redirect()->route('admin.podcasts.index')->with('success', 'Podcast excluído.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cover' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string'],
            'host' => ['nullable', 'string', 'max:255'],
            'rss_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}