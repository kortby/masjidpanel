<?php

use App\Models\BannedIdentifier;
use App\Models\User;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\get;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin']);
});

it('allows super admin to block a user', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $user = User::factory()->create(['email' => 'baduser@example.com', 'device_id' => 'device-123']);

    actingAs($admin)
        ->post(route('admin.users.block', $user))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh()->banned_at)->not->toBeNull();
    
    expect(BannedIdentifier::where('value', 'baduser@example.com')->exists())->toBeTrue();
    expect(BannedIdentifier::where('value', 'device-123')->exists())->toBeTrue();
});

it('prevents a blocked user from registering again with the same email or device_id', function () {
    BannedIdentifier::create(['type' => 'email', 'value' => 'banned@example.com']);
    BannedIdentifier::create(['type' => 'device_cookie', 'value' => 'banned-device']);

    // Try same email
    post('/register', [
        'name' => 'Test User',
        'email' => 'banned@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'language' => 'en',
    ])->assertSessionHasErrors('email');

    // Try different email but same device cookie
    $test = test();
    $test->withCookie('device_id', 'banned-device')
        ->post('/register', [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'language' => 'en',
        ])->assertSessionHasErrors('email');
});

it('allows super admin to unblock a user', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $user = User::factory()->create(['banned_at' => now(), 'email' => 'blocked@example.com']);
    BannedIdentifier::create(['type' => 'email', 'value' => 'blocked@example.com', 'user_id' => $user->id]);

    actingAs($admin)
        ->post(route('admin.users.unblock', $user))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh()->banned_at)->toBeNull();
    expect(BannedIdentifier::where('value', 'blocked@example.com')->exists())->toBeFalse();
});

it('logs out a banned user immediately via middleware', function () {
    $user = User::factory()->create(['banned_at' => now()]);

    actingAs($user)
        ->get('/feed')
        ->assertRedirect('/login')
        ->assertSessionHasErrors('email');

    expect(auth()->check())->toBeFalse();
});
