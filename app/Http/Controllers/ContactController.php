<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $key = 'contact:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->withErrors([
                'email' => 'Muitas mensagens enviadas. Aguarde alguns minutos.',
            ]);
        }

        RateLimiter::hit($key, 300);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'message' => $request->message,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Mensagem enviada com sucesso! Em breve entraremos em contato.');
    }
}