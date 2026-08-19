<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chart;
use App\Models\ChartEntry;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ChartController extends Controller
{
    public function index(Request $request): Response
    {
        $charts = Chart::withCount('entries')
            ->when($request->get('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Charts/Index', [
            'charts' => $charts,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Charts/Form');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $chart = Chart::create($this->validated($request));

        return redirect()->route('admin.charts.edit', $chart)->with('success', 'Ranking criado.');
    }

    public function edit(Chart $chart): Response
    {
        $chart->load(['entries.song.artist']);

        return Inertia::render('Admin/Charts/Form', [
            'chart' => $chart,
            'songs' => Song::with('artist')->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, Chart $chart): \Illuminate\Http\RedirectResponse
    {
        $chart->update($this->validated($request));

        return redirect()->route('admin.charts.index')->with('success', 'Ranking atualizado.');
    }

    public function destroy(Chart $chart): \Illuminate\Http\RedirectResponse
    {
        $chart->delete();

        return redirect()->route('admin.charts.index')->with('success', 'Ranking excluído.');
    }

    public function syncEntries(Request $request, Chart $chart): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'entries' => ['nullable', 'array', 'max:100'],
            'entries.*.song_id' => ['required', 'exists:songs,id'],
            'entries.*.position' => ['required', 'integer', 'min:1'],
            'entries.*.plays' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($chart, $data) {
            $chart->entries()->delete();

            foreach (($data['entries'] ?? []) as $entry) {
                $chart->entries()->create([
                    'song_id' => $entry['song_id'],
                    'position' => $entry['position'],
                    'plays' => $entry['plays'] ?? 0,
                ]);
            }
        });

        return back()->with('success', 'Posições do ranking salvas.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'period' => ['required', 'in:daily,weekly,monthly'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}