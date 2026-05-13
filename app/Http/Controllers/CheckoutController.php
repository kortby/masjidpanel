<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->is_verified) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Checkout/Index');
    }

    public function process(Request $request)
    {
        $priceId = config('services.stripe.verification_price_id', 'price_fake_123'); // Fallback for dev

        return $request->user()->checkout([$priceId => 1], [
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'client_reference_id' => $request->user()->id,
        ]);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (! $sessionId) {
            return redirect()->route('checkout.index')->with('error', 'Invalid session.');
        }

        return redirect()->route('dashboard')->with('success', 'Thank you! Your verification has been processed.');
    }

    public function cancel()
    {
        return redirect()->route('checkout.index')->with('error', 'Payment was cancelled.');
    }
}
