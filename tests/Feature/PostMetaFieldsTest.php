<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores post with meta fields', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Jobs & Hiring', 'slug' => 'jobs-hiring']);

    $response = $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => 'Senior Developer Needed',
        'description' => 'Looking for a senior developer',
        'city' => 'Seattle',
        'zip_code' => '98101',
        'meta' => [
            'job_type' => 'Full-Time',
            'salary' => '$120k/year',
            'company' => 'Acme Corp',
        ],
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $post = Post::latest()->first();
    expect($post->meta)->toBeArray();
    expect($post->meta['job_type'])->toBe('Full-Time');
    expect($post->meta['salary'])->toBe('$120k/year');
    expect($post->meta['company'])->toBe('Acme Corp');
});

it('stores post with housing meta fields', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Housing & Roommates', 'slug' => 'housing-roommates']);

    $response = $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => '2BR Apartment Available',
        'description' => 'Nice apartment near downtown',
        'city' => 'Austin',
        'meta' => [
            'listing_type' => 'Apartment for Rent',
            'price' => '$1200/month',
            'bedrooms' => '2 BR',
            'furnished' => 'Yes',
        ],
    ]);

    $response->assertRedirect();
    $post = Post::latest()->first();
    expect($post->meta['listing_type'])->toBe('Apartment for Rent');
    expect($post->meta['price'])->toBe('$1200/month');
    expect($post->meta['furnished'])->toBe('Yes');
});

it('stores post with tags that get created automatically', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Buy, Sell & Give Away', 'slug' => 'buy-sell']);

    $response = $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => 'iPhone for Sale',
        'description' => 'Selling my iPhone',
        'city' => 'Dallas',
        'meta' => [
            'condition' => 'Like New',
            'price' => '$500',
            'negotiable' => 'Yes',
        ],
        'tags' => ['Used', 'Discount', 'OBO'],
    ]);

    $response->assertRedirect();

    $post = Post::with('tags')->latest()->first();
    expect($post->tags)->toHaveCount(3);
    expect($post->tags->pluck('name')->sort()->values()->all())->toBe(['Discount', 'OBO', 'Used']);

    // Tags should be created in the tags table
    expect(Tag::where('slug', 'used')->exists())->toBeTrue();
    expect(Tag::where('slug', 'discount')->exists())->toBeTrue();
    expect(Tag::where('slug', 'obo')->exists())->toBeTrue();
});

it('reuses existing tags instead of creating duplicates', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Test', 'slug' => 'test']);

    // Pre-create a tag
    Tag::create(['name' => 'Urgent', 'slug' => 'urgent']);

    $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => 'Test Post',
        'description' => 'Test description',
        'city' => 'Seattle',
        'tags' => ['Urgent', 'New Tag'],
    ]);

    // 'Urgent' should not be duplicated
    expect(Tag::where('slug', 'urgent')->count())->toBe(1);
    expect(Tag::count())->toBe(2); // Urgent + New Tag
});

it('stores post with empty meta when no category-specific fields provided', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Other', 'slug' => 'other']);

    $response = $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => 'General Post',
        'description' => 'A post without meta fields',
        'city' => 'Chicago',
    ]);

    $response->assertRedirect();
    $post = Post::latest()->first();
    expect($post->meta)->toBeNull();
});

it('stores post with marriage meta fields', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Marriage & Matrimony', 'slug' => 'marriage-matrimony']);

    $response = $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => 'Looking for a spouse',
        'description' => 'Practicing Muslim looking for marriage',
        'city' => 'Houston',
        'meta' => [
            'looking_for' => 'Wife',
            'age' => '28',
            'ethnicity' => 'South Asian',
            'marital_status' => 'Never Married',
        ],
    ]);

    $response->assertRedirect();
    $post = Post::latest()->first();
    expect($post->meta['looking_for'])->toBe('Wife');
    expect($post->meta['age'])->toBe('28');
    expect($post->meta['marital_status'])->toBe('Never Married');
});

it('stores post with rideshare meta fields', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Rideshare & Carpool', 'slug' => 'rideshare-carpool']);

    $response = $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => 'Dallas to Houston ride',
        'description' => 'Need a ride this weekend',
        'city' => 'Dallas',
        'meta' => [
            'ride_type' => 'Looking for a Ride',
            'from_location' => 'Dallas, TX',
            'to_location' => 'Houston, TX',
            'travel_date' => 'May 20, 2026',
        ],
    ]);

    $response->assertRedirect();
    $post = Post::latest()->first();
    expect($post->meta['ride_type'])->toBe('Looking for a Ride');
    expect($post->meta['from_location'])->toBe('Dallas, TX');
    expect($post->meta['to_location'])->toBe('Houston, TX');
});

it('displays meta fields on the post show page', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Jobs & Hiring', 'slug' => 'jobs-hiring']);

    $post = Post::create([
        'title' => 'Developer Job',
        'description' => 'Looking for a developer',
        'city' => 'Austin',
        'category_id' => $category->id,
        'user_id' => $user->id,
        'meta' => ['job_type' => 'Full-Time', 'salary' => '$100k'],
        'expires_at' => now()->addDays(30),
    ]);

    $response = $this->get("/posts/{$post->id}");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Posts/Show')
        ->where('post.meta.job_type', 'Full-Time')
        ->where('post.meta.salary', '$100k')
    );
});
