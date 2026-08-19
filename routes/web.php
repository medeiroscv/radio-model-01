<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BrandingAssetController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Installer\InstallerController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\StreamStatusController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rotas do instalador (acessíveis apenas antes da instalação)
Route::get('/install', [InstallerController::class, 'index'])->name('installer.index');
Route::post('/install', [InstallerController::class, 'store'])->name('installer.store');
Route::post('/install/check-database', [InstallerController::class, 'checkDatabase'])->name('installer.check-database');
Route::get('/install/complete', [InstallerController::class, 'complete'])->name('installer.complete');

// Assets de identidade visual. A URL termina em /serve de propósito:
// isso impede que regras de arquivos estáticos do Nginx interceptem PNG/JPG/ICO
// antes que a requisição chegue ao Laravel.
Route::get('/branding-assets/{filename}/serve', BrandingAssetController::class)
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('branding.asset');

// Compatibilidade com instalações que já utilizavam a URL antiga.
Route::get('/uploads/branding/{filename}', BrandingAssetController::class)
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('branding.asset.legacy');

// Página inicial pública
Route::get('/', [HomeController::class, 'index'])->name('home');

// Status do streaming (polling do player)
Route::get('/api/stream/status', StreamStatusController::class)->name('stream.status');

// Rotas públicas do site
Route::get('/promocoes', [PromotionController::class, 'index'])->name('promotions.index');
Route::get('/promocoes/{slug}', [PromotionController::class, 'show'])->name('promotions.show');

Route::get('/noticias', [NewsController::class, 'index'])->name('news.index');
Route::get('/noticias/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/musicas', [SongController::class, 'index'])->name('songs.index');

Route::get('/programacao', [ScheduleController::class, 'index'])->name('schedule.index');
Route::get('/programas/{slug}', [ProgramController::class, 'show'])->name('programs.show');

Route::get('/podcasts', [PodcastController::class, 'index'])->name('podcasts.index');
Route::get('/podcasts/{slug}', [PodcastController::class, 'show'])->name('podcasts.show');

Route::get('/rankings', [ChartController::class, 'index'])->name('charts.index');

Route::get('/a-radio', [AboutController::class, 'index'])->name('about');

Route::get('/contato', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::post('/contato', [ContactController::class, 'store'])->name('contact.store');

// Newsletter pública
Route::post('/newsletter', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// SEO
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/admin.php';
require __DIR__.'/auth.php';
