<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertiser;
use App\Models\Banner;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    public function index(Request $request): Response
    {
        $banners = Banner::with('advertiser')
            ->when($request->get('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Banners/Index', [
            'banners' => $banners,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Banners/Form', [
            'advertisers' => Advertiser::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Banner::create($this->validated($request));

        return redirect()->route('admin.banners.index')->with('success', 'Banner criado.');
    }

    public function edit(Banner $banner): Response
    {
        return Inertia::render('Admin/Banners/Form', [
            'banner' => $banner,
            'advertisers' => Advertiser::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Banner $banner): \Illuminate\Http\RedirectResponse
    {
        $banner->update($this->validated($request));

        return redirect()->route('admin.banners.index')->with('success', 'Banner atualizado.');
    }

    public function destroy(Banner $banner): \Illuminate\Http\RedirectResponse
    {
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner excluído.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'advertiser_id' => ['nullable', 'exists:advertisers,id'],
            'title' => ['required', 'string', 'max:255'],
            'internal_title' => ['nullable', 'string', 'max:255'],
            'image_desktop' => ['nullable', 'url', 'max:2048'],
            'image_mobile' => ['nullable', 'url', 'max:2048'],
            'url' => ['nullable', 'url', 'max:2048'],
            'position' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}