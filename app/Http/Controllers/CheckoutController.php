<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->is_verified) {
            return redirect()->intended(route('home'));
        }

        if (!session()->has('url.intended') && url()->previous() !== url()->current()) {
            session(['url.intended' => url()->previous()]);
        }

        return Inertia::render('Checkout/Index');
    }

    public function process(Request $request)
    {
        $priceId = env('PRICE_TO_VERIFY'); // Fallback for dev

        $checkout = $request->user()->checkout([$priceId => 1], [
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'client_reference_id' => $request->user()->id,
        ]);

        return Inertia::location($checkout->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (! $sessionId) {
            return redirect()->route('checkout.index')->with('error', 'Invalid session.');
        }

        try {
            $stripe = $request->user()->stripe();
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $request->user()->forceFill(['is_verified' => true])->save();
            }
        } catch (\Exception $e) {
            return redirect()->intended(route('home'))->with('error', 'There was an issue verifying your payment. Please contact support.');
        }

        return redirect()->intended(route('home'))->with('success', 'Thank you! Your verification has been processed.');
    }

    public function cancel()
    {
        return redirect()->intended(route('home'))->with('error', 'Payment was cancelled.');
    }
}
