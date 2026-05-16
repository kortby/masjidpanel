<?php

use App\Mail\ContactMessageConfirmation;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('saves contact message to database', function () {
    Mail::fake();

    $response = $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Question about the platform',
        'message' => 'I have a question about how verification works.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Question about the platform',
        'is_read' => false,
    ]);
});

it('sends email to admin and confirmation to sender', function () {
    Mail::fake();

    $this->post('/contact', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'subject' => 'Partnership inquiry',
        'message' => 'I would like to discuss a partnership opportunity.',
    ]);

    Mail::assertSent(ContactMessageReceived::class, function ($mail) {
        return $mail->hasTo('kortby@gmail.com')
            && $mail->contactMessage->name === 'Jane Doe'
            && $mail->contactMessage->subject === 'Partnership inquiry';
    });

    Mail::assertSent(ContactMessageConfirmation::class, function ($mail) {
        return $mail->hasTo('jane@example.com')
            && $mail->contactMessage->name === 'Jane Doe';
    });
});

it('validates contact form fields', function () {
    Mail::fake();

    $response = $this->post('/contact', [
        'name' => '',
        'email' => 'not-an-email',
        'subject' => '',
        'message' => 'short',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    Mail::assertNothingSent();
});

it('admin dashboard includes messages', function () {
    (new RoleSeeder)->run();
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    ContactMessage::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message.',
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('messages', 1)
        ->where('messages.0.name', 'Test User')
        ->where('messages.0.subject', 'Test Subject')
        ->where('messages.0.is_read', false)
    );
});

it('admin can toggle message read status', function () {
    (new RoleSeeder)->run();
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $message = ContactMessage::create([
        'name' => 'User',
        'email' => 'user@example.com',
        'subject' => 'Hello',
        'message' => 'Hello there, this is a test.',
    ]);

    expect($message->refresh()->is_read)->toBeFalse();

    $this->actingAs($admin)->post("/admin/messages/{$message->id}/toggle-read");
    expect($message->refresh()->is_read)->toBeTrue();

    $this->actingAs($admin)->post("/admin/messages/{$message->id}/toggle-read");
    $message->refresh();
    expect($message->is_read)->toBeFalse();
});

it('admin can delete a message', function () {
    (new RoleSeeder)->run();
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $message = ContactMessage::create([
        'name' => 'User',
        'email' => 'user@example.com',
        'subject' => 'Delete me',
        'message' => 'This message should be deleted.',
    ]);

    $response = $this->actingAs($admin)->delete("/admin/messages/{$message->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
});

it('non-admin users cannot manage messages', function () {
    (new RoleSeeder)->run();
    $user = User::factory()->create();

    $message = ContactMessage::create([
        'name' => 'User',
        'email' => 'user@example.com',
        'subject' => 'Test',
        'message' => 'This is a test message content.',
    ]);

    $this->actingAs($user)->post("/admin/messages/{$message->id}/toggle-read")->assertForbidden();
    $this->actingAs($user)->delete("/admin/messages/{$message->id}")->assertForbidden();
});
