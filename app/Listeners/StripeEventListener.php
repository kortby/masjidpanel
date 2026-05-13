<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;

        if ($payload['type'] === 'checkout.session.completed') {
            $session = $payload['data']['object'];

            if (isset($session['client_reference_id'])) {
                $userId = $session['client_reference_id'];
                
                $user = User::find($userId);
                
                if ($user) {
                    $user->update(['is_verified' => true]);
                    Log::info("Verified user {$user->id} via Stripe webhook.");
                } else {
                    Log::error("Received checkout.session.completed for client_reference_id {$userId} but user not found.");
                }
            }
        }
    }
}
