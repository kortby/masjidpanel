<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('dynamically sorts categories by demand based on location', function () {
    $user = User::factory()->create();
    
    // Create categories
    $catJobs = Category::create(['name' => 'Jobs', 'slug' => 'jobs']);
    $catHousing = Category::create(['name' => 'Housing', 'slug' => 'housing']);
    $catEvents = Category::create(['name' => 'Events', 'slug' => 'events']);

    // Seed Posts in Seattle (Jobs: 3, Housing: 1, Events: 0)
    for ($i = 0; $i < 3; $i++) {
        Post::create([
            'user_id' => $user->id,
            'category_id' => $catJobs->id,
            'title' => 'Job ' . $i,
            'description' => 'Desc',
            'city' => 'Seattle',
            'expires_at' => now()->addDays(10),
        ]);
    }
    
    Post::create([
        'user_id' => $user->id,
        'category_id' => $catHousing->id,
        'title' => 'Housing 1',
        'description' => 'Desc',
        'city' => 'Seattle',
        'expires_at' => now()->addDays(10),
    ]);

    // Seed Posts in Portland (Events: 2, Jobs: 1, Housing: 0)
    for ($i = 0; $i < 2; $i++) {
        Post::create([
            'user_id' => $user->id,
            'category_id' => $catEvents->id,
            'title' => 'Event ' . $i,
            'description' => 'Desc',
            'city' => 'Portland',
            'expires_at' => now()->addDays(10),
        ]);
    }
    
    Post::create([
        'user_id' => $user->id,
        'category_id' => $catJobs->id,
        'title' => 'Job Portland',
        'description' => 'Desc',
        'city' => 'Portland',
        'expires_at' => now()->addDays(10),
    ]);

    // Query for Seattle
    $seattleCategories = Category::orderedByLocationDemand('Seattle')->get();
    
    // Jobs should be first (3 posts), Housing second (1 post), Events third (0 posts)
    expect($seattleCategories[0]->id)->toBe($catJobs->id)
        ->and($seattleCategories[0]->posts_count)->toBe(3)
        ->and($seattleCategories[1]->id)->toBe($catHousing->id)
        ->and($seattleCategories[1]->posts_count)->toBe(1)
        ->and($seattleCategories[2]->id)->toBe($catEvents->id)
        ->and($seattleCategories[2]->posts_count)->toBe(0);

    // Query for Portland
    $portlandCategories = Category::orderedByLocationDemand('Portland')->get();
    
    // Events should be first (2 posts), Jobs second (1 post), Housing third (0 posts)
    expect($portlandCategories[0]->id)->toBe($catEvents->id)
        ->and($portlandCategories[0]->posts_count)->toBe(2)
        ->and($portlandCategories[1]->id)->toBe($catJobs->id)
        ->and($portlandCategories[1]->posts_count)->toBe(1)
        ->and($portlandCategories[2]->id)->toBe($catHousing->id)
        ->and($portlandCategories[2]->posts_count)->toBe(0);
});
