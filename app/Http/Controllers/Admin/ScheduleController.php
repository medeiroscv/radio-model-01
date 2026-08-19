<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presenter;
use App\Models\Program;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    public function index(Request $request): Response
    {
        $schedules = Schedule::with(['program', 'presenter'])
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Schedules/Index', [
            'schedules' => $schedules,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Schedules/Form', [
            'programs' => Program::orderBy('name')->get(),
            'presenters' => Presenter::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->validated($request)->create($request->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Horário criado.');
    }

    public function edit(Schedule $schedule): Response
    {
        return Inertia::render('Admin/Schedules/Form', [
            'schedule' => $schedule,
            'programs' => Program::orderBy('name')->get(),
            'presenters' => Presenter::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Schedule $schedule): \Illuminate\Http\RedirectResponse
    {
        $schedule->update($this->validated($request)->all());

        return redirect()->route('admin.schedules.index')->with('success', 'Horário atualizado.');
    }

    public function destroy(Schedule $schedule): \Illuminate\Http\RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Horário excluído.');
    }

    protected function validated(Request $request): Schedule
    {
        $data = $request->validate([
            'program_id' => ['required', 'exists:programs,id'],
            'presenter_id' => ['nullable', 'exists:presenters,id'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'days_of_week' => ['required', 'array', 'min:1'],
            'days_of_week.*' => ['integer', 'between:1,7'],
            'is_active' => ['boolean'],
        ]);

        $schedule = new Schedule($data);
        $schedule->is_active = $request->boolean('is_active');

        return $schedule;
    }
}