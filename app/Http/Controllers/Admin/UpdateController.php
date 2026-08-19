<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Updater\UpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class UpdateController extends Controller
{
    public function __construct(protected UpdateService $updater) {}

    public function index(): Response
    {
        $latest = $this->updater->isConfigured()
            ? Cache::remember(
                'updater.latest',
                now()->addMinutes(10),
                fn () => $this->updater->latestRelease()
            )
            : null;

        return Inertia::render('Admin/Update/Index', [
            'configured' => $this->updater->isConfigured(),
            'repo' => $this->updater->repo(),
            'currentVersion' => $this->updater->currentVersion(),
            'latest' => $latest,
            'hasUpdate' => $this->updater->hasUpdate($latest),
            'updateStatus' => $this->updater->status(),
        ]);
    }

    public function check(): RedirectResponse
    {
        $latest = $this->updater->latestRelease();

        if (! $latest) {
            return back()->with(
                'error',
                'Não foi possível consultar o GitHub. Verifique o UPDATE_REPO e tente novamente.'
            );
        }

        Cache::put('updater.latest', $latest, now()->addMinutes(10));

        return $this->updater->hasUpdate($latest)
            ? back()->with('success', 'Nova versão disponível no GitHub.')
            : back()->with('success', 'Você já está na versão mais recente.');
    }

    public function status(): JsonResponse
    {
        return response()->json($this->updater->status());
    }

    public function prepare(): JsonResponse
    {
        try {
            return response()->json($this->updater->prepare());
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function step(): JsonResponse
    {
        try {
            return response()->json($this->updater->step());
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function finalize(): JsonResponse
    {
        try {
            return response()->json($this->updater->finalize());
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function reset(): JsonResponse
    {
        try {
            return response()->json($this->updater->resetFailed());
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
