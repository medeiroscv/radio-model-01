<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function index(): Response
    {
        $files = collect(Storage::disk('public')->files('media'))
            ->map(fn ($path) => $this->fileInfo($path))
            ->merge(
                collect(Storage::disk('public')->allDirectories('media'))
                    ->flatMap(fn ($dir) => Storage::disk('public')->files($dir))
                    ->map(fn ($path) => $this->fileInfo($path))
            )
            ->reject(fn ($f) => $f === null)
            ->sortByDesc('modified')
            ->values();

        return Inertia::render('Admin/Media/Index', [
            'files' => $files,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
        ]);

        $file = $request->file('file');
        $name = date('Y-m-d') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media', $name, 'public');

        if (! $path) {
            return back()->withErrors(['file' => 'Não foi possível salvar o arquivo.']);
        }

        return back()->with('success', 'Arquivo enviado: ' . Storage::disk('public')->url($path));
    }

    public function destroy(Request $request): \Illuminate\Http\RedirectResponse
    {
        $path = $request->input('path');

        if (! $path || ! str_starts_with((string) $path, 'media/')) {
            return back()->withErrors(['path' => 'Caminho inválido.']);
        }

        Storage::disk('public')->delete($path);

        return back()->with('success', 'Arquivo excluído.');
    }

    protected function fileInfo(string $path): ?array
    {
        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return [
            'path' => $path,
            'name' => basename($path),
            'url' => Storage::disk('public')->url($path),
            'size' => Storage::disk('public')->size($path),
            'modified' => Storage::disk('public')->lastModified($path),
        ];
    }
}