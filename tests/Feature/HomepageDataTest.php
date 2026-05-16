<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('homepage returns categories, recentPosts, stats, and popularTags', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Jobs & Hiring', 'slug' => 'jobs-hiring']);

    Post::create([
        'title' => 'Test Job',
        'description' => 'A test job post',
        'city' => 'Austin',
        'category_id' => $category->id,
        'user_id' => $user->id,
        'expires_at' => now()->addDays(30),
    ]);

    $tag = Tag::create(['name' => 'Remote', 'slug' => 'remote']);
    Post::first()->tags()->attach($tag->id);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Board/Index')
        ->has('categories')
        ->has('recentPosts')
        ->has('stats')
        ->has('popularTags')
        ->where('stats.total_posts', 1)
        ->where('stats.verified_users', 1)
        ->where('stats.total_categories', 1)
    );
});

it('homepage recentPosts contain expected post data', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Housing', 'slug' => 'housing']);

    Post::create([
        'title' => 'Room for Rent in Austin',
        'description' => 'Nice room near campus',
        'city' => 'Austin',
        'category_id' => $category->id,
        'user_id' => $user->id,
        'expires_at' => now()->addDays(30),
    ]);

    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->has('recentPosts', 1)
        ->where('recentPosts.0.title', 'Room for Rent in Austin')
        ->where('recentPosts.0.city', 'Austin')
        ->where('recentPosts.0.category_name', 'Housing')
        ->where('recentPosts.0.author_name', $user->name)
    );
});

it('homepage recentPosts excludes expired posts', function () {
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Test', 'slug' => 'test']);

    Post::create([
        'title' => 'Active Post',
        'description' => 'Still valid',
        'city' => 'Austin',
        'category_id' => $category->id,
        'user_id' => $user->id,
        'expires_at' => now()->addDays(10),
    ]);

    Post::create([
        'title' => 'Expired Post',
        'description' => 'No longer valid',
        'city' => 'Austin',
        'category_id' => $category->id,
        'user_id' => $user->id,
        'expires_at' => now()->subDays(1),
    ]);

    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->has('recentPosts', 1)
        ->where('recentPosts.0.title', 'Active Post')
    );
});

it('homepage popularTags only includes tags with posts', function () {
    $orphanTag = Tag::create(['name' => 'Unused', 'slug' => 'unused']);
    $usedTag = Tag::create(['name' => 'Popular', 'slug' => 'popular']);

    $user = User::factory()->create();
    $category = Category::create(['name' => 'Test', 'slug' => 'test']);
    $post = Post::create([
        'title' => 'Test',
        'description' => 'Test',
        'city' => 'Austin',
        'category_id' => $category->id,
        'user_id' => $user->id,
        'expires_at' => now()->addDays(30),
    ]);
    $post->tags()->attach($usedTag->id);

    $response = $this->get('/');

    $response->assertInertia(fn ($page) => $page
        ->has('popularTags', 1)
        ->where('popularTags.0.name', 'Popular')
    );
});

it('feed returns popularTags prop', function () {
    $tag = Tag::create(['name' => 'Urgent', 'slug' => 'urgent']);
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Test', 'slug' => 'test']);
    $post = Post::create([
        'title' => 'Test',
        'description' => 'Test',
        'city' => 'Austin',
        'category_id' => $category->id,
        'user_id' => $user->id,
        'expires_at' => now()->addDays(30),
    ]);
    $post->tags()->attach($tag->id);

    $response = $this->get('/feed');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Board/Feed')
        ->has('popularTags', 1)
        ->where('popularTags.0.name', 'Urgent')
    );
});
