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
        $locationFilter = $request->query('location');
        if ($locationFilter !== null) {
            $request->session()->put('location', $locationFilter);
            $city = $locationFilter;
        } else {
            $city = $request->session()->get('location');
        }

        $categoryId = $request->query('category_id');

        $categories = Category::orderedByLocationDemand($city)->get();

        $postsQuery = Post::with(['user', 'category', 'media'])
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });

        if ($city) {
            if (is_numeric($city)) {
                $postsQuery->orderByRaw('CASE WHEN zip_code IS NULL OR zip_code = "" THEN 1 ELSE 0 END ASC')
                           ->orderByRaw('ABS(CAST(zip_code AS INTEGER) - ?) ASC', [(int)$city]);
            } else {
                $postsQuery->where('city', $city);
            }
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

        return redirect()->route('posts.show', $post)->with('success', 'Post created successfully!');
    }

    public function edit(Request $request, Post $post)
    {
        abort_if($post->user_id !== $request->user()->id && !$request->user()->hasRole('Super Admin'), 403, 'Unauthorized');

        $categories = Category::orderBy('name')->get();

        $post->load('media');

        $mediaUrls = $post->getMedia('images')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
            ];
        });

        return Inertia::render('Posts/Edit', [
            'categories' => $categories,
            'post' => [
                'id' => $post->id,
                'category_id' => $post->category_id,
                'title' => $post->title,
                'description' => $post->description,
                'city' => $post->city,
                'zip_code' => $post->zip_code,
                'meta' => $post->meta,
                'images' => $mediaUrls,
            ],
        ]);
    }

    public function update(Request $request, Post $post)
    {
        abort_if($post->user_id !== $request->user()->id && !$request->user()->hasRole('Super Admin'), 403, 'Unauthorized');

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city' => 'required|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'meta' => 'nullable|array',
        ]);

        $post->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'city' => $validated['city'],
            'zip_code' => $validated['zip_code'] ?? null,
            'meta' => $validated['meta'] ?? null,
        ]);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully!');
    }

    public function destroy(Request $request, Post $post)
    {
        abort_if($post->user_id !== $request->user()->id && !$request->user()->hasRole('Super Admin'), 403, 'Unauthorized');

        $post->delete();

        return redirect()->route('feed')->with('success', 'Post deleted successfully.');
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

    public function search(Request $request)
    {
        $query = $request->query('q', '');
        
        if (empty(trim($query))) {
            return response()->json([]);
        }

        $posts = Post::with('category')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->latest()
            ->take(8)
            ->get(['id', 'title', 'category_id', 'city', 'created_at']);

        return response()->json($posts);
    }
}
