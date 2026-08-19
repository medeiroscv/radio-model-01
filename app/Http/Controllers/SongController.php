<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->get('q');

        $songs = Song::with('artist')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhereHas('artist', fn ($a) => $a->where('name', 'like', "%{$search}%")))
            ->orderBy('title')
            ->paginate(24)
            ->withQueryString();

        return Inertia::render('Songs', [
            'songs' => $songs,
            'search' => $search,
        ]);
    }
}