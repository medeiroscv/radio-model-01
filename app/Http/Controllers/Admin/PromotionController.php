<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PromotionController extends Controller
{
    public function index(Request $request): Response
    {
        $promotions = Promotion::when($request->get('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Promotions/Index', [
            'promotions' => $promotions,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Promotions/Form');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        Promotion::create($data);

        return redirect()->route('admin.promotions.index')->with('success', 'Promoção criada.');
    }

    public function edit(Promotion $promotion): Response
    {
        return Inertia::render('Admin/Promotions/Form', [
            'promotion' => $promotion,
        ]);
    }

    public function update(Request $request, Promotion $promotion): \Illuminate\Http\RedirectResponse
    {
        $promotion->update($this->validated($request));

        return redirect()->route('admin.promotions.index')->with('success', 'Promoção atualizada.');
    }

    public function destroy(Promotion $promotion): \Illuminate\Http\RedirectResponse
    {
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('success', 'Promoção excluída.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'call_to_action' => ['nullable', 'string', 'max:255'],
            'rules' => ['nullable', 'string'],
            'regulations' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'url', 'max:2048'],
            'banner_image' => ['nullable', 'url', 'max:2048'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'participate_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }
}