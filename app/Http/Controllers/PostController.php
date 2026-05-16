<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategorySuggestion;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            ->select('posts.*')
            ->where(function ($q) {
                $q->whereNull('posts.expires_at')
                    ->orWhere('posts.expires_at', '>', now());
            });

        if ($city) {
            if (is_numeric($city)) {
                $postsQuery->selectRaw('(posts.zip_code = ?) as is_local', [$city])
                    ->orderByRaw('CASE WHEN posts.zip_code = ? THEN 0 ELSE 1 END', [$city]);
                
                $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
                if ($driver === 'pgsql') {
                    $postsQuery->orderByRaw('ABS(CAST(NULLIF(regexp_replace(posts.zip_code, \'[^0-9]\', \'\', \'g\'), \'\') AS INTEGER) - ?) ASC', [(int) $city]);
                } elseif ($driver === 'mysql') {
                    $postsQuery->orderByRaw('ABS(CAST(NULLIF(posts.zip_code, \'\') AS SIGNED) - ?) ASC', [(int) $city]);
                } else {
                    $postsQuery->orderByRaw('ABS(CAST(NULLIF(posts.zip_code, \'\') AS INTEGER) - ?) ASC', [(int) $city]);
                }
            } else {
                $postsQuery->selectRaw('(LOWER(posts.city) = LOWER(?)) as is_local', [$city])
                    ->orderByRaw('CASE WHEN LOWER(posts.city) = LOWER(?) THEN 0 ELSE 1 END', [$city]);
            }
        } else {
            $postsQuery->selectRaw('1 as is_local');
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $postsQuery->where('posts.category_id', $request->category_id);
        }

        if ($request->has('tag')) {
            $tagSlug = $request->query('tag');
            $postsQuery->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $postsQuery->where(function ($q) use ($search) {
                $q->where('posts.title', 'like', "%{$search}%")
                    ->orWhere('posts.description', 'like', "%{$search}%");
            });
        }

        $posts = $postsQuery->latest()->paginate(15)->withQueryString();

        return Inertia::render('Board/Feed', [
            'categories' => $categories,
            'posts' => $posts,
            'filters' => array_merge($request->only(['category_id', 'search', 'tag']), ['location' => $city]),
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
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
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

        if (! empty($validated['tags'])) {
            $tagIds = collect($validated['tags'])->map(function ($tagName) {
                return Tag::firstOrCreate([
                    'slug' => Str::slug($tagName),
                ], [
                    'name' => $tagName,
                ])->id;
            });
            $post->tags()->sync($tagIds);
        }

        if (! empty($validated['suggested_category_name'])) {
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
        abort_if($post->user_id !== $request->user()->id && ! $request->user()->hasRole('Super Admin'), 403, 'Unauthorized');

        $categories = Category::orderBy('name')->get();

        $post->load(['media', 'tags']);

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
                'tags' => $post->tags->pluck('name')->toArray(),
                'images' => $mediaUrls,
            ],
        ]);
    }

    public function update(Request $request, Post $post)
    {
        abort_if($post->user_id !== $request->user()->id && ! $request->user()->hasRole('Super Admin'), 403, 'Unauthorized');

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'city' => 'required|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'meta' => 'nullable|array',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|max:5120',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'integer',
        ]);

        $post->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'city' => $validated['city'],
            'zip_code' => $validated['zip_code'] ?? null,
            'meta' => $validated['meta'] ?? null,
        ]);

        if (! empty($validated['deleted_images'])) {
            $post->media()->whereIn('id', $validated['deleted_images'])->delete();
        }

        if ($request->hasFile('images')) {
            $currentCount = $post->media()->count();
            foreach ($request->file('images') as $image) {
                if ($currentCount < 3) {
                    $post->addMedia($image)->toMediaCollection('images');
                    $currentCount++;
                }
            }
        }

        if (isset($validated['tags'])) {
            $tagIds = collect($validated['tags'])->map(function ($tagName) {
                return Tag::firstOrCreate([
                    'slug' => Str::slug($tagName),
                ], [
                    'name' => $tagName,
                ])->id;
            });
            $post->tags()->sync($tagIds);
        } else {
            $post->tags()->detach();
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully!');
    }

    public function destroy(Request $request, Post $post)
    {
        abort_if($post->user_id !== $request->user()->id && ! $request->user()->hasRole('Super Admin'), 403, 'Unauthorized');

        $post->delete();

        return redirect()->route('feed')->with('success', 'Post deleted successfully.');
    }

    public function show(Request $request, Post $post)
    {
        $post->load(['user', 'category', 'media', 'tags']);

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
            'tags' => $post->tags->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'slug' => $t->slug]),
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
                $q->whereNull('posts.expires_at')
                    ->orWhere('posts.expires_at', '>', now());
            })
            ->where(function ($q) use ($query) {
                $q->where('posts.title', 'like', "%{$query}%")
                    ->orWhere('posts.description', 'like', "%{$query}%");
            })
            ->latest()
            ->take(8)
            ->get(['id', 'title', 'category_id', 'city', 'created_at']);

        return response()->json($posts);
    }
}
