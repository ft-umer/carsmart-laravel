<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentMethodsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | P3: Payment methods index (cards on file)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        // --- Replace with real Eloquent query ---
        $methods = $this->mockMethods();

        \Illuminate\Support\Facades\Log::info('payment_methods_viewed', [
            'user' => $request->user()?->id,
        ]);

        return view('payments.methods', [
            'methods' => $methods,
            'total'   => count($methods),
            'vendors' => $this->vendorList(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Send setup link (MIT mandate + setup intent)
    |--------------------------------------------------------------------------
    */
    public function sendSetupLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|string',
            'email'     => 'required|email',
        ]);

        // --- Replace with real Stripe/payment gateway setup intent ---
        // Example:
        // $setupIntent = \Stripe\SetupIntent::create([
        //     'customer'             => $vendor->stripe_customer_id,
        //     'payment_method_types' => ['card'],
        //     'usage'                => 'off_session',   // MIT mandate
        // ]);
        // Mail::to($validated['email'])->send(new CardSetupMailable($setupIntent->client_secret));

        \Illuminate\Support\Facades\Log::info('payment_method_setup_sent', [
            'user'      => $request->user()?->id,
            'vendor_id' => $validated['vendor_id'],
            'email'     => $validated['email'],
        ]);

        return back()->with('success', 'Card setup link sent to ' . $validated['email']);
    }

    /*
    |--------------------------------------------------------------------------
    | Replace card (send new setup link)
    |--------------------------------------------------------------------------
    */
    public function replace(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // --- Replace with real replacement flow ---

        \Illuminate\Support\Facades\Log::info('payment_method_replace_initiated', [
            'user'   => $request->user()?->id,
            'pm_id'  => $id,
            'email'  => $validated['email'],
        ]);

        return back()->with('success', 'Card replacement link sent.');
    }

    /*
    |--------------------------------------------------------------------------
    | Remove card
    |--------------------------------------------------------------------------
    */
    public function remove(Request $request, string $id): RedirectResponse
    {
        // --- Replace with real card detach (e.g. Stripe\PaymentMethod::detach()) ---

        \Illuminate\Support\Facades\Log::info('payment_method_removed', [
            'user'  => $request->user()?->id,
            'pm_id' => $id,
        ]);

        return back()->with('success', 'Card removed successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Webhook: card setup completed (called by payment gateway)
    |--------------------------------------------------------------------------
    */
    public function setupWebhook(Request $request)
    {
        // --- Replace with real webhook verification + status update ---
        // Verify signature, update payment method status to Verified,
        // record mandate_accepted_at and setup_initiated_by.

        \Illuminate\Support\Facades\Log::info('payment_method_added', [
            'payload' => $request->all(),
        ]);

        return response()->json(['received' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / mock data
    |--------------------------------------------------------------------------
    */
    private function vendorList(): array
    {
        return [
            ['id' => 'VND-001', 'name' => 'Fast Cars Ltd'],
            ['id' => 'VND-002', 'name' => 'Premium Autos'],
            ['id' => 'VND-003', 'name' => 'City Motors'],
        ];
    }

    private function mockMethods(): array
    {
        $now = now();
        return [
            [
                'id'                 => 'PM-001',
                'vendor_id'          => 'VND-001',
                'vendor_name'        => 'Fast Cars Ltd',
                'brand'              => 'Visa',
                'last4'              => '4242',
                'expiry'             => '08/2027',
                'status'             => 'Verified',
                'added_by'           => 'AM',
                'added_at'           => $now->copy()->subMonths(3)->format('d M Y'),
                'mandate_accepted'   => true,
                'setup_initiated_by' => 'AM',
            ],
            [
                'id'                 => 'PM-002',
                'vendor_id'          => 'VND-002',
                'vendor_name'        => 'Premium Autos',
                'brand'              => 'Mastercard',
                'last4'              => '5555',
                'expiry'             => '12/2025',
                'status'             => 'Verified',
                'added_by'           => 'JB',
                'added_at'           => $now->copy()->subMonths(1)->format('d M Y'),
                'mandate_accepted'   => true,
                'setup_initiated_by' => 'JB',
            ],
            [
                'id'                 => 'PM-003',
                'vendor_id'          => 'VND-003',
                'vendor_name'        => 'City Motors',
                'brand'              => 'Visa',
                'last4'              => '1234',
                'expiry'             => '03/2024',  // expired
                'status'             => 'Expired',
                'added_by'           => 'SK',
                'added_at'           => $now->copy()->subYears(1)->format('d M Y'),
                'mandate_accepted'   => true,
                'setup_initiated_by' => 'SK',
            ],
        ];
    }
}