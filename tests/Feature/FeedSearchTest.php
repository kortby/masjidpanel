<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('loads feed with no filters', function () {
    $response = $this->get('/feed');
    $response->assertStatus(200);
});

it('loads feed with location zipcode', function () {
    $response = $this->get('/feed?location=92123');
    $response->assertStatus(200);
});

it('loads feed with location city', function () {
    $response = $this->get('/feed?location=Seattle');
    $response->assertStatus(200);
});

it('loads feed with search query', function () {
    $this->seed();
    $response = $this->get('/feed?search=test');
    $response->assertStatus(200);
});

it('loads feed with category id', function () {
    $this->seed();
    $response = $this->get('/feed?category_id=1');
    $response->assertStatus(200);
});

it('loads feed with tag', function () {
    $this->seed();
    $response = $this->get('/feed?tag=test-tag');
    $response->assertStatus(200);
});

it('loads feed with location zipcode and search query', function () {
    $this->seed();
    $response = $this->get('/feed?location=92123&search=foo');
    $response->assertStatus(200);
});

it('loads feed with location zipcode, search query, and category id', function () {
    $this->seed();
    $response = $this->get('/feed?location=92123&search=foo&category_id=1');
    $response->assertStatus(200);
});

it('handles empty string parameters gracefully without 500 errors', function () {
    $this->seed();
    $response = $this->get('/feed?location=&search=&category_id=&tag=');
    $response->assertStatus(200);
});

it('handles location zipcode when database contains empty string zip codes', function () {
    $this->seed();
    // Simulate empty string zip_code which causes invalid input syntax in PostgreSQL
    $post = Post::first();
    $post->zip_code = '';
    $post->save();

    $response = $this->get('/feed?location=92123&search=foo');
    $response->assertStatus(200);
});

it('handles location zipcode when database contains non-numeric zip codes', function () {
    $this->seed();
    // Simulate non-numeric string zip_code which causes invalid input syntax in PostgreSQL
    $post = Post::first();
    $post->zip_code = '92101-1234';
    $post->save();

    $response = $this->get('/feed?location=92123&search=foo');
    $response->assertStatus(200);
});

