<?php

use App\Models\Category;
use App\Models\User;

test('post creation rejects invalid zip codes', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Test', 'slug' => 'test']);

    $response = $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => 'Test Title',
        'description' => 'Test Description',
        'city' => 'Seattle',
        'zip_code' => '94', // Invalid 2-digit zip code
    ]);

    $response->assertSessionHasErrors(['zip_code']);
});

test('post creation accepts valid zip codes', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Test 2', 'slug' => 'test-2']);

    $response = $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => 'Test Title',
        'description' => 'Test Description',
        'city' => 'Seattle',
        'zip_code' => '92123', // Valid 5-digit zip code
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('posts', ['zip_code' => '92123']);
});
