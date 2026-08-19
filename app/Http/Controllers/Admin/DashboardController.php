<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Contact;
use App\Models\News;
use App\Models\Newsletter;
use App\Models\Promotion;
use App\Models\Schedule;
use App\Models\Song;
use App\Models\StreamHistory;
use App\Models\Video;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'news' => News::count(),
                'promotions' => Promotion::count(),
                'songs' => Song::count(),
                'videos' => Video::count(),
                'schedules' => Schedule::count(),
                'banners' => Banner::count(),
                'contacts' => Contact::count(),
                'newsletters' => Newsletter::count(),
            ],
            'latestNews' => News::with('category')->orderByDesc('created_at')->take(5)->get(),
            'recentContacts' => Contact::orderByDesc('created_at')->take(5)->get(),
            'recentTracks' => StreamHistory::orderByDesc('played_at')->take(5)->get(),
        ]);
    }
}