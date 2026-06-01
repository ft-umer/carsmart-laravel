<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DisputesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | S1: Disputes queue
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $search   = $request->input('search', '');
        $source   = $request->input('source', '');
        $sla      = $request->input('sla', '');
        $state    = $request->input('state', '');
        $owner    = $request->input('owner', '');
        $archived = $request->boolean('include_archived', false);

        // --- Replace with real Eloquent query ---
        $disputes = collect($this->mockDisputes());

        if ($search) {
            $disputes = $disputes->filter(fn($d) =>
                str_contains(strtolower($d['ref'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($d['deal_ref'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($d['reason'] ?? ''), strtolower($search))
            );
        }
        if ($source)  $disputes = $disputes->where('source', $source);
        if ($state)   $disputes = $disputes->where('state', $state);
        if ($owner)   $disputes = $disputes->where('owner', $owner);

        if ($sla === 'ack_due') {
            $disputes = $disputes->filter(fn($d) =>
                ($d['ack_hours_left'] ?? 99) >= 0 && ($d['ack_hours_left'] ?? 99) <= 24
            );
        } elseif ($sla === 'decision_due') {
            $disputes = $disputes->filter(fn($d) =>
                ($d['decision_days_left'] ?? 99) >= 0 && ($d['decision_days_left'] ?? 99) <= 1
            );
        }

        \Illuminate\Support\Facades\Log::info('dispute_index_viewed', [
            'user' => $request->user()?->id,
        ]);

        return view('disputes.index', [
            'disputes' => $disputes->values(),
            'total'    => $disputes->count(),
            'search'   => $search,
            'source'   => $source,
            'sla'      => $sla,
            'state'    => $state,
            'owner'    => $owner,
            'owners'   => ['AM', 'JB', 'SK', 'CL'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | S2: Dispute case detail
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, string $id): View
    {
        $dispute = $this->findDispute($id);
        abort_if(!$dispute, 404, 'Dispute not found.');

        \Illuminate\Support\Facades\Log::info('dispute_case_viewed', [
            'user'       => $request->user()?->id,
            'dispute_id' => $id,
        ]);

        return view('disputes.show', [
            'dispute' => $dispute,
            'owners'  => ['AM', 'JB', 'SK', 'CL'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create case (form)
    |--------------------------------------------------------------------------
    */
    public function create(Request $request): View
    {
        return view('disputes.create', [
            'deal_ref' => $request->input('deal'),
            'owners'   => ['AM', 'JB', 'SK', 'CL'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store new case
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'deal_id'  => 'required|string',
            'source'   => 'required|in:Seller objection,Post-handover',
            'reason'   => 'required|string|max:1000',
            'owner'    => 'nullable|string',
        ]);

        // --- Replace with real model creation ---
        $ref = 'DSP-' . rand(1000, 9999);

        \Illuminate\Support\Facades\Log::info('dispute_case_created', [
            'user'   => $request->user()?->id,
            'ref'    => $ref,
            'data'   => $validated,
        ]);

        return redirect()->route('disputes.show', $ref)
                         ->with('success', "Dispute case {$ref} opened.");
    }

    /*
    |--------------------------------------------------------------------------
    | Update state / owner (quick panel)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'owner' => 'nullable|string',
            'state' => 'nullable|in:New,Ack sent,Investigating,Decision pending,Resolved,Escalated',
            'notes' => 'nullable|string',
        ]);

        $oldState = $this->findDispute($id)['state'] ?? null;

        // --- Replace with real update ---

        \Illuminate\Support\Facades\Log::info('dispute_updated', [
            'user'       => $request->user()?->id,
            'dispute_id' => $id,
            'changes'    => array_filter($validated),
        ]);

        if (isset($validated['state']) && $validated['state'] !== $oldState) {
            \Illuminate\Support\Facades\Log::info('dispute_state_changed', [
                'dispute_id' => $id,
                'from'       => $oldState,
                'to'         => $validated['state'],
                'by'         => $request->user()?->id,
            ]);
        }

        return back()->with('success', 'Case updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Send acknowledgement
    |--------------------------------------------------------------------------
    */
    public function sendAck(Request $request, string $id): RedirectResponse
    {
        // --- Replace with real email/notification send + status update ---

        \Illuminate\Support\Facades\Log::info('dispute_ack_sent', [
            'user'       => $request->user()?->id,
            'dispute_id' => $id,
        ]);

        return back()->with('success', 'Acknowledgement sent to all parties.');
    }

    /*
    |--------------------------------------------------------------------------
    | Decide outcome
    | Allowed: price_adjustment | cancel_rerun | vendor_charge | partial_refund | note_only
    |--------------------------------------------------------------------------
    */
    public function decideOutcome(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'outcome_type' => 'required|in:price_adjustment,cancel_rerun,vendor_charge,partial_refund,note_only',
            'amount'       => 'nullable|numeric|min:0',
            'notes'        => 'required|string|max:2000',
        ]);

        $financialTypes = ['price_adjustment','vendor_charge','partial_refund'];
        if (in_array($validated['outcome_type'], $financialTypes) && !isset($validated['amount'])) {
            return back()->withErrors(['amount' => 'Amount is required for financial outcomes.']);
        }

        // --- Replace with real outcome recording + financial postings ---
        // Financial outcomes should:
        // 1. Post entries to Wallet/Charges
        // 2. Update the linked Deal (price, state)
        // 3. Notify relevant parties

        \Illuminate\Support\Facades\Log::info('dispute_decision_recorded', [
            'user'         => $request->user()?->id,
            'dispute_id'   => $id,
            'outcome_type' => $validated['outcome_type'],
            'amount'       => $validated['amount'] ?? null,
            'notes'        => $validated['notes'],
        ]);

        if (in_array($validated['outcome_type'], $financialTypes)) {
            \Illuminate\Support\Facades\Log::info('financial_adjustment_applied', [
                'dispute_id' => $id,
                'type'       => $validated['outcome_type'],
                'amount'     => $validated['amount'],
            ]);
        }

        return back()->with('success', 'Outcome recorded and financial postings created.');
    }

    /*
    |--------------------------------------------------------------------------
    | Apply charge / refund (standalone financial action)
    |--------------------------------------------------------------------------
    */
    public function applyCharge(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'type'   => 'required|in:vendor_charge,partial_refund,adjustment',
            'amount' => 'required|numeric|min:0.01',
            'party'  => 'required|string',
            'notes'  => 'required|string|max:500',
        ]);

        // --- Replace with real wallet/charge posting ---

        \Illuminate\Support\Facades\Log::info('financial_adjustment_applied', [
            'user'       => $request->user()?->id,
            'dispute_id' => $id,
            'type'       => $validated['type'],
            'amount'     => $validated['amount'],
            'party'      => $validated['party'],
        ]);

        return back()->with('success', 'Charge/refund of £' . number_format($validated['amount']) . ' applied.');
    }

    /*
    |--------------------------------------------------------------------------
    | Close case
    |--------------------------------------------------------------------------
    */
    public function close(Request $request, string $id): RedirectResponse
    {
        // --- Replace with real close logic ---

        \Illuminate\Support\Facades\Log::info('dispute_closed', [
            'user'       => $request->user()?->id,
            'dispute_id' => $id,
        ]);

        return redirect()->route('disputes.index')
                         ->with('success', 'Dispute case closed.');
    }

    /*
    |--------------------------------------------------------------------------
    | Escalate case
    |--------------------------------------------------------------------------
    */
    public function escalate(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // --- Replace with real escalation logic ---

        \Illuminate\Support\Facades\Log::info('dispute_escalated', [
            'user'       => $request->user()?->id,
            'dispute_id' => $id,
            'reason'     => $validated['reason'] ?? null,
        ]);

        return back()->with('success', 'Case escalated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Upload evidence
    |--------------------------------------------------------------------------
    */
    public function uploadEvidence(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'evidence_file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:25600',
            'notes'         => 'nullable|string|max:500',
        ]);

        $file = $request->file('evidence_file');

        // --- Replace with real file storage ---

        \Illuminate\Support\Facades\Log::info('dispute_evidence_uploaded', [
            'user'        => $request->user()?->id,
            'dispute_id'  => $id,
            'filename'    => $file->getClientOriginalName(),
        ]);

        return back()->with('success', 'Evidence uploaded.');
    }

    /*
    |--------------------------------------------------------------------------
    | Save notes
    |--------------------------------------------------------------------------
    */
    public function saveNotes(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate(['notes' => 'nullable|string']);

        // --- Replace with real notes update ---

        \Illuminate\Support\Facades\Log::info('dispute_notes_updated', [
            'user'       => $request->user()?->id,
            'dispute_id' => $id,
        ]);

        return back()->with('success', 'Notes saved.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / mock data
    |--------------------------------------------------------------------------
    */
    private function findDispute(string $id): ?array
    {
        return collect($this->mockDisputes())->firstWhere('id', $id)
            ?? collect($this->mockDisputes())->firstWhere('ref', $id);
    }

    private function mockDisputes(): array
    {
        $now = now();
        return [
            [
                'id'                 => 'DSP-1180',
                'ref'                => 'DSP-1180',
                'deal_ref'           => 'DEL-3112',
                'deal_id'            => 'DEL-3112',
                'deal_price'         => 14000,
                'source'             => 'Seller objection',
                'reason'             => 'Condition variance — undisclosed scratches',
                'state'              => 'New',
                'owner'              => 'AM',
                'raised_by'          => 'John Smith',
                'raised_by_role'     => 'Seller',
                'against_party'      => 'Fast Cars Ltd (Buyer)',
                'vehicle_title'      => 'BMW 330i 2019',
                'vrm'                => 'BM19 XYZ',
                'ack_hours_left'     => 18,
                'decision_days_left' => 5,
                'created_at'         => $now->copy()->subHours(6)->format('d M Y H:i'),
                'notes'              => '',
                'outcome'            => null,
                'evidence'           => [],
                'financial_postings' => [],
                'inspection_reports' => [],
                'communications'     => [],
                'activity'           => [
                    ['description' => 'Dispute case opened — Seller objection', 'date' => $now->copy()->subHours(6)->format('d M Y H:i'), 'by' => 'System'],
                    ['description' => 'Owner assigned: AM', 'date' => $now->copy()->subHours(5)->format('d M Y H:i'), 'by' => 'System'],
                ],
                'audit_log' => [
                    ['timestamp' => $now->copy()->subHours(6)->format('d M Y H:i:s'), 'event' => 'dispute_case_created', 'field' => 'state', 'old_value' => null, 'new_value' => 'New', 'by' => 'System', 'ip' => '—'],
                ],
                'buyer_name'  => 'Fast Cars Ltd',
                'seller_name' => 'John Smith',
            ],
            [
                'id'                 => 'DSP-1175',
                'ref'                => 'DSP-1175',
                'deal_ref'           => 'DEL-3088',
                'deal_id'            => 'DEL-3088',
                'deal_price'         => 13650,
                'source'             => 'Post-handover',
                'reason'             => 'Engine fault discovered after delivery',
                'state'              => 'Investigating',
                'owner'              => 'JB',
                'raised_by'          => 'Fast Cars Ltd',
                'raised_by_role'     => 'Buyer',
                'against_party'      => 'Tom Brown (Seller)',
                'vehicle_title'      => 'Ford Focus 2020',
                'vrm'                => 'FO20 XYZ',
                'ack_hours_left'     => null,
                'decision_days_left' => 2,
                'created_at'         => $now->copy()->subDays(3)->format('d M Y H:i'),
                'notes'              => 'Buyer provided mechanic report. Awaiting seller response.',
                'outcome'            => null,
                'evidence'           => [
                    ['name' => 'mechanic_report.pdf', 'added_by' => 'Fast Cars Ltd', 'added_at' => $now->copy()->subDays(2)->format('d M Y'), 'url' => '#', 'notes' => 'Independent mechanic report'],
                    ['name' => 'engine_photo_1.jpg',  'added_by' => 'Fast Cars Ltd', 'added_at' => $now->copy()->subDays(2)->format('d M Y'), 'url' => '#', 'notes' => null],
                ],
                'financial_postings' => [],
                'inspection_reports' => [
                    ['name' => 'Pre-sale Inspection Report', 'date' => $now->copy()->subDays(10)->format('d M Y'), 'url' => '#'],
                ],
                'communications' => [],
                'activity' => [
                    ['description' => 'Dispute case opened — Post-handover', 'date' => $now->copy()->subDays(3)->format('d M Y H:i'), 'by' => 'System'],
                    ['description' => 'Acknowledgement sent', 'date' => $now->copy()->subDays(3)->format('d M Y H:i'), 'by' => 'JB'],
                    ['description' => 'Evidence uploaded by buyer', 'date' => $now->copy()->subDays(2)->format('d M Y H:i'), 'by' => 'Fast Cars Ltd'],
                    ['description' => 'State changed to Investigating', 'date' => $now->copy()->subDays(2)->format('d M Y H:i'), 'by' => 'JB'],
                ],
                'audit_log' => [
                    ['timestamp' => $now->copy()->subDays(3)->format('d M Y H:i:s'), 'event' => 'dispute_case_created', 'field' => 'state', 'old_value' => null,    'new_value' => 'New',           'by' => 'System', 'ip' => '—'],
                    ['timestamp' => $now->copy()->subDays(3)->format('d M Y H:i:s'), 'event' => 'dispute_ack_sent',     'field' => 'state', 'old_value' => 'New',    'new_value' => 'Ack sent',      'by' => 'JB',     'ip' => '10.0.0.5'],
                    ['timestamp' => $now->copy()->subDays(2)->format('d M Y H:i:s'), 'event' => 'dispute_updated',      'field' => 'state', 'old_value' => 'Ack sent','new_value' => 'Investigating', 'by' => 'JB',     'ip' => '10.0.0.5'],
                ],
                'buyer_name'  => 'Fast Cars Ltd',
                'seller_name' => 'Tom Brown',
            ],
            [
                'id'                 => 'DSP-1168',
                'ref'                => 'DSP-1168',
                'deal_ref'           => 'DEL-3071',
                'deal_id'            => 'DEL-3071',
                'deal_price'         => 9800,
                'source'             => 'Seller objection',
                'reason'             => 'Reserve price not met — seller disputes BIN acceptance',
                'state'              => 'Resolved',
                'owner'              => 'SK',
                'raised_by'          => 'Alice Cooper',
                'raised_by_role'     => 'Seller',
                'against_party'      => 'City Motors (Buyer)',
                'vehicle_title'      => 'VW Golf 2018',
                'vrm'                => 'VW18 ABC',
                'ack_hours_left'     => null,
                'decision_days_left' => null,
                'created_at'         => $now->copy()->subDays(14)->format('d M Y H:i'),
                'notes'              => 'Resolved with price adjustment of +£200.',
                'outcome'            => [
                    'type'        => 'price_adjustment',
                    'amount'      => 200,
                    'notes'       => 'Price adjusted by £200 to account for condition discrepancy. Both parties agreed.',
                    'decided_by'  => 'SK',
                    'decided_at'  => $now->copy()->subDays(10)->format('d M Y H:i'),
                ],
                'evidence'           => [],
                'financial_postings' => [
                    ['date' => $now->copy()->subDays(10)->format('d M Y'), 'type' => 'price_adjustment', 'party' => 'Seller', 'amount' => 200, 'ref' => 'DEL-3071', 'status' => 'Applied'],
                ],
                'inspection_reports' => [],
                'communications'     => [],
                'activity'           => [
                    ['description' => 'Dispute opened',           'date' => $now->copy()->subDays(14)->format('d M Y H:i'), 'by' => 'System'],
                    ['description' => 'Outcome: price adjustment','date' => $now->copy()->subDays(10)->format('d M Y H:i'), 'by' => 'SK'],
                    ['description' => 'Case resolved',            'date' => $now->copy()->subDays(10)->format('d M Y H:i'), 'by' => 'SK'],
                ],
                'audit_log' => [],
                'buyer_name'  => 'City Motors',
                'seller_name' => 'Alice Cooper',
            ],
        ];
    }
}