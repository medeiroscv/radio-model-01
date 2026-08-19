<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(Request $request): Response
    {
        $categorySlug = $request->get('category');
        $search = $request->get('q');

        $category = $categorySlug ? NewsCategory::where('slug', $categorySlug)->first() : null;

        $news = News::published()
            ->with(['category', 'author:id,name'])
            ->when($category, fn ($q) => $q->where('news_category_id', $category->id))
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = NewsCategory::orderBy('sort_order')->get();

        return Inertia::render('News/Index', [
            'news' => $news,
            'categories' => $categories,
            'activeCategory' => $categorySlug,
            'search' => $search,
        ]);
    }

    public function show(string $slug): Response
    {
        $news = News::published()
            ->with(['category', 'author:id,name', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $news->increment('views');

        $related = News::published()
            ->where('news_category_id', $news->news_category_id)
            ->whereKeyNot($news->getKey())
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return Inertia::render('News/Show', [
            'news' => $news,
            'related' => $related,
            'pageTitle' => $news->meta_title ?: $news->title,
            'pageDescription' => $news->meta_description ?: $news->summary,
            'ogImage' => $news->featured_image,
            'canonical' => $news->canonical_url ?: url('/noticias/'.$news->slug),
        ]);
    }
}