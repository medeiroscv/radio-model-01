<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class NewsCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/NewsCategories/Index', [
            'categories' => NewsCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        NewsCategory::create($data);

        return back()->with('success', 'Categoria criada.');
    }

    public function update(Request $request, NewsCategory $newsCategory): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        $newsCategory->update($data);

        return back()->with('success', 'Categoria atualizada.');
    }

    public function destroy(NewsCategory $newsCategory): \Illuminate\Http\RedirectResponse
    {
        $newsCategory->delete();

        return back()->with('success', 'Categoria excluída.');
    }
}