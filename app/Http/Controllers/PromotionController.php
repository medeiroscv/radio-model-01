<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Inertia\Inertia;
use Inertia\Response;

class PromotionController extends Controller
{
    public function index(): Response
    {
        $promotions = Promotion::active()
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Promotions', [
            'promotions' => $promotions,
        ]);
    }

    public function show(string $slug): Response
    {
        $promotion = Promotion::active()->where('slug', $slug)->firstOrFail();
        $promotion->increment('views');

        return Inertia::render('Promotions/Show', [
            'promotion' => $promotion,
        ]);
    }
}