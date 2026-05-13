<?php

namespace App\Http\Controllers;

use App\Mail\PostMessageRelay;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class MessageController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $user = $request->user();

        // Rate Limiting: 10 messages per hour
        $key = 'send-message:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', 'You have sent too many messages. Please try again in ' . ceil($seconds / 60) . ' minutes.');
        }

        $validated = $request->validate([
            'message' => 'required|string|min:10|max:2000',
        ]);

        RateLimiter::hit($key, 3600); // 1 hour

        Mail::to($post->user->email)->send(new PostMessageRelay($post, $user, $validated['message']));

        return back()->with('success', 'Your message has been securely sent to the author!');
    }
}
