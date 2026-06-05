<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VendorsController extends Controller
{
    // -------------------------------------------------------------------------
    // Mock data
    // -------------------------------------------------------------------------

    private function mockVendors(): array
    {
        return [
            [
                'id'            => 'VEN-001',
                'name'          => 'Fast Cars Ltd',
                'legal_name'    => 'Fast Cars Limited',
                'company_no'    => '12345678',
                'vat_no'        => 'GB123456789',
                'kyb'           => 'Verified',
                'card_on_file'  => true,
                'listings'      => 12,
                'bids'          => 48,
                'purchases'     => 5,
                'wallet'        => 'Clear',
                'last_activity' => '1 day ago',
                'owner'         => 'JR',
                'email'         => 'accounts@fastcars.co.uk',
                'phone'         => '+44 161 000 0000',
                'address'       => '1 Business Park, Manchester M1 1AA',
                'tags'          => ['Premium', 'Regular bidder'],
            ],
            [
                'id'            => 'VEN-002',
                'name'          => 'Prime Auto Group',
                'legal_name'    => 'Prime Auto Group Ltd',
                'company_no'    => '87654321',
                'vat_no'        => '',
                'kyb'           => 'Pending',
                'card_on_file'  => false,
                'listings'      => 3,
                'bids'          => 7,
                'purchases'     => 0,
                'wallet'        => 'Hold',
                'last_activity' => '3 days ago',
                'owner'         => 'AM',
                'email'         => 'info@primeauto.co.uk',
                'phone'         => '+44 207 000 0000',
                'address'       => '',
                'tags'          => [],
            ],
            [
                'id'            => 'VEN-003',
                'name'          => 'City Dealers Ltd',
                'legal_name'    => 'City Dealers Limited',
                'company_no'    => '11223344',
                'vat_no'        => '',
                'kyb'           => 'Required',
                'card_on_file'  => false,
                'listings'      => 0,
                'bids'          => 0,
                'purchases'     => 0,
                'wallet'        => '—',
                'last_activity' => '1 week ago',
                'owner'         => 'SR',
                'email'         => 'contact@citydealers.co.uk',
                'phone'         => '',
                'address'       => '',
                'tags'          => ['New'],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // C5 — Vendors index
    // -------------------------------------------------------------------------

    /**
     * GET /vendors
     */
    public function index(Request $request)
    {
        $vendors = $this->mockVendors();

        if ($search = $request->get('q')) {
            $s = strtolower($search);
            $vendors = array_filter($vendors, fn($v) =>
                str_contains(strtolower($v['name']), $s) ||
                str_contains(strtolower($v['email']), $s)
            );
        }

        if ($kyb = $request->get('kyb')) {
            $vendors = array_filter($vendors, fn($v) => $v['kyb'] === $kyb);
        }

        if ($request->get('card') === 'yes') {
            $vendors = array_filter($vendors, fn($v) => $v['card_on_file']);
        } elseif ($request->get('card') === 'no') {
            $vendors = array_filter($vendors, fn($v) => !$v['card_on_file']);
        }

        return view('crm.vendors', [
            'vendors' => array_values($vendors),
        ]);
    }

    /**
     * GET /vendors/{id}
     */
    public function show(string $id)
    {
        $vendor = collect($this->mockVendors())->firstWhere('id', $id)
            ?? $this->mockVendors()[0];

        return view('crm.vendors', [
            'vendors'     => $this->mockVendors(),
            'open_detail' => $vendor,
        ]);
    }

    /**
     * POST /vendors
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email',
        ]);

        // TODO: Eloquent create + fire vendor_created event
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor created.');
    }

    /**
     * PATCH /vendors/{id}
     * Event: vendor_updated
     */
    public function update(Request $request, string $id)
    {
        // TODO: Eloquent update + audit
        return redirect()->route('vendors.show', $id)
            ->with('success', 'Vendor updated.');
    }

    /**
     * POST /vendors/{id}/invite
     * Invite to an auction set
     */
    public function invite(Request $request, string $id)
    {
        $request->validate(['auction_id' => 'required|string']);

        // TODO: Create auction invitation + fire auction_invite_sent
        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Invitation sent.');
    }

    /**
     * POST /vendors/{id}/request-documents
     * Request KYB documents
     */
    public function requestDocuments(Request $request, string $id)
    {
        // TODO: Send document request + create task
        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Document request sent.');
    }

    /**
     * POST /vendors/{id}/kyb/start
     * Start KYB review (Compliance role)
     * Event: kyb_review_started
     */
    public function startKyb(Request $request, string $id)
    {
        // TODO: Set KYB state → In review + notify Compliance
        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'KYB review started.');
    }

    /**
     * POST /vendors/{id}/kyb/override
     * Super Admin KYB override — requires reason + attachment
     * Event: kyb_override_applied
     */
    public function overrideKyb(Request $request, string $id)
    {
        $request->validate([
            'reason'     => 'required|string|max:2000',
            'attachment' => 'required|file|max:25600',
        ]);

        // TODO: Log override with actor, reason, attachment + set KYB to Verified
        return back()->with('success', 'KYB override applied and logged.');
    }
}
