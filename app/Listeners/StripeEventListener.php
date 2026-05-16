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

                    try {
                        $fingerprints = $user->paymentMethods()->map(fn ($pm) => $pm->card->fingerprint ?? null)->filter()->unique();

                        if ($fingerprints->isNotEmpty()) {
                            $isBanned = \App\Models\BannedIdentifier::where('type', 'stripe_fingerprint')
                                ->whereIn('value', $fingerprints)
                                ->exists();

                            if ($isBanned) {
                                $user->update(['banned_at' => now()]);
                                $user->subscriptions->each->cancelNow();
                                Log::warning("User {$user->id} banned due to blocked Stripe fingerprint.");
                                
                                \App\Models\BannedIdentifier::firstOrCreate([
                                    'type' => 'email',
                                    'value' => $user->email,
                                ], ['user_id' => $user->id]);
                                
                                if ($user->device_id) {
                                    \App\Models\BannedIdentifier::firstOrCreate([
                                        'type' => 'device_cookie',
                                        'value' => $user->device_id,
                                    ], ['user_id' => $user->id]);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to check payment methods for banned fingerprints: " . $e->getMessage());
                    }
                } else {
                    Log::error("Received checkout.session.completed for client_reference_id {$userId} but user not found.");
                }
            }
        }
    }
}
