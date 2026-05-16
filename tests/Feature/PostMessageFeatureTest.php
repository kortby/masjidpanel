<?php

use App\Mail\PostCreated;
use App\Mail\PostMessageRelay;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('sends an email to the author when a post is created', function () {
    $this->seed();
    Mail::fake();

    $user = User::first();
    $user->update(['is_verified' => true]);
    $category = Category::first();

    $this->actingAs($user)->post('/posts', [
        'category_id' => $category->id,
        'title' => 'My New Post',
        'description' => 'Test description',
        'city' => 'San Diego',
        'zip_code' => '92123',
    ]);

    Mail::assertSent(PostCreated::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && $mail->post->title === 'My New Post';
    });
});

it('saves a message to the database and emails the author', function () {
    $this->seed();
    Mail::fake();

    $post = Post::first();
    $author = $post->user;
    $sender = User::where('id', '!=', $author->id)->first();
    $sender->update(['is_verified' => true]);

    $this->actingAs($sender)->post("/posts/{$post->id}/message", [
        'message' => 'Hello, I am interested in this post.',
    ]);

    $this->assertDatabaseHas('post_messages', [
        'post_id' => $post->id,
        'sender_id' => $sender->id,
        'message' => 'Hello, I am interested in this post.',
    ]);

    Mail::assertQueued(PostMessageRelay::class, function ($mail) use ($author) {
        return $mail->hasTo($author->email);
    });
});

it('allows the author to see their post messages', function () {
    $this->seed();
    $post = Post::first();
    $author = $post->user;
    $author->update(['is_verified' => true]);
    $sender = User::where('id', '!=', $author->id)->first();

    $post->messages()->create([
        'sender_id' => $sender->id,
        'message' => 'Secret message to author',
    ]);

    $this->actingAs($author)->get("/posts/{$post->id}")
        ->assertInertia(fn (Assert $page) => $page
            ->has('post.messages', 1)
            ->where('post.messages.0.message', 'Secret message to author')
        );
});

it('does not allow non-authors to see post messages', function () {
    $this->seed();
    $post = Post::first();
    $author = $post->user;
    $author->update(['is_verified' => true]);
    $sender = User::where('id', '!=', $author->id)->first();
    $otherUser = User::whereNotIn('id', [$author->id, $sender->id])->first();
    $otherUser->update(['is_verified' => true]);

    $post->messages()->create([
        'sender_id' => $sender->id,
        'message' => 'Secret message to author',
    ]);

    $this->actingAs($otherUser)->get("/posts/{$post->id}")
        ->assertInertia(fn (Assert $page) => $page
            ->missing('post.messages')
        );
});
