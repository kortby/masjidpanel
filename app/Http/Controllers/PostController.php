<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategorySuggestion;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $city = $request->session()->get('location');

        $categoryId = $request->query('category_id');

        $categories = Category::orderedByLocationDemand($city)->get();

        $postsQuery = Post::with(['user', 'category', 'media'])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });

        if ($city) {
            $postsQuery->where('city', $city);
        }

        if ($request->has('category_id')) {
            $postsQuery->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $postsQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $posts = $postsQuery->latest()->paginate(15)->withQueryString();

        return Inertia::render('Board/Feed', [
            'categories' => $categories,
            'posts' => $posts,
            'filters' => array_merge($request->only(['category_id', 'search']), ['location' => $city]),
        ]);
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return Inertia::render('Posts/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city' => 'required|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'suggested_category_name' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|max:5120',
        ]);

        $post = Post::create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'city' => $validated['city'],
            'zip_code' => $validated['zip_code'] ?? null,
            'meta' => $validated['meta'] ?? null,
            'expires_at' => now()->addDays(30),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $post->addMedia($image)->toMediaCollection('images');
            }
        }

        if (!empty($validated['suggested_category_name'])) {
            CategorySuggestion::create([
                'user_id' => $request->user()->id,
                'post_id' => $post->id,
                'suggested_name' => $validated['suggested_category_name'],
            ]);
        }

        return redirect()->route('home')->with('success', 'Post created successfully!');
    }

    public function show(Request $request, Post $post)
    {
        $post->load(['user', 'category', 'media']);

        $user = $request->user();
        $isVerified = $user && $user->is_verified;

        $author = $post->user;
        $contactInfo = null;

        if ($isVerified) {
            if ($author->show_phone && $author->phone_number) {
                $contactInfo['phone'] = $author->phone_number;
            }
            if ($author->show_email) {
                $contactInfo['email'] = $author->email;
            }
        }

        $mediaUrls = $post->getMedia('images')->map(function ($media) {
            return [
                'url' => $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
            ];
        });

        $postData = [
            'id' => $post->id,
            'title' => $post->title,
            'description' => $post->description,
            'city' => $post->city,
            'zip_code' => $post->zip_code,
            'meta' => $post->meta,
            'created_at' => $post->created_at,
            'category' => $post->category->only('id', 'name'),
            'author_id' => $author->id,
            'author_name' => $author->name,
            'contact_info' => $contactInfo,
            'images' => $mediaUrls,
            'is_author' => $user && $user->id === $author->id,
            'has_public_contact' => $author->show_phone || $author->show_email,
        ];

        return Inertia::render('Posts/Show', [
            'post' => $postData,
            'isVerified' => $isVerified,
        ]);
    }
}
