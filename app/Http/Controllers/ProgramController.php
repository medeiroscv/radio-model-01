<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function show(string $slug): Response
    {
        $program = Program::where('is_active', true)
            ->where('slug', $slug)
            ->with('presenter', 'schedules.presenter')
            ->firstOrFail();

        return Inertia::render('Programs/Show', [
            'program' => $program,
            'pageTitle' => $program->name,
            'pageDescription' => $program->description,
            'ogImage' => $program->image,
        ]);
    }
}