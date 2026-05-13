<?php

use App\Mail\PostMessageRelay;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('blocks unverified users from sending messages', function () {
    $author = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Jobs', 'slug' => 'jobs']);
    $post = Post::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'title' => 'Test Post',
        'description' => 'Test Desc',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(10),
    ]);

    $unverifiedUser = User::factory()->create(['is_verified' => false]);

    $response = $this->actingAs($unverifiedUser)
        ->post("/posts/{$post->id}/message", [
            'message' => 'Hello this is a valid message length!',
        ]);

    $response->assertRedirect('/checkout');
});

it('allows verified users to send messages and dispatches mail', function () {
    Mail::fake();

    $author = User::factory()->create(['is_verified' => true]);
    $category = Category::create(['name' => 'Jobs', 'slug' => 'jobs']);
    $post = Post::create([
        'user_id' => $author->id,
        'category_id' => $category->id,
        'title' => 'Test Post',
        'description' => 'Test Desc',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(10),
    ]);

    $verifiedUser = User::factory()->create(['is_verified' => true]);

    $response = $this->actingAs($verifiedUser)
        ->from("/posts/{$post->id}")
        ->post("/posts/{$post->id}/message", [
            'message' => 'Hello this is a valid message length!',
        ]);

    $response->assertRedirect("/posts/{$post->id}");
    $response->assertSessionHas('success');

    Mail::assertQueued(PostMessageRelay::class, function ($mail) use ($author, $verifiedUser) {
        return $mail->hasTo($author->email) &&
               $mail->sender->id === $verifiedUser->id;
    });
});
