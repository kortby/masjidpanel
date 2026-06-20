<?php

use App\Models\Category;
use App\Models\CategorySuggestion;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('blocks standard users from accessing admin routes', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin/dashboard');

    $response->assertStatus(403);
});

it('allows super admins to access admin dashboard', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
});

it('approves a category suggestion and reassigns the post', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $category = Category::create(['name' => 'Other', 'slug' => 'other']);

    $post = Post::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Test Post',
        'description' => 'Test',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(30),
    ]);

    $suggestion = CategorySuggestion::create([
        'user_id' => $admin->id,
        'post_id' => $post->id,
        'suggested_name' => 'Tutoring',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post("/admin/suggestions/{$suggestion->id}/approve");

    $response->assertSessionHas('success');

    $newCategory = Category::where('name', 'Tutoring')->first();
    expect($newCategory)->not->toBeNull();

    $post->refresh();
    expect($post->category_id)->toBe($newCategory->id);

    $suggestion->refresh();
    expect($suggestion->status)->toBe('approved');
});

it('allows super admin to view posts list in dashboard', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $category = Category::create(['name' => 'Other', 'slug' => 'other']);
    $post = Post::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Admin Test Post',
        'description' => 'Test',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(30),
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Admin/Dashboard')
        ->has('posts.data')
        ->where('posts.data.0.title', 'Admin Test Post')
        ->where('posts.data.0.user.name', $admin->name)
    );
});

it('allows super admin to delete posts using admin route', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $category = Category::create(['name' => 'Other', 'slug' => 'other']);
    $post = Post::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Admin Test Post',
        'description' => 'Test',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(30),
    ]);

    $response = $this->actingAs($admin)->delete("/admin/posts/{$post->id}");

    $response->assertRedirect();
    expect(Post::find($post->id))->toBeNull();
});

it('blocks non-admin users from deleting posts using admin route', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $user = User::factory()->create();

    $category = Category::create(['name' => 'Other', 'slug' => 'other']);
    $post = Post::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Admin Test Post',
        'description' => 'Test',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(30),
    ]);

    $response = $this->actingAs($user)->delete("/admin/posts/{$post->id}");

    $response->assertStatus(403);
    expect(Post::find($post->id))->not->toBeNull();
});

it('soft-deletes posts when deleted by standard user', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Other', 'slug' => 'other']);
    $post = Post::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'User Post To Delete',
        'description' => 'Test',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(30),
    ]);

    $response = $this->actingAs($user)->delete("/posts/{$post->id}");

    $response->assertRedirect();
    $post->refresh();
    expect($post->trashed())->toBeTrue();
    expect(Post::find($post->id))->toBeNull(); // Standard find excludes trashed
});

it('hides soft-deleted posts from standard users', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $user = User::factory()->create();
    $category = Category::create(['name' => 'Other', 'slug' => 'other']);
    $post = Post::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Trashed Post',
        'description' => 'Test',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(30),
    ]);
    $post->delete(); // Soft delete it

    // Standard user gets 404
    $response = $this->actingAs($user)->get("/posts/{$post->id}");
    $response->assertStatus(404);
});

it('allows super admin to view soft-deleted posts', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $category = Category::create(['name' => 'Other', 'slug' => 'other']);
    $post = Post::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Trashed Post',
        'description' => 'Test',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(30),
    ]);
    $post->delete(); // Soft delete it

    $response = $this->actingAs($admin)->get("/posts/{$post->id}");
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Posts/Show')
        ->where('post.title', 'Trashed Post')
    );
});

it('allows super admin to permanently delete soft-deleted posts', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $category = Category::create(['name' => 'Other', 'slug' => 'other']);
    $post = Post::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'title' => 'Trashed Post',
        'description' => 'Test',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(30),
    ]);
    $post->delete(); // Soft delete it

    // Verify it is in database with trashed
    expect(Post::withTrashed()->find($post->id))->not->toBeNull();

    $response = $this->actingAs($admin)->delete("/admin/posts/{$post->id}");

    $response->assertRedirect();
    expect(Post::withTrashed()->find($post->id))->toBeNull(); // Force deleted
});

it('allows messaging even if public contact info is shown', function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
    $user = User::factory()->create(['is_verified' => true]);

    // Author with show_phone = true
    $author = User::factory()->create([
        'is_verified' => true,
        'show_phone' => true,
        'phone_number' => '+15555555555',
    ]);

    $category = Category::create(['name' => 'Other', 'slug' => 'other']);
    $post = Post::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'title' => 'Public Contact Post',
        'description' => 'Test',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(30),
    ]);

    // Send a message
    $response = $this->actingAs($user)->post("/posts/{$post->id}/message", [
        'message' => 'This is a test message to the author.',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    expect($post->messages()->count())->toBe(1);
});
