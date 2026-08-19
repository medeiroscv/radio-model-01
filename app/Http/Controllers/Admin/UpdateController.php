<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Updater\UpdateService;
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
            ? Cache::remember('updater.latest', now()->addMinutes(10), fn () => $this->updater->latestRelease())
            : null;

        return Inertia::render('Admin/Update/Index', [
            'configured' => $this->updater->isConfigured(),
            'repo' => $this->updater->repo(),
            'currentVersion' => $this->updater->currentVersion(),
            'latest' => $latest,
            'hasUpdate' => $this->updater->hasUpdate($latest),
        ]);
    }

    public function check(): RedirectResponse
    {
        $latest = $this->updater->latestRelease();

        if (! $latest) {
            return back()->with('error', 'Não foi possível consultar o GitHub. Verifique o UPDATE_REPO e tente novamente.');
        }

        Cache::put('updater.latest', $latest, now()->addMinutes(10));

        if ($this->updater->hasUpdate($latest)) {
            return back()->with('success', 'Nova versão '.$latest['version'].' disponível no GitHub.');
        }

        return back()->with('success', 'Você já está na versão mais recente ('.$this->updater->currentVersion().').');
    }

    public function update(): RedirectResponse
    {
        try {
            $result = $this->updater->update();

            return back()->with('success', 'Atualização concluída! Seu site agora está na versão '.$result['version'].'.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Falha na atualização: '.$e->getMessage());
        }
    }
}