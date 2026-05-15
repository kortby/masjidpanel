<?php

use App\Models\User;

test('profile settings can update language', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/settings/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'language' => 'ar',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $user->refresh();

    expect($user->language)->toBe('ar');
});

test('profile language is applied to application locale', function () {
    $user = User::factory()->create(['language' => 'es']);

    $response = $this
        ->actingAs($user)
        ->get('/'); // Or any other route that uses the web middleware

    $response->assertOk();

    expect(app()->getLocale())->toBe('es');
});
