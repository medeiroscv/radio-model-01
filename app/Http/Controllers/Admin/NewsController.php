<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(Request $request): Response
    {
        $news = News::with(['category', 'author:id,name'])
            ->when($request->get('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->get('status'), fn ($q, $s) => $q->where('is_published', $s === 'published'))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/News/Index', [
            'news' => $news,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/News/Form', [
            'categories' => NewsCategory::orderBy('sort_order')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request);

        $news = News::create($data);
        $news->tags()->sync($request->input('tag_ids', []));

        return redirect()->route('admin.news.index')->with('success', 'Notícia criada com sucesso.');
    }

    public function edit(News $news): Response
    {
        $news->load('tags');

        return Inertia::render('Admin/News/Form', [
            'news' => $news,
            'categories' => NewsCategory::orderBy('sort_order')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, News $news): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request, $news);

        $news->update($data);
        $news->tags()->sync($request->input('tag_ids', []));

        return redirect()->route('admin.news.index')->with('success', 'Notícia atualizada com sucesso.');
    }

    public function destroy(News $news): \Illuminate\Http\RedirectResponse
    {
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Notícia excluída.');
    }

    protected function validated(Request $request, ?News $news = null): array
    {
        $data = $request->validate([
            'news_category_id' => ['nullable', 'exists:news_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'url', 'max:2048'],
            'gallery' => ['nullable', 'array'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $data['slug'] = $news ? $news->slug : (Str::slug($data['title']).'-'.Str::lower(Str::random(5)));
        $data['user_id'] = $data['user_id'] ?? auth()->id();
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