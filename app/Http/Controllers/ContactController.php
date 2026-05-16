<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageConfirmation;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $contactMessage = ContactMessage::create($validated);

        Mail::to('kortby@gmail.com')->send(new ContactMessageReceived($contactMessage));
        Mail::to($request->email)->send(new ContactMessageConfirmation($contactMessage));

        return redirect()->back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');
    }
}
