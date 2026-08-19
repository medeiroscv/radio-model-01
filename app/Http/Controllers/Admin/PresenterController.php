<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presenter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PresenterController extends Controller
{
    public function index(Request $request): Response
    {
        $presenters = Presenter::when($request->get('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('sort_order')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Presenters/Index', [
            'presenters' => $presenters,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Presenters/Form');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        Presenter::create($data);

        return redirect()->route('admin.presenters.index')->with('success', 'Apresentador criado.');
    }

    public function edit(Presenter $presenter): Response
    {
        return Inertia::render('Admin/Presenters/Form', [
            'presenter' => $presenter,
        ]);
    }

    public function update(Request $request, Presenter $presenter): \Illuminate\Http\RedirectResponse
    {
        $presenter->update($this->validated($request));

        return redirect()->route('admin.presenters.index')->with('success', 'Apresentador atualizado.');
    }

    public function destroy(Presenter $presenter): \Illuminate\Http\RedirectResponse
    {
        $presenter->delete();

        return redirect()->route('admin.presenters.index')->with('success', 'Apresentador excluído.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'photo' => ['nullable', 'url', 'max:2048'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'x_twitter' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}