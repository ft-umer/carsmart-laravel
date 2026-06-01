<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DealsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | D1: Deals index / browse
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $search     = $request->input('search', '');
        $state      = $request->input('state', '');
        $objection  = $request->input('objection', '');
        $financial  = $request->input('financial', '');
        $owner      = $request->input('owner', '');
        $archived   = $request->boolean('include_archived', false);

        // --- Replace with your real Eloquent query ---
        $deals = $this->mockDeals();

        if ($search) {
            $deals = array_filter($deals, fn($d) =>
                str_contains(strtolower($d['ref'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($d['vehicle_title'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($d['buyer_name'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($d['seller_name'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($d['vrm'] ?? ''), strtolower($search))
            );
        }

        if ($state) {
            $deals = array_filter($deals, fn($d) => ($d['state'] ?? '') === $state);
        }

        if ($objection === 'in_window') {
            $deals = array_filter($deals, fn($d) =>
                ($d['objection_days_left'] ?? -1) >= 0
            );
        } elseif ($objection === 'window_over') {
            $deals = array_filter($deals, fn($d) =>
                ($d['objection_days_left'] ?? 0) < 0
            );
        }

        if ($financial === 'holds') {
            $deals = array_filter($deals, fn($d) => !empty($d['deposit_hold']));
        } elseif ($financial === 'awaiting_payout') {
            $deals = array_filter($deals, fn($d) => ($d['state'] ?? '') === 'Awaiting payout');
        }

        if ($owner) {
            $deals = array_filter($deals, fn($d) => ($d['owner'] ?? '') === $owner);
        }

        \Illuminate\Support\Facades\Log::info('deal_index_viewed', [
            'user'    => $request->user()?->id,
            'filters' => compact('search', 'state', 'objection', 'financial', 'owner'),
        ]);

        return view('deals.index', [
            'deals'    => array_values($deals),
            'total'    => count($deals),
            'page'     => 1,
            'hasMore'  => false,
            'search'   => $search,
            'state'    => $state,
            'objection'=> $objection,
            'financial'=> $financial,
            'owner'    => $owner,
            'owners'   => $this->ownerList(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | D2: Deal detail
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, string $id): View
    {
        $deal = $this->findDeal($id);

        abort_if(!$deal, 404, 'Deal not found.');

        \Illuminate\Support\Facades\Log::info('deal_viewed', [
            'user'    => $request->user()?->id,
            'deal_id' => $id,
        ]);

        return view('deals.show', [
            'deal'               => $deal,
            'owners'             => $this->ownerList(),
            'payoutDestinations' => $this->payoutDestinations($deal),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create / store
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view('deals.create', [
            'owners' => $this->ownerList(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_title' => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'source'        => 'required|in:AUC,BIN,Offer',
            'seller_id'     => 'required|string',
            'buyer_id'      => 'required|string',
            'owner'         => 'nullable|string',
        ]);

        // --- Replace with real model creation ---
        $id = 'DEL-' . strtoupper(substr(uniqid(), -4));

        \Illuminate\Support\Facades\Log::info('deal_created', [
            'user'    => $request->user()?->id,
            'deal_id' => $id,
            'data'    => $validated,
        ]);

        return redirect()->route('deals.show', $id)
                         ->with('success', 'Deal created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit / update
    |--------------------------------------------------------------------------
    */
    public function edit(string $id): View
    {
        $deal = $this->findDeal($id);
        abort_if(!$deal, 404);

        return view('deals.edit', [
            'deal'   => $deal,
            'owners' => $this->ownerList(),
        ]);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'owner'  => 'nullable|string',
            'state'  => 'nullable|in:Pending,Collection scheduled,Handover complete,Awaiting payout,Closed,Cancelled',
            'notes'  => 'nullable|string',
        ]);

        // --- Replace with real model update ---

        $changed = array_filter($validated);

        \Illuminate\Support\Facades\Log::info('deal_updated', [
            'user'    => $request->user()?->id,
            'deal_id' => $id,
            'changes' => $changed,
        ]);

        return back()->with('success', 'Deal updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        // --- Replace with real soft-delete ---

        \Illuminate\Support\Facades\Log::info('deal_deleted', [
            'user'    => $request->user()?->id,
            'deal_id' => $id,
        ]);

        return redirect()->route('deals.index')
                         ->with('success', 'Deal deleted.');
    }

    /*
    |--------------------------------------------------------------------------
    | Price adjustment (AJAX / form POST)
    |--------------------------------------------------------------------------
    */
    public function adjustPrice(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'new_price' => 'required|numeric|min:0',
            'reason'    => 'required|string|max:1000',
        ]);

        // --- Replace with real price adjustment logic ---

        \Illuminate\Support\Facades\Log::info('price_adjusted', [
            'user'      => $request->user()?->id,
            'deal_id'   => $id,
            'new_price' => $validated['new_price'],
            'reason'    => $validated['reason'],
        ]);

        return back()->with('success', 'Price adjusted to £' . number_format($validated['new_price']));
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel & re-run
    |--------------------------------------------------------------------------
    */
    public function cancelAndRerun(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // --- Replace with real cancel + re-queue logic ---

        \Illuminate\Support\Facades\Log::info('deal_cancelled', [
            'user'    => $request->user()?->id,
            'deal_id' => $id,
            'reason'  => $validated['reason'],
        ]);

        return redirect()->route('deals.index')
                         ->with('success', 'Deal cancelled. Re-run queued.');
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm handover
    |--------------------------------------------------------------------------
    */
    public function confirmHandover(Request $request, string $id): RedirectResponse
    {
        // --- Replace with real handover confirmation logic ---

        \Illuminate\Support\Facades\Log::info('handover_confirmed', [
            'user'    => $request->user()?->id,
            'deal_id' => $id,
        ]);

        return back()->with('success', 'Handover confirmed. Payout can now be requested.');
    }

    /*
    |--------------------------------------------------------------------------
    | Request payout
    |--------------------------------------------------------------------------
    */
    public function requestPayout(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'destination' => 'required|string',
            'note'        => 'required|string|max:1000',
        ]);

        // Validate: handover must be confirmed + docs uploaded
        $deal = $this->findDeal($id);
        if (!($deal['handover_signed'] ?? false)) {
            return back()->withErrors(['note' => 'Handover must be confirmed before requesting payout.']);
        }

        // --- Replace with real payout request creation ---

        \Illuminate\Support\Facades\Log::info('payout_requested', [
            'user'        => $request->user()?->id,
            'deal_id'     => $id,
            'destination' => $validated['destination'],
            'note'        => $validated['note'],
        ]);

        return back()->with('success', 'Payout request submitted. Awaiting Admin approval.');
    }

    /*
    |--------------------------------------------------------------------------
    | Generate settlement PDF
    |--------------------------------------------------------------------------
    */
    public function generateSettlement(string $id)
    {
        // --- Replace with real PDF generation (e.g. Snappy/DomPDF) ---

        \Illuminate\Support\Facades\Log::info('settlement_generated', ['deal_id' => $id]);

        return back()->with('success', 'Settlement PDF queued — check Documents tab.');
    }

    /*
    |--------------------------------------------------------------------------
    | Save notes (quick panel)
    |--------------------------------------------------------------------------
    */
    public function saveNotes(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate(['notes' => 'nullable|string']);

        // --- Replace with real notes update ---

        \Illuminate\Support\Facades\Log::info('deal_notes_updated', [
            'user'    => $request->user()?->id,
            'deal_id' => $id,
        ]);

        return back()->with('success', 'Notes saved.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers (replace with real DB/service calls)
    |--------------------------------------------------------------------------
    */
    private function findDeal(string $id): ?array
    {
        return collect($this->mockDeals())->firstWhere('id', $id);
    }

    private function ownerList(): array
    {
        return ['AM', 'JB', 'SK', 'CL', 'RP'];
    }

    private function payoutDestinations(array $deal): array
    {
        return [
            ['id' => 'bank_1', 'label' => 'Seller bank account (••••4421)'],
            ['id' => 'wallet_1', 'label' => 'Platform wallet'],
        ];
    }

    private function mockDeals(): array
    {
        $now = now();
        return [
            [
                'id'                  => 'DEL-3112',
                'ref'                 => 'DEL-3112',
                'source'              => 'AUC',
                'vehicle_title'       => 'BMW 330i 2019',
                'vrm'                 => 'BM19 XYZ',
                'price'               => 14000,
                'platform_fee'        => 350,
                'buyer_premium'       => 200,
                'deposit_hold'        => 500,
                'deposit_hold_expiry' => $now->copy()->addDays(3)->toDateString(),
                'reserve'             => 13000,
                'bin_price'           => null,
                'tax'                 => 0,
                'adjustments'         => 0,
                'state'               => 'Pending',
                'owner'               => 'AM',
                'objection_active'    => true,
                'objection_days_left' => 6,
                'objection_ends_at'   => $now->copy()->addDays(6)->toDateString(),
                'kyc_verified'        => true,
                'card_on_file'        => true,
                'v5c_uploaded'        => false,
                'photos_uploaded'     => false,
                'handover_signed'     => false,
                'mot_uploaded'        => false,
                'buyer_signed'        => false,
                'seller_signed'       => false,
                'buyer_name'          => 'Fast Cars Ltd',
                'buyer_company'       => 'Fast Cars Ltd',
                'buyer_email'         => 'buyer@fastcars.co.uk',
                'vendor_id'           => 'VND-001',
                'buyer_consent_email' => true,
                'seller_name'         => 'John Smith',
                'seller_id'           => 'CST-0041',
                'seller_email'        => 'john@example.com',
                'seller_phone'        => '+44 7700 900000',
                'seller_consent_email'=> true,
                'seller_consent_sms'  => true,
                'payout_state'        => null,
                'payout_request'      => null,
                'linked_listing_id'   => null,
                'job_id'              => null,
                'notes'               => '',
                'created_at'          => $now->copy()->subDays(2)->format('d M Y'),
                'price_adjustments'   => [],
                'documents'           => [],
                'communications'      => [],
                'activity'            => [
                    ['description' => 'Deal created from auction lot AUC-8801', 'date' => $now->copy()->subDays(2)->format('d M Y H:i'), 'by' => 'System'],
                    ['description' => 'Owner assigned: AM',                     'date' => $now->copy()->subDays(1)->format('d M Y H:i'), 'by' => 'AM'],
                ],
                'audit_log'           => [
                    ['timestamp' => $now->copy()->subDays(2)->format('d M Y H:i:s'), 'event' => 'deal_created',       'field' => 'state',  'old_value' => null,      'new_value' => 'Pending', 'by' => 'System', 'ip' => '—'],
                    ['timestamp' => $now->copy()->subDays(1)->format('d M Y H:i:s'), 'event' => 'deal_owner_changed', 'field' => 'owner',  'old_value' => null,      'new_value' => 'AM',      'by' => 'AM',     'ip' => '192.168.1.1'],
                ],
            ],
            [
                'id'                  => 'DEL-3098',
                'ref'                 => 'DEL-3098',
                'source'              => 'BIN',
                'vehicle_title'       => 'Audi A4 2021',
                'vrm'                 => 'AU21 ABC',
                'price'               => 22500,
                'platform_fee'        => 450,
                'buyer_premium'       => 250,
                'deposit_hold'        => 1000,
                'deposit_hold_expiry' => $now->copy()->addDays(5)->toDateString(),
                'reserve'             => null,
                'bin_price'           => 22500,
                'tax'                 => 0,
                'adjustments'         => 0,
                'state'               => 'Awaiting payout',
                'owner'               => 'JB',
                'objection_active'    => false,
                'objection_days_left' => -1,
                'objection_ends_at'   => $now->copy()->subDays(1)->toDateString(),
                'kyc_verified'        => true,
                'card_on_file'        => true,
                'v5c_uploaded'        => true,
                'photos_uploaded'     => true,
                'handover_signed'     => true,
                'mot_uploaded'        => true,
                'buyer_signed'        => true,
                'seller_signed'       => true,
                'buyer_name'          => 'Premium Autos',
                'buyer_company'       => 'Premium Autos Ltd',
                'buyer_email'         => 'buys@premiumautos.co.uk',
                'vendor_id'           => 'VND-002',
                'buyer_consent_email' => true,
                'seller_name'         => 'Sarah Jones',
                'seller_id'           => 'CST-0088',
                'seller_email'        => 'sarah@example.com',
                'seller_phone'        => '+44 7700 900111',
                'seller_consent_email'=> true,
                'seller_consent_sms'  => false,
                'payout_state'        => 'Pending approval',
                'payout_request'      => [
                    'amount'       => 22050,
                    'destination'  => 'Seller bank account (••••4421)',
                    'requested_by' => 'JB',
                    'requested_at' => $now->copy()->subHours(3)->format('d M Y H:i'),
                    'status'       => 'Pending',
                    'note'         => 'Handover completed. All documents uploaded.',
                    'approvals'    => [],
                ],
                'linked_listing_id'   => null,
                'job_id'              => 'JOB-0041',
                'notes'               => 'Buyer requested delivery by Friday.',
                'created_at'          => $now->copy()->subDays(10)->format('d M Y'),
                'price_adjustments'   => [],
                'documents'           => [
                    ['name' => 'V5C.pdf',            'uploaded_at' => $now->copy()->subDays(1)->format('d M Y'), 'uploaded_by' => 'JB', 'url' => '#', 'required' => true],
                    ['name' => 'condition_front.jpg', 'uploaded_at' => $now->copy()->subDays(1)->format('d M Y'), 'uploaded_by' => 'JB', 'url' => '#', 'required' => false],
                ],
                'communications'      => [
                    ['from' => 'System', 'direction' => 'outbound', 'channel' => 'Email', 'body' => 'Your deal DEL-3098 has been confirmed. Handover is scheduled.', 'sent_at' => $now->copy()->subDays(2)->format('d M Y H:i')],
                ],
                'activity'            => [
                    ['description' => 'Deal created from BIN purchase',   'date' => $now->copy()->subDays(10)->format('d M Y H:i'), 'by' => 'System'],
                    ['description' => 'Handover confirmed',                'date' => $now->copy()->subDays(1)->format('d M Y H:i'),  'by' => 'JB'],
                    ['description' => 'Payout requested',                  'date' => $now->copy()->subHours(3)->format('d M Y H:i'), 'by' => 'JB'],
                ],
                'audit_log'           => [
                    ['timestamp' => $now->copy()->subDays(10)->format('d M Y H:i:s'), 'event' => 'deal_created',        'field' => 'state', 'old_value' => null,          'new_value' => 'Pending',          'by' => 'System', 'ip' => '—'],
                    ['timestamp' => $now->copy()->subDays(1)->format('d M Y H:i:s'),  'event' => 'handover_confirmed',   'field' => 'state', 'old_value' => 'Collection scheduled', 'new_value' => 'Handover complete', 'by' => 'JB', 'ip' => '10.0.0.5'],
                    ['timestamp' => $now->copy()->subHours(3)->format('d M Y H:i:s'), 'event' => 'payout_requested',    'field' => 'state', 'old_value' => 'Handover complete',    'new_value' => 'Awaiting payout',   'by' => 'JB', 'ip' => '10.0.0.5'],
                ],
            ],
        ];
    }
}