<?php

namespace App\Http\Controllers;

use App\Models\Chart;
use Inertia\Inertia;
use Inertia\Response;

class ChartController extends Controller
{
    public function index(): Response
    {
        $charts = Chart::where('is_active', true)
            ->with(['entries' => fn ($q) => $q->with('song.artist')->orderBy('position')])
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Charts', [
            'charts' => $charts,
        ]);
    }
}