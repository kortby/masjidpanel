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
