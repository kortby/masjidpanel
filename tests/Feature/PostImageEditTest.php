<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('users can add images to an existing post up to the limit', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Services', 'slug' => 'services', 'description' => 'test']);
    $post = Post::create(['user_id' => $user->id, 'category_id' => $category->id, 'title' => 'test', 'description' => 'test', 'city' => 'test']);

    $this->actingAs($user);

    $file1 = UploadedFile::fake()->image('photo1.jpg');
    $file2 = UploadedFile::fake()->image('photo2.jpg');

    $response = $this->put(route('posts.update', $post), [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'category_id' => $category->id,
        'city' => 'New York',
        'images' => [$file1, $file2],
        'deleted_images' => [],
    ]);

    $response->assertRedirect(route('posts.show', $post));
    
    // Assert 2 media files are attached
    expect($post->getMedia('images')->count())->toBe(2);
});

test('users can delete existing images and upload new ones simultaneously', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Services', 'slug' => 'services', 'description' => 'test']);
    $post = Post::create(['user_id' => $user->id, 'category_id' => $category->id, 'title' => 'test', 'description' => 'test', 'city' => 'test']);

    // Attach an initial image
    $post->addMedia(UploadedFile::fake()->image('old_photo.jpg'))->toMediaCollection('images');
    
    expect($post->getMedia('images')->count())->toBe(1);
    
    $mediaId = $post->getMedia('images')->first()->id;

    $this->actingAs($user);

    $newFile = UploadedFile::fake()->image('new_photo.jpg');

    $response = $this->put(route('posts.update', $post), [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'category_id' => $category->id,
        'city' => 'New York',
        'images' => [$newFile],
        'deleted_images' => [$mediaId],
    ]);

    $response->assertRedirect(route('posts.show', $post));
    
    $post->refresh();
    
    // Should still be 1 total because we deleted 1 and added 1
    expect($post->getMedia('images')->count())->toBe(1);
    
    // The new ID should not match the old one
    expect($post->getMedia('images')->first()->id)->not->toBe($mediaId);
});

test('backend caps total images at 3 when updating', function () {
    $user = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Services', 'slug' => 'services', 'description' => 'test']);
    $post = Post::create(['user_id' => $user->id, 'category_id' => $category->id, 'title' => 'test', 'description' => 'test', 'city' => 'test']);

    // Attach 2 initial images
    $post->addMedia(UploadedFile::fake()->image('old_1.jpg'))->toMediaCollection('images');
    $post->addMedia(UploadedFile::fake()->image('old_2.jpg'))->toMediaCollection('images');

    $this->actingAs($user);

    // Try to add 2 more without deleting any (total would be 4, max is 3)
    $response = $this->put(route('posts.update', $post), [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'category_id' => $category->id,
        'city' => 'New York',
        'images' => [
            UploadedFile::fake()->image('new_1.jpg'),
            UploadedFile::fake()->image('new_2.jpg'),
        ],
        'deleted_images' => [],
    ]);

    $response->assertRedirect(route('posts.show', $post));
    
    $post->refresh();
    // Only 1 of the 2 new ones should be added, capping total at 3
    expect($post->getMedia('images')->count())->toBe(3);
});
