<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'consent' => ['required', 'accepted'],
        ]);

        Newsletter::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'consent' => true,
                'status' => 'subscribed',
            ]
        );

        return back()->with('newsletter_ok', true);
    }

    public function unsubscribe(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        Newsletter::where('email', $data['email'])->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        return back()->with('newsletter_ok', true);
    }
}