<?php

namespace App\Http\Controllers;

use App\Models\Presenter;
use App\Models\Program;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function index(): Response
    {
        $presenters = Presenter::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $programs = Program::where('is_active', true)
            ->with('presenter')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        return Inertia::render('About', [
            'presenters' => $presenters,
            'programs' => $programs,
        ]);
    }
}