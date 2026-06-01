<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WalletsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | P2: Wallets index
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        // --- Replace with real Eloquent query ---
        $wallets = $this->mockWallets();

        \Illuminate\Support\Facades\Log::info('wallets_index_viewed', [
            'user' => $request->user()?->id,
        ]);

        return view('payments.wallets.index', [
            'wallets' => $wallets,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | P2: Wallet detail
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, string $id): View
    {
        $wallet = collect($this->mockWallets())->firstWhere('id', $id);
        abort_if(!$wallet, 404, 'Wallet not found.');

        \Illuminate\Support\Facades\Log::info('wallet_viewed', [
            'user'      => $request->user()?->id,
            'wallet_id' => $id,
        ]);

        return view('payments.wallets.show', [
            'wallet' => $wallet,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create hold
    |--------------------------------------------------------------------------
    */
    public function createHold(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'deal_ref' => 'nullable|string',
            'amount'   => 'required|numeric|min:0.01',
            'reason'   => 'required|string|max:500',
            'expiry'   => 'nullable|date|after:today',
        ]);

        // --- Replace with real hold creation ---

        \Illuminate\Support\Facades\Log::info('wallet_hold_created', [
            'user'      => $request->user()?->id,
            'wallet_id' => $id,
            'amount'    => $validated['amount'],
            'reason'    => $validated['reason'],
        ]);

        return back()->with('success', 'Hold of £' . number_format($validated['amount']) . ' created.');
    }

    /*
    |--------------------------------------------------------------------------
    | Release hold
    |--------------------------------------------------------------------------
    */
    public function releaseHold(Request $request, string $walletId, string $holdId): RedirectResponse
    {
        // --- Replace with real hold release ---

        \Illuminate\Support\Facades\Log::info('wallet_hold_released', [
            'user'      => $request->user()?->id,
            'wallet_id' => $walletId,
            'hold_id'   => $holdId,
        ]);

        return back()->with('success', 'Hold released.');
    }

    /*
    |--------------------------------------------------------------------------
    | Capture hold
    |--------------------------------------------------------------------------
    */
    public function captureHold(Request $request, string $walletId, string $holdId): RedirectResponse
    {
        // --- Replace with real hold capture ---

        \Illuminate\Support\Facades\Log::info('wallet_hold_captured', [
            'user'      => $request->user()?->id,
            'wallet_id' => $walletId,
            'hold_id'   => $holdId,
        ]);

        return back()->with('success', 'Hold captured.');
    }

    /*
    |--------------------------------------------------------------------------
    | Request payout (from wallet)
    |--------------------------------------------------------------------------
    */
    public function requestPayout(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'destination' => 'required|string',
            'note'        => 'required|string|max:1000',
        ]);

        // --- Replace with real payout request creation ---

        \Illuminate\Support\Facades\Log::info('wallet_payout_requested', [
            'user'        => $request->user()?->id,
            'wallet_id'   => $id,
            'amount'      => $validated['amount'],
            'destination' => $validated['destination'],
        ]);

        return back()->with('success', 'Payout request submitted. Awaiting Admin approval.');
    }

    /*
    |--------------------------------------------------------------------------
    | Add adjustment (journal entry)
    |--------------------------------------------------------------------------
    */
    public function addAdjustment(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric',
            'description' => 'required|string|max:500',
            'reference'   => 'nullable|string|max:100',
        ]);

        // --- Replace with real ledger adjustment ---

        \Illuminate\Support\Facades\Log::info('wallet_adjustment_added', [
            'user'        => $request->user()?->id,
            'wallet_id'   => $id,
            'amount'      => $validated['amount'],
            'description' => $validated['description'],
        ]);

        return back()->with('success', 'Adjustment posted to ledger.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / mock data
    |--------------------------------------------------------------------------
    */
    private function mockWallets(): array
    {
        $now = now();
        return [
            [
                'id'                  => 'WLT-001',
                'vendor_id'           => 'VND-001',
                'vendor_name'         => 'Fast Cars Ltd',
                'balance'             => 2100,
                'holds'               => 700,
                'status'              => 'Clear',
                'last_payout_date'    => $now->copy()->subDays(14)->format('d M Y'),
                'mandate_accepted_at' => $now->copy()->subMonths(3)->format('d M Y H:i'),
                'mandate_accepted_by' => 'AM',
                'payment_method'      => [
                    'brand'              => 'Visa',
                    'last4'              => '4242',
                    'expiry'             => '08/2027',
                    'status'             => 'Verified',
                    'added_by'           => 'AM',
                    'added_at'           => $now->copy()->subMonths(3)->format('d M Y'),
                    'mandate_accepted'   => true,
                    'setup_initiated_by' => 'AM',
                ],
                'movements'   => [
                    ['date' => $now->copy()->subDays(2)->format('d M Y'), 'ref' => 'DEL-3112', 'type' => 'hold',    'description' => 'Deposit hold — DEL-3112', 'amount' => 700,  'balance_after' => 2100],
                    ['date' => $now->copy()->subDays(5)->format('d M Y'), 'ref' => 'DEL-3098', 'type' => 'capture', 'description' => 'Deposit captured — DEL-3098', 'amount' => 500, 'balance_after' => 2800],
                    ['date' => $now->copy()->subDays(14)->format('d M Y'),'ref' => 'PAY-0041', 'type' => 'payout',  'description' => 'Payout approved',         'amount' => 1800, 'balance_after' => 2300],
                ],
                'holds_list'  => [
                    ['id' => 'HLD-001', 'deal_ref' => 'DEL-3112', 'amount' => 700, 'reason' => 'Deposit', 'expiry' => $now->copy()->addDays(3)->toDateString(), 'status' => 'Active'],
                ],
                'payouts'     => [
                    ['id' => 'PAY-0041', 'ref' => 'PAY-0041', 'requested_at' => $now->copy()->subDays(15)->format('d M Y'), 'amount' => 1800, 'destination' => 'Bank ••••4421', 'deal_ref' => 'DEL-3090', 'note' => 'Handover complete', 'requested_by' => 'JB', 'status' => 'Approved', 'resolved_at' => $now->copy()->subDays(14)->format('d M Y')],
                ],
                'payout_destinations' => [
                    ['id' => 'bank_1', 'label' => 'Bank account ••••4421'],
                    ['id' => 'wallet', 'label' => 'Platform wallet'],
                ],
                'statements'  => [
                    ['label' => 'May 2024 Statement', 'period' => 'May 2024', 'pdf_url' => '#', 'csv_url' => '#'],
                    ['label' => 'Apr 2024 Statement', 'period' => 'Apr 2024', 'pdf_url' => '#', 'csv_url' => '#'],
                ],
                'audit_log'   => [
                    ['timestamp' => $now->copy()->subDays(2)->format('d M Y H:i:s'), 'event' => 'wallet_hold_created',   'field' => 'holds',   'old_value' => '£0',    'new_value' => '£700',  'by' => 'System', 'ip' => '—'],
                    ['timestamp' => $now->copy()->subDays(14)->format('d M Y H:i:s'),'event' => 'wallet_payout_approved','field' => 'balance', 'old_value' => '£4100', 'new_value' => '£2300', 'by' => 'AM',     'ip' => '10.0.0.1'],
                ],
            ],
            [
                'id'               => 'WLT-002',
                'vendor_id'        => 'VND-002',
                'vendor_name'      => 'Premium Autos',
                'balance'          => 5400,
                'holds'            => 1000,
                'status'           => 'Clear',
                'last_payout_date' => $now->copy()->subDays(7)->format('d M Y'),
                'mandate_accepted_at' => null,
                'payment_method'   => null,
                'movements'        => [],
                'holds_list'       => [],
                'payouts'          => [],
                'payout_destinations' => [['id' => 'bank_2', 'label' => 'Bank account ••••9988']],
                'statements'       => [],
                'audit_log'        => [],
            ],
        ];
    }
}