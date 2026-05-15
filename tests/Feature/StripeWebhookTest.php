<?php

use App\Listeners\StripeEventListener;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Events\WebhookReceived;

uses(RefreshDatabase::class);

it('updates user verification status when checkout.session.completed webhook is received', function () {
    $user = User::factory()->create([
        'is_verified' => false,
    ]);

    $payload = [
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_123',
                'client_reference_id' => $user->id,
            ],
        ],
    ];

    $event = new WebhookReceived($payload);
    $listener = new StripeEventListener;
    $listener->handle($event);

    $user->refresh();

    expect($user->is_verified)->toBeTrue();
});
