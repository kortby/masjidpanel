<?php

use Stripe\StripeClient;
use Stripe\Balance;

it('can successfully authenticate and connect to the stripe api', function () {
    // Ensure the secret key is set
    $secretKey = config('cashier.secret');
    expect($secretKey)->not->toBeEmpty('Stripe secret key is not configured in .env (STRIPE_SECRET)');

    // Attempt to connect and fetch the balance as a simple ping
    $stripe = new StripeClient($secretKey);
    $balance = $stripe->balance->retrieve();

    expect($balance)->toBeInstanceOf(Balance::class);
})->group('stripe', 'integration');
