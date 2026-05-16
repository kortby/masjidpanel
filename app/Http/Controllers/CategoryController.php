<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->session()->get('location');

        $categories = Category::orderedByLocationDemand($city)->get();

        $recentPosts = Post::with(['user', 'category', 'media'])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'city' => $post->city,
                'zip_code' => $post->zip_code,
                'created_at' => $post->created_at,
                'category_name' => $post->category?->name,
                'author_name' => $post->user?->name,
                'thumb' => $post->media->first()?->getUrl(),
            ]);

        $stats = [
            'total_posts' => Post::count(),
            'verified_users' => User::where('is_verified', true)->count(),
            'total_categories' => Category::count(),
        ];

        $popularTags = Tag::withCount('posts')
            ->whereHas('posts')
            ->orderByDesc('posts_count')
            ->take(8)
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Board/Index', [
            'categories' => $categories,
            'recentPosts' => $recentPosts,
            'stats' => $stats,
            'popularTags' => $popularTags,
            'filters' => ['location' => $city],
        ]);
    }
}
