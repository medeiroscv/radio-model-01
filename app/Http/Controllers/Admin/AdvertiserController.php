<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertiser;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdvertiserController extends Controller
{
    public function index(Request $request): Response
    {
        $advertisers = Advertiser::withCount('banners')
            ->when($request->get('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Advertisers/Index', [
            'advertisers' => $advertisers,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Advertisers/Form');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Advertiser::create($this->validated($request));

        return redirect()->route('admin.advertisers.index')->with('success', 'Anunciante criado.');
    }

    public function edit(Advertiser $advertiser): Response
    {
        return Inertia::render('Admin/Advertisers/Form', [
            'advertiser' => $advertiser,
        ]);
    }

    public function update(Request $request, Advertiser $advertiser): \Illuminate\Http\RedirectResponse
    {
        $advertiser->update($this->validated($request));

        return redirect()->route('admin.advertisers.index')->with('success', 'Anunciante atualizado.');
    }

    public function destroy(Advertiser $advertiser): \Illuminate\Http\RedirectResponse
    {
        $advertiser->delete();

        return redirect()->route('admin.advertisers.index')->with('success', 'Anunciante excluído.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);
    }
}