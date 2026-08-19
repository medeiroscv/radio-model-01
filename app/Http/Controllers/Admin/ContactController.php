<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Contacts/Index', [
            'contacts' => Contact::orderByDesc('created_at')->paginate(15),
        ]);
    }

    public function destroy(Contact $contact): \Illuminate\Http\RedirectResponse
    {
        $contact->delete();

        return back()->with('success', 'Contato excluído.');
    }
}