<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Presenter;
use App\Services\Streaming\StreamingService;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function __construct(protected StreamingService $streaming)
    {
    }

    public function index(): Response
    {
        $week = $this->streaming->weeklySchedule();

        $programs = Program::where('is_active', true)
            ->with('presenter')
            ->orderBy('sort_order')
            ->get();

        $presenters = Presenter::where('is_active', true)->orderBy('sort_order')->get();

        return Inertia::render('Schedule', [
            'week' => $week,
            'programs' => $programs,
            'presenters' => $presenters,
        ]);
    }
}