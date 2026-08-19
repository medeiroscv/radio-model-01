<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdvertiserController;
use App\Http\Controllers\Admin\ArtistController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ChartController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\PodcastController;
use App\Http\Controllers\Admin\PodcastEpisodeController;
use App\Http\Controllers\Admin\PresenterController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SongController;
use App\Http\Controllers\Admin\StationController;
use App\Http\Controllers\Admin\StreamController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:super-admin|admin|editor|journalist|commercial|presenter'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Conteúdo - Notícias
        Route::resource('news', NewsController::class)->except(['show']);
        Route::resource('news-categories', NewsCategoryController::class)->except(['show', 'create']);

        // Conteúdo - Mídia
        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('media', [MediaController::class, 'destroy'])->name('media.destroy');

        // Conteúdo - Promoções
        Route::resource('promotions', PromotionController::class)->except(['show']);

        // Rádio - Programação
        Route::resource('programs', ProgramController::class)->except(['show']);
        Route::resource('presenters', PresenterController::class)->except(['show']);
        Route::resource('schedules', ScheduleController::class)->except(['show']);

        // Rádio - Música
        Route::resource('artists', ArtistController::class)->except(['show']);
        Route::resource('songs', SongController::class)->except(['show']);

        // Rádio - Rankings
        Route::resource('charts', ChartController::class)->except(['show']);
        Route::post('charts/{chart}/entries', [ChartController::class, 'syncEntries'])->name('charts.entries');

        // Rádio - Vídeos
        Route::resource('videos', VideoController::class)->except(['show']);

        // Rádio - Podcasts
        Route::resource('podcasts', PodcastController::class)->except(['show']);
        Route::prefix('podcasts/{podcast}')->name('podcasts.')->group(function () {
            Route::get('episodes', [PodcastEpisodeController::class, 'index'])->name('episodes.index');
            Route::get('episodes/create', [PodcastEpisodeController::class, 'create'])->name('episodes.create');
            Route::post('episodes', [PodcastEpisodeController::class, 'store'])->name('episodes.store');
            Route::get('episodes/{episode}/edit', [PodcastEpisodeController::class, 'edit'])->name('episodes.edit');
            Route::put('episodes/{episode}', [PodcastEpisodeController::class, 'update'])->name('episodes.update');
            Route::delete('episodes/{episode}', [PodcastEpisodeController::class, 'destroy'])->name('episodes.destroy');
        });

        // Rádio - Streaming
        Route::get('stream', [StreamController::class, 'edit'])->name('stream.edit');
        Route::post('stream', [StreamController::class, 'update'])->name('stream.update');

        // Aparência - Configurações da rádio
        Route::get('station', [StationController::class, 'edit'])->name('station.edit');
        Route::post('station', [StationController::class, 'update'])->name('station.update');

        // Publicidade - Banners e Anunciantes
        Route::resource('banners', BannerController::class)->except(['show']);
        Route::resource('advertisers', AdvertiserController::class)->except(['show']);

        // Comunicação - Contatos e Newsletter
        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::delete('contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');
        Route::get('newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');
        Route::delete('newsletters/{newsletter}', [NewsletterController::class, 'destroy'])->name('newsletters.destroy');

        // Sistema - Usuários
        Route::resource('users', AdminUserController::class)->except(['show']);

        // Sistema - Atualizações (via GitHub)
        Route::get('update', [UpdateController::class, 'index'])->name('update.index')->middleware('role:super-admin|admin');
        Route::post('update/check', [UpdateController::class, 'check'])->name('update.check')->middleware('role:super-admin|admin');
        Route::post('update', [UpdateController::class, 'update'])->name('update.run')->middleware('role:super-admin|admin');
    });