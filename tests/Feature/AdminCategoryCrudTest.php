<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAdmin(): User
{
    (new RoleSeeder)->run();
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    return $admin;
}

it('can create a category', function () {
    $admin = createAdmin();
    $this->actingAs($admin);

    $response = $this->post('/admin/categories', ['name' => 'Electronics']);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'name' => 'Electronics',
        'slug' => 'electronics',
    ]);
});

it('validates category name is required', function () {
    $admin = createAdmin();
    $this->actingAs($admin);

    $response = $this->post('/admin/categories', ['name' => '']);

    $response->assertSessionHasErrors('name');
});

it('validates category name is unique on create', function () {
    $admin = createAdmin();
    $this->actingAs($admin);

    Category::create(['name' => 'Jobs', 'slug' => 'jobs']);

    $response = $this->post('/admin/categories', ['name' => 'Jobs']);

    $response->assertSessionHasErrors('name');
});

it('can update a category', function () {
    $admin = createAdmin();
    $this->actingAs($admin);

    $category = Category::create(['name' => 'Jobs', 'slug' => 'jobs']);

    $response = $this->put("/admin/categories/{$category->id}", ['name' => 'Employment']);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Employment',
        'slug' => 'employment',
    ]);
});

it('validates category name is unique on update excluding self', function () {
    $admin = createAdmin();
    $this->actingAs($admin);

    Category::create(['name' => 'Jobs', 'slug' => 'jobs']);
    $category = Category::create(['name' => 'Housing', 'slug' => 'housing']);

    $response = $this->put("/admin/categories/{$category->id}", ['name' => 'Jobs']);

    $response->assertSessionHasErrors('name');
});

it('allows updating a category with its own name', function () {
    $admin = createAdmin();
    $this->actingAs($admin);

    $category = Category::create(['name' => 'Jobs', 'slug' => 'jobs']);

    $response = $this->put("/admin/categories/{$category->id}", ['name' => 'Jobs']);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

it('can delete a category without posts', function () {
    $admin = createAdmin();
    $this->actingAs($admin);

    $category = Category::create(['name' => 'Empty Category', 'slug' => 'empty-category']);

    $response = $this->delete("/admin/categories/{$category->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('cannot delete a category that has posts', function () {
    $admin = createAdmin();
    $this->actingAs($admin);

    $category = Category::create(['name' => 'Has Posts', 'slug' => 'has-posts']);

    // Create a post manually since Post has no factory
    Post::create([
        'title' => 'Test Post',
        'description' => 'Test',
        'city' => 'TestCity',
        'category_id' => $category->id,
        'user_id' => $admin->id,
        'expires_at' => now()->addDays(30),
    ]);

    $response = $this->delete("/admin/categories/{$category->id}");

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

it('blocks non-admin users from category crud', function () {
    (new RoleSeeder)->run();
    $user = User::factory()->create();
    $this->actingAs($user);

    $category = Category::create(['name' => 'Test', 'slug' => 'test']);

    $this->post('/admin/categories', ['name' => 'New'])->assertForbidden();
    $this->put("/admin/categories/{$category->id}", ['name' => 'Updated'])->assertForbidden();
    $this->delete("/admin/categories/{$category->id}")->assertForbidden();
});
