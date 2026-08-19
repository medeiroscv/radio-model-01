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

        Route::resource('news', NewsController::class)->except(['show']);
        Route::resource('news-categories', NewsCategoryController::class)->except(['show', 'create']);

        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::delete('media', [MediaController::class, 'destroy'])->name('media.destroy');

        Route::resource('promotions', PromotionController::class)->except(['show']);

        Route::resource('programs', ProgramController::class)->except(['show']);
        Route::resource('presenters', PresenterController::class)->except(['show']);
        Route::resource('schedules', ScheduleController::class)->except(['show']);

        Route::resource('artists', ArtistController::class)->except(['show']);
        Route::resource('songs', SongController::class)->except(['show']);

        Route::resource('charts', ChartController::class)->except(['show']);
        Route::post('charts/{chart}/entries', [ChartController::class, 'syncEntries'])
            ->name('charts.entries');

        Route::resource('videos', VideoController::class)->except(['show']);
        Route::resource('podcasts', PodcastController::class)->except(['show']);

        Route::prefix('podcasts/{podcast}')->name('podcasts.')->group(function () {
            Route::get('episodes', [PodcastEpisodeController::class, 'index'])->name('episodes.index');
            Route::get('episodes/create', [PodcastEpisodeController::class, 'create'])->name('episodes.create');
            Route::post('episodes', [PodcastEpisodeController::class, 'store'])->name('episodes.store');
            Route::get('episodes/{episode}/edit', [PodcastEpisodeController::class, 'edit'])->name('episodes.edit');
            Route::put('episodes/{episode}', [PodcastEpisodeController::class, 'update'])->name('episodes.update');
            Route::delete('episodes/{episode}', [PodcastEpisodeController::class, 'destroy'])->name('episodes.destroy');
        });

        Route::get('stream', [StreamController::class, 'edit'])->name('stream.edit');
        Route::post('stream', [StreamController::class, 'update'])->name('stream.update');

        Route::get('station', [StationController::class, 'edit'])->name('station.edit');
        Route::post('station', [StationController::class, 'update'])->name('station.update');

        Route::resource('banners', BannerController::class)->except(['show']);
        Route::resource('advertisers', AdvertiserController::class)->except(['show']);

        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::delete('contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

        Route::get('newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');
        Route::delete('newsletters/{newsletter}', [NewsletterController::class, 'destroy'])->name('newsletters.destroy');

        Route::resource('users', AdminUserController::class)->except(['show']);

        Route::middleware('role:super-admin|admin')->group(function () {
            Route::get('update', [UpdateController::class, 'index'])->name('update.index');
            Route::post('update/check', [UpdateController::class, 'check'])->name('update.check');
            Route::get('update/status', [UpdateController::class, 'status'])->name('update.status');
            Route::post('update/prepare', [UpdateController::class, 'prepare'])->name('update.prepare');
            Route::post('update/step', [UpdateController::class, 'step'])->name('update.step');
            Route::post('update/finalize', [UpdateController::class, 'finalize'])->name('update.finalize');
            Route::post('update/reset', [UpdateController::class, 'reset'])->name('update.reset');
        });
    });
