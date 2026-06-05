<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomersController extends Controller
{
    // -------------------------------------------------------------------------
    // Mock data
    // -------------------------------------------------------------------------

    private function mockCustomers(): array
    {
        return [
            [
                'id'            => 'CST-001',
                'name'          => 'Jane Doe',
                'phone'         => '+44 7700 900001',
                'email'         => 'jane.doe@example.com',
                'consent'       => ['email' => true, 'sms' => false, 'whatsapp' => true],
                'tags'          => ['VIP', 'Repeat'],
                'listings'      => 2,
                'last_activity' => '2 days ago',
                'owner'         => 'AM',
                'dnc'           => false,
                'source'        => 'Website',
                'address'       => '123 High Street, London SW1A 1AA',
                'preferred_channel' => 'Email',
                'best_time'     => 'Morning',
                'language'      => 'English',
                'notes'         => '',
            ],
            [
                'id'            => 'CST-002',
                'name'          => 'David Hughes',
                'phone'         => '+44 7700 900002',
                'email'         => 'david.h@example.com',
                'consent'       => ['email' => true, 'sms' => true, 'whatsapp' => false],
                'tags'          => [],
                'listings'      => 0,
                'last_activity' => '1 week ago',
                'owner'         => 'SR',
                'dnc'           => false,
                'source'        => 'Phone',
                'address'       => '',
                'preferred_channel' => 'SMS',
                'best_time'     => 'Afternoon',
                'language'      => 'English',
                'notes'         => '',
            ],
            [
                'id'            => 'CST-003',
                'name'          => 'Maria Santos',
                'phone'         => '+44 7700 900003',
                'email'         => 'maria.s@example.com',
                'consent'       => ['email' => false, 'sms' => false, 'whatsapp' => false],
                'tags'          => ['DNC'],
                'listings'      => 1,
                'last_activity' => '3 days ago',
                'owner'         => 'AM',
                'dnc'           => true,
                'source'        => 'Import',
                'address'       => '',
                'preferred_channel' => 'Email',
                'best_time'     => '',
                'language'      => 'Portuguese',
                'notes'         => 'Requested removal from all communications.',
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // C4 — Customers index
    // -------------------------------------------------------------------------

    /**
     * GET /customers
     */
    public function index(Request $request)
    {
        $customers = $this->mockCustomers();

        // Filter: search
        if ($search = $request->get('q')) {
            $s = strtolower($search);
            $customers = array_filter($customers, fn($c) =>
                str_contains(strtolower($c['name']), $s) ||
                str_contains(strtolower($c['email']), $s) ||
                str_contains($c['phone'], $s)
            );
        }

        // Filter: consent
        if ($consent = $request->get('consent')) {
            if ($consent === 'dnc') {
                $customers = array_filter($customers, fn($c) => $c['dnc']);
            } elseif (in_array($consent, ['email', 'sms', 'whatsapp'])) {
                $customers = array_filter($customers, fn($c) => $c['consent'][$consent] ?? false);
            }
        }

        return view('crm.customers', [
            'customers' => array_values($customers),
        ]);
    }

    /**
     * GET /customers/{id}
     */
    public function show(string $id)
    {
        $customer = collect($this->mockCustomers())->firstWhere('id', $id)
            ?? $this->mockCustomers()[0];

        return view('crm.customers', [
            'customers'    => $this->mockCustomers(),
            'open_detail'  => $customer,
        ]);
    }

    /**
     * POST /customers
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
        ]);

        // TODO: Eloquent create + fire person_created event
        return redirect()->route('customers.index')
            ->with('success', 'Person record created.');
    }

    /**
     * PATCH /customers/{id}
     */
    public function update(Request $request, string $id)
    {
        // TODO: Eloquent update + audit
        return redirect()->route('customers.show', $id)
            ->with('success', 'Record updated.');
    }

    /**
     * PATCH /customers/{id}/dnc
     * Event: person_dnc_set
     */
    public function markDnc(Request $request, string $id)
    {
        $request->validate(['reason' => 'required|string|max:1000']);

        // TODO: Set DNC flag + audit entry + fire person_dnc_set
        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Do-Not-Contact flag set.');
    }

    /**
     * POST /customers/{id}/merge
     * Event: duplicates_merged
     */
    public function merge(Request $request, string $id)
    {
        $request->validate([
            'target_id'  => 'required|string',
            'field_picks' => 'array',
        ]);

        // TODO: Merge logic — field-level picks, transfer listings/comms, delete duplicate
        return redirect()->route('customers.show', $id)
            ->with('success', 'Records merged.');
    }

    /**
     * GET /customers/{id}/export
     * Role-gated: Admin / CRM with PII masking for lower roles
     */
    public function export(string $id)
    {
        // TODO: Generate CSV with PII masking per role
        abort(404, 'Export not implemented in demo.');
    }

    /**
     * PATCH /customers/{id}/consent
     * Event: consent_updated
     */
    public function updateConsent(Request $request, string $id)
    {
        $request->validate([
            'channel' => 'required|in:email,sms,whatsapp,dnc',
            'value'   => 'required|boolean',
            'reason'  => 'required|string|max:1000',
        ]);

        // TODO: Update consent + audit with actor, before/after, reason
        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Consent updated.');
    }
}
