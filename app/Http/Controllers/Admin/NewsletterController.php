<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Inertia\Inertia;
use Inertia\Response;

class NewsletterController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Newsletters/Index', [
            'newsletters' => Newsletter::orderByDesc('created_at')->paginate(15),
        ]);
    }

    public function destroy(Newsletter $newsletter): \Illuminate\Http\RedirectResponse
    {
        $newsletter->delete();

        return back()->with('success', 'Inscrição excluída.');
    }
}