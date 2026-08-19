<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presenter;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function index(Request $request): Response
    {
        $programs = Program::with('presenter')
            ->when($request->get('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('sort_order')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Programs/Index', [
            'programs' => $programs,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Programs/Form', [
            'presenters' => Presenter::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        Program::create($data);

        return redirect()->route('admin.programs.index')->with('success', 'Programa criado.');
    }

    public function edit(Program $program): Response
    {
        return Inertia::render('Admin/Programs/Form', [
            'program' => $program,
            'presenters' => Presenter::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Program $program): \Illuminate\Http\RedirectResponse
    {
        $program->update($this->validated($request));

        return redirect()->route('admin.programs.index')->with('success', 'Programa atualizado.');
    }

    public function destroy(Program $program): \Illuminate\Http\RedirectResponse
    {
        $program->delete();

        return redirect()->route('admin.programs.index')->with('success', 'Programa excluído.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'presenter_id' => ['nullable', 'exists:presenters,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'url', 'max:2048'],
            'category' => ['nullable', 'string', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}