<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('login'));
});

test('non-admin users are forbidden from the dashboard', function () {
    $this->seed(RoleSeeder::class);
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('admin.dashboard'));
    $response->assertForbidden();
});

test('admin users can visit the dashboard', function () {
    $this->seed(RoleSeeder::class);
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');
    $this->actingAs($admin);

    $response = $this->get(route('admin.dashboard'));
    $response->assertOk();
});
