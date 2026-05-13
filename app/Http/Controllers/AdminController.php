<?php

namespace App\Http\Controllers;

use App\Models\CategorySuggestion;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_users' => User::count(),
            'verified_users' => User::where('is_verified', true)->count(),
            'active_posts' => Post::where('expires_at', '>', now())->count(),
            'pending_suggestions' => CategorySuggestion::where('status', 'pending')->count(),
        ];

        $suggestions = CategorySuggestion::with(['user', 'post'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($suggestion) {
                return [
                    'id' => $suggestion->id,
                    'suggested_name' => $suggestion->suggested_name,
                    'user_name' => $suggestion->user->name,
                    'post_title' => $suggestion->post ? $suggestion->post->title : 'Deleted Post',
                    'post_id' => $suggestion->post_id,
                    'created_at' => $suggestion->created_at,
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'metrics' => $metrics,
            'suggestions' => $suggestions,
        ]);
    }
}
