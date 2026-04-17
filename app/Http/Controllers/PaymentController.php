<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentController extends Controller
{
    /**
     * ✅ CREATE STRIPE CHECKOUT SESSION
     */
    public function process($reportId)
    {
        $report = Report::findOrFail($reportId);
        $settings = Setting::first();

        Stripe::setApiKey($settings->stripe_secret_key);

        // ✅ Calculate price securely
        $pricePerPage = $settings->per_page_price ?? 0;
        $total = $report->page_count * $pricePerPage;
        $amount = $total * 100; // cents

        $user = Auth::user();

        // ✅ Base session data
        $sessionData = [
            'payment_method_types' => ['card'],

            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Report Unlock (' . $report->page_count . ' pages)',
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],

            'mode' => 'payment',

            'success_url' => route('payment.success', $report->id) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel', $report->id),
        ];

        // ✅ FIX: Use ONLY one of these
        if ($user->stripe_customer_id) {
            $sessionData['customer'] = $user->stripe_customer_id;
        } else {
            $sessionData['customer_creation'] = 'always';
        }

        // ✅ Create Stripe session
        $session = Session::create($sessionData);

        return redirect($session->url);
    }

    /**
     * ✅ PAYMENT SUCCESS HANDLER
     */
    public function success(Request $request, $reportId)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('lock', $reportId)
                ->with('error', 'Invalid payment session.');
        }

        $settings = Setting::first();
        Stripe::setApiKey($settings->stripe_secret_key);

        // ✅ Retrieve session from Stripe
        $session = Session::retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            return redirect()->route('lock', $reportId)
                ->with('error', 'Payment not completed.');
        }

        $report = Report::findOrFail($reportId);
        $user = Auth::user();

        // ✅ Save Stripe customer ID (only once)
        if ($session->customer && !$user->stripe_customer_id) {
            $user->update([
                'stripe_customer_id' => $session->customer
            ]);
        }

        // ✅ Secure price calculation (never trust frontend)
        $pricePerPage = $settings->per_page_price ?? 0;
        $total = $report->page_count * $pricePerPage;

        // ✅ Update report
        $report->update([
            'payment_status' => 'paid',
            'price' => $total
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Payment successful! Report unlocked.');
    }

    /**
     * ❌ PAYMENT CANCEL
     */
    public function cancel($reportId)
    {
        return redirect()->route('lock', $reportId)
            ->with('error', 'Payment cancelled.');
    }
}