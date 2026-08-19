<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class VideoController extends Controller
{
    public function index(Request $request): Response
    {
        $videos = Video::with('category')
            ->when($request->get('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderByDesc('published_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Videos/Index', [
            'videos' => $videos,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Videos/Form', [
            'categories' => NewsCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        Video::create($data);

        return redirect()->route('admin.videos.index')->with('success', 'Vídeo criado.');
    }

    public function edit(Video $video): Response
    {
        return Inertia::render('Admin/Videos/Form', [
            'video' => $video,
            'categories' => NewsCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Video $video): \Illuminate\Http\RedirectResponse
    {
        $video->update($this->validated($request));

        return redirect()->route('admin.videos.index')->with('success', 'Vídeo atualizado.');
    }

    public function destroy(Video $video): \Illuminate\Http\RedirectResponse
    {
        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Vídeo excluído.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'url', 'max:2048'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'platform' => ['nullable', 'string', 'max:255'],
            'video_id' => ['nullable', 'string', 'max:255'],
            'news_category_id' => ['nullable', 'exists:news_categories,id'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');

        if ($request->filled('published_at')) {
            $data['published_at'] = $request->input('published_at');
        } elseif ($data['is_published']) {
            $data['published_at'] = now();
        }

        return $data;
    }
}