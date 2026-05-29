<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class LeadsController extends Controller
{
    // -------------------------------------------------------------------------
    // Dummy data helpers
    // -------------------------------------------------------------------------

    /**
     * Returns a collection of mock leads.
     * In production: Lead::with(['person','vehicle','valuations','tasks'])->get()
     */
    private function mockLeads(): array
    {
        return [
            [
                'id'               => 'LED-2041',
                'name'             => 'John Smith',
                'email'            => 'john.smith@example.com',
                'phone'            => '+44 7700 900123',
                'source'           => 'Website',
                'vrm'              => 'AB19 CDE',
                'vin'              => '',
                'stage'            => 'Qualified',
                'sla_due'          => now()->toDateString(),
                'owner'            => 'SR',
                'date_added'       => '2025-10-10',
                'linked_listing_id'=> 'LST-9001',
                'consent'          => ['email' => true, 'sms' => false, 'whatsapp' => true],
                'dnc'              => false,
                'tags'             => ['High value', 'Repeat'],
                'priority'         => 'High',
                'notes'            => 'Interested in quick sale.',
                'valuations'       => [
                    [
                        'id'       => 'v1',
                        'date'     => '2025-10-12T10:24:00Z',
                        'source'   => 'AutoProvider',
                        'valuer'   => 'ProviderX',
                        'amount'   => 14200,
                        'notes'    => 'Latest automated pull',
                        'comps'    => ['C1'],
                        'applied'  => false,
                    ],
                ],
                'tasks'            => [
                    ['id' => 't1', 'title' => 'Call back by 5pm', 'due' => now()->toDateString(), 'status' => 'pending', 'priority' => 'High'],
                ],
                'activity'         => [
                    ['date' => '2025-10-10', 'type' => 'lead_created',  'description' => 'Lead created via Website'],
                    ['date' => '2025-10-11', 'type' => 'stage_moved',   'description' => 'Stage moved to Qualified'],
                    ['date' => '2025-10-12', 'type' => 'valuation_fetched', 'description' => 'Valuation pulled: £14,200 via AutoProvider'],
                ],
            ],
            [
                'id'               => 'LED-2042',
                'name'             => 'Jane Perez',
                'email'            => 'jane.perez@example.com',
                'phone'            => '+44 7700 900124',
                'source'           => 'Walk-in',
                'vrm'              => '',
                'vin'              => '',
                'stage'            => 'New',
                'sla_due'          => now()->addDay()->toDateString(),
                'owner'            => '',
                'date_added'       => '2025-10-09',
                'linked_listing_id'=> '',
                'consent'          => ['email' => true, 'sms' => true, 'whatsapp' => false],
                'dnc'              => false,
                'tags'             => [],
                'priority'         => 'Normal',
                'notes'            => '',
                'valuations'       => [],
                'tasks'            => [],
                'activity'         => [
                    ['date' => '2025-10-09', 'type' => 'lead_created', 'description' => 'Lead created via Walk-in'],
                ],
            ],
            [
                'id'               => 'LED-2043',
                'name'             => 'Carlos Webb',
                'email'            => 'carlos.webb@example.com',
                'phone'            => '+44 7700 900125',
                'source'           => 'Phone',
                'vrm'              => 'XY21 ZAB',
                'vin'              => '',
                'stage'            => 'Pricing sent',
                'sla_due'          => now()->subDay()->toDateString(), // overdue
                'owner'            => 'PM',
                'date_added'       => '2025-10-08',
                'linked_listing_id'=> '',
                'consent'          => ['email' => true, 'sms' => false, 'whatsapp' => false],
                'dnc'              => false,
                'tags'             => ['Urgent'],
                'priority'         => 'High',
                'notes'            => 'Chasing quote approval.',
                'valuations'       => [
                    [
                        'id'       => 'v2',
                        'date'     => '2025-10-08T14:00:00Z',
                        'source'   => 'Internal',
                        'valuer'   => 'PM',
                        'amount'   => 9800,
                        'notes'    => 'Manual desk valuation',
                        'comps'    => [],
                        'applied'  => false,
                    ],
                ],
                'tasks'            => [
                    ['id' => 't2', 'title' => 'Send revised quote', 'due' => now()->subDay()->toDateString(), 'status' => 'overdue', 'priority' => 'High'],
                ],
                'activity'         => [
                    ['date' => '2025-10-08', 'type' => 'lead_created',     'description' => 'Lead created via Phone'],
                    ['date' => '2025-10-08', 'type' => 'valuation_added',  'description' => 'Manual valuation added: £9,800 by PM'],
                    ['date' => '2025-10-09', 'type' => 'message_sent',     'description' => 'Pricing email sent via template "Quote v1"'],
                ],
            ],
            [
                'id'               => 'LED-2044',
                'name'             => 'Amara Osei',
                'email'            => 'amara.osei@example.com',
                'phone'            => '+44 7700 900126',
                'source'           => 'Referral',
                'vrm'              => 'MN70 PQR',
                'vin'              => '',
                'stage'            => 'Awaiting seller docs',
                'sla_due'          => now()->addDays(2)->toDateString(),
                'owner'            => 'SR',
                'date_added'       => '2025-10-07',
                'linked_listing_id'=> 'LST-9002',
                'consent'          => ['email' => true, 'sms' => true, 'whatsapp' => true],
                'dnc'              => false,
                'tags'             => ['Referral', 'VIP'],
                'priority'         => 'Normal',
                'notes'            => 'Awaiting V5 and service history.',
                'valuations'       => [
                    [
                        'id'       => 'v3',
                        'date'     => '2025-10-09T09:00:00Z',
                        'source'   => 'AutoProvider',
                        'valuer'   => 'ProviderY',
                        'amount'   => 18500,
                        'notes'    => 'Automated pull',
                        'comps'    => ['C2', 'C3'],
                        'applied'  => true,
                    ],
                    [
                        'id'       => 'v4',
                        'date'     => '2025-10-11T11:00:00Z',
                        'source'   => 'Internal',
                        'valuer'   => 'SR',
                        'amount'   => 18800,
                        'notes'    => 'After inspection uplift',
                        'comps'    => [],
                        'applied'  => false,
                    ],
                ],
                'tasks'            => [],
                'activity'         => [
                    ['date' => '2025-10-07', 'type' => 'lead_created',       'description' => 'Lead created via Referral'],
                    ['date' => '2025-10-09', 'type' => 'valuation_fetched',  'description' => 'Valuation pulled: £18,500 via ProviderY'],
                    ['date' => '2025-10-09', 'type' => 'valuation_applied',  'description' => 'Valuation £18,500 applied to LST-9002 as Guide price'],
                    ['date' => '2025-10-11', 'type' => 'valuation_added',    'description' => 'Manual valuation added: £18,800 by SR'],
                ],
            ],
            [
                'id'               => 'LED-2045',
                'name'             => 'Priya Kapoor',
                'email'            => 'priya.kapoor@example.com',
                'phone'            => '+44 7700 900127',
                'source'           => 'Import',
                'vrm'              => 'EF68 STU',
                'vin'              => '',
                'stage'            => 'Ready',
                'sla_due'          => now()->addDays(3)->toDateString(),
                'owner'            => 'PM',
                'date_added'       => '2025-10-06',
                'linked_listing_id'=> 'LST-9003',
                'consent'          => ['email' => true, 'sms' => true, 'whatsapp' => false],
                'dnc'              => false,
                'tags'             => ['Import'],
                'priority'         => 'Normal',
                'notes'            => 'All docs received, ready to list.',
                'valuations'       => [
                    [
                        'id'       => 'v5',
                        'date'     => '2025-10-06T16:00:00Z',
                        'source'   => 'AutoProvider',
                        'valuer'   => 'ProviderX',
                        'amount'   => 22000,
                        'notes'    => 'Pre-listing automated pull',
                        'comps'    => [],
                        'applied'  => true,
                    ],
                ],
                'tasks'            => [
                    ['id' => 't3', 'title' => 'Confirm listing live', 'due' => now()->addDay()->toDateString(), 'status' => 'pending', 'priority' => 'Normal'],
                ],
                'activity'         => [
                    ['date' => '2025-10-06', 'type' => 'lead_created',       'description' => 'Lead created via Import'],
                    ['date' => '2025-10-06', 'type' => 'valuation_fetched',  'description' => 'Valuation pulled: £22,000 via ProviderX'],
                    ['date' => '2025-10-06', 'type' => 'valuation_applied',  'description' => 'Valuation applied to LST-9003'],
                ],
            ],
        ];
    }

    private function stageOptions(): array
    {
        return ['New', 'Qualified', 'Pricing sent', 'Awaiting seller docs', 'Ready'];
    }

    private function ownerOptions(): array
    {
        return ['SR', 'PM', 'AM', 'JR'];
    }

    private function sourceOptions(): array
    {
        return ['Website', 'Phone', 'Referral', 'Import', 'Walk-in'];
    }

    // -------------------------------------------------------------------------
    // C1 — Browse / Pipeline
    // -------------------------------------------------------------------------

    /**
     * GET /crm/leads
     * Table + Board view of leads pipeline.
     */
    public function index(Request $request)
    {
        $leads   = $this->mockLeads();
        $stages  = $this->stageOptions();
        $owners  = $this->ownerOptions();
        $sources = $this->sourceOptions();

        // Simple in-memory filtering (replace with Eloquent scopes)
        $search = $request->input('search', '');
        $stage  = $request->input('stage', '');
        $owner  = $request->input('owner', '');
        $source = $request->input('source', '');
        $sla    = $request->input('sla', '');
        $view   = $request->input('view', 'table'); // table|board

        if ($search) {
            $leads = array_filter($leads, fn($l) =>
                str_contains(strtolower($l['name']),  strtolower($search)) ||
                str_contains(strtolower($l['email']), strtolower($search)) ||
                str_contains(strtolower($l['phone']), strtolower($search)) ||
                str_contains(strtolower($l['vrm']),   strtolower($search))
            );
        }
        if ($stage)  $leads = array_filter($leads, fn($l) => $l['stage']  === $stage);
        if ($owner)  $leads = array_filter($leads, fn($l) => $l['owner']  === $owner);
        if ($source) $leads = array_filter($leads, fn($l) => $l['source'] === $source);
        if ($sla === 'overdue')    $leads = array_filter($leads, fn($l) => $l['sla_due'] < now()->toDateString());
        if ($sla === 'due_today')  $leads = array_filter($leads, fn($l) => $l['sla_due'] === now()->toDateString());

        // Board grouping
        $board = [];
        foreach ($stages as $s) {
            $board[$s] = array_values(array_filter($leads, fn($l) => $l['stage'] === $s));
        }

        return view('leads.index', compact(
            'leads', 'stages', 'owners', 'sources',
            'board', 'search', 'stage', 'owner', 'source', 'sla', 'view'
        ));
    }

    // -------------------------------------------------------------------------
    // C2 — Create
    // -------------------------------------------------------------------------

    /**
     * GET /crm/leads/create
     */
    public function create()
    {
        $stages  = $this->stageOptions();
        $owners  = $this->ownerOptions();
        $sources = $this->sourceOptions();

        return view('leads.create', compact('stages', 'owners', 'sources'));
    }

    /**
     * POST /crm/leads
     */
    public function store(Request $request)
    {
        // Validation rules (C2)
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'nullable|email',
            'phone'  => 'nullable|string',
            // At least one contact method
        ]);

        if (empty($request->email) && empty($request->phone)) {
            return back()->withErrors(['contact' => 'At least one contact method (email or phone) is required.'])->withInput();
        }

        // TODO: Check for duplicate (email/telephone already exists) → lead_created
        // TODO: Dispatch lead_created event

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    // -------------------------------------------------------------------------
    // C3 — Lead Detail
    // -------------------------------------------------------------------------

    /**
     * GET /crm/leads/{lead}
     */
    public function show(string $lead)
    {
        $record = collect($this->mockLeads())->firstWhere('id', $lead)
            ?? $this->mockLeads()[0]; // fallback for demo

        return view('leads.show', [
            'lead'    => $record,
            'stages'  => $this->stageOptions(),
            'owners'  => $this->ownerOptions(),
        ]);
    }

    /**
     * GET /crm/leads/{lead}/edit
     */
    public function edit(string $lead)
    {
        $record = collect($this->mockLeads())->firstWhere('id', $lead)
            ?? $this->mockLeads()[0];

        return view('leads.edit', [
            'lead'    => $record,
            'stages'  => $this->stageOptions(),
            'owners'  => $this->ownerOptions(),
            'sources' => $this->sourceOptions(),
        ]);
    }

    /**
     * PUT /crm/leads/{lead}
     */
    public function update(Request $request, string $lead)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // TODO: Eloquent update + audit field-level changes (History tab)
        return redirect()->route('leads.show', $lead)
            ->with('success', 'Lead updated.');
    }

    /**
     * DELETE /crm/leads/{lead}
     */
    public function destroy(string $lead)
    {
        // TODO: soft-delete; admin recoverable via audit
        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted.');
    }

    // -------------------------------------------------------------------------
    // Stage / Owner helpers
    // -------------------------------------------------------------------------

    /**
     * PATCH /crm/leads/{lead}/stage
     * C1: Moving to Ready requires contact + consent + mandatory checks.
     */
    public function moveStage(Request $request, string $lead)
    {
        $request->validate(['stage' => 'required|string']);

        // TODO: enforce Ready gate → lead_board_moved
        return response()->json(['success' => true, 'message' => 'Stage updated.']);
    }

    /**
     * PATCH /crm/leads/{lead}/assign
     */
    public function assign(Request $request, string $lead)
    {
        $request->validate(['owner' => 'required|string']);

        // TODO: lead_owner_changed event
        return response()->json(['success' => true]);
    }

    // -------------------------------------------------------------------------
    // Conversions
    // -------------------------------------------------------------------------

    /**
     * POST /crm/leads/{lead}/convert-listing
     * Copies lead-level valuations to new Listing's Valuations tab.
     * Latest valuation surfaces on Listing Overview.
     */
    public function convertToListing(string $lead)
    {
        // TODO:
        // 1. Create Listing from lead data
        // 2. Copy all lead valuations to listing
        // 3. Dispatch lead_converted_to_listing event

        return redirect()->route('leads.show', $lead)
            ->with('success', 'Lead converted to Listing. Valuations carried over.');
    }

    /**
     * POST /crm/leads/{lead}/convert-customer
     */
    public function convertToCustomer(string $lead)
    {
        // TODO: lead_converted_to_customer event
        return redirect()->route('leads.index')
            ->with('success', 'Lead converted to Customer.');
    }

    // -------------------------------------------------------------------------
    // DNC / Merge
    // -------------------------------------------------------------------------

    public function markDnc(Request $request, string $lead)
    {
        // TODO: lead_dnc_set; block all outbound messages
        return response()->json(['success' => true]);
    }

    public function merge(Request $request, string $lead)
    {
        $request->validate(['merge_into' => 'required|string']);

        // TODO: lead_merged event; field-by-field pick
        return response()->json(['success' => true]);
    }

    // -------------------------------------------------------------------------
    // Valuations (Phase 3 update)
    // -------------------------------------------------------------------------

    /**
     * POST /crm/leads/{lead}/valuations/pull
     * Pull latest valuation from provider.
     * States: In progress → Succeeded / Failed (rate-limit message)
     */
    public function pullValuation(string $lead)
    {
        $record = collect($this->mockLeads())->firstWhere('id', $lead);

        if (empty($record['vrm']) && empty($record['vin'])) {
            return response()->json([
                'success' => false,
                'error'   => 'No VRM or VIN on this lead. Add one before pulling a valuation.',
            ], 422);
        }

        // Simulate provider call — replace with real provider SDK
        // 80 % success rate for demo
        $success = rand(1, 10) <= 8;

        if ($success) {
            $newVal = [
                'id'      => 'v' . rand(10000, 99999),
                'date'    => now()->toISOString(),
                'source'  => 'AutoProvider',
                'valuer'  => 'ProviderX',
                'amount'  => rand(8000, 25000),
                'notes'   => 'Auto-pulled',
                'comps'   => [],
                'applied' => false,
            ];

            // TODO: persist; dispatch valuation_fetched event with leadId, amount, source
            return response()->json([
                'success'   => true,
                'status'    => 'succeeded',
                'valuation' => $newVal,
                'message'   => 'Valuation fetched successfully.',
            ]);
        }

        // Simulate rate-limit / provider down
        return response()->json([
            'success' => false,
            'status'  => 'failed',
            'error'   => 'Provider returned an error. Please retry in a few minutes.',
        ], 503);
    }

    /**
     * POST /crm/leads/{lead}/valuations
     * Add manual valuation.
     */
    public function addValuation(Request $request, string $lead)
    {
        $request->validate([
            'source' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'valuer' => 'nullable|string',
            'notes'  => 'nullable|string',
            'comps'  => 'nullable|string',
        ]);

        $payload = [
            'id'      => 'v' . rand(10000, 99999),
            'date'    => now()->toISOString(),
            'source'  => $request->source,
            'valuer'  => $request->valuer,
            'amount'  => (float) $request->amount,
            'notes'   => $request->notes,
            'comps'   => $request->comps ? explode(',', $request->comps) : [],
            'applied' => false,
        ];

        // TODO: persist; dispatch valuation_added (lead context) event
        return response()->json([
            'success'   => true,
            'valuation' => $payload,
            'message'   => 'Valuation added.',
        ]);
    }

    /**
     * POST /crm/leads/{lead}/valuations/{valuation}/apply
     * Apply valuation to linked Listing (Guide and/or Reserve).
     * Only available when lead is linked to a Listing.
     */
    public function applyValuation(Request $request, string $lead, string $valuation)
    {
        $request->validate([
            'price_type' => 'required|in:guide,reserve,both',
        ]);

        $record = collect($this->mockLeads())->firstWhere('id', $lead);

        if (empty($record['linked_listing_id'])) {
            // Value stored; carries over on conversion
            return response()->json([
                'success' => false,
                'error'   => 'No linked listing found. Valuation saved and will carry over on conversion.',
            ], 422);
        }

        // TODO: update Listing guide/reserve price; dispatch valuation_applied (listing context) event
        // TODO: log before→after delta in audit
        return response()->json([
            'success'    => true,
            'message'    => 'Valuation applied to listing ' . $record['linked_listing_id'],
            'listing_id' => $record['linked_listing_id'],
        ]);
    }

    // -------------------------------------------------------------------------
    // Bulk actions
    // -------------------------------------------------------------------------

    public function bulkAssign(Request $request)
    {
        $request->validate(['lead_ids' => 'required|array', 'owner' => 'required|string']);
        // TODO: lead_owner_changed × N
        return response()->json(['success' => true]);
    }

    public function bulkStage(Request $request)
    {
        $request->validate(['lead_ids' => 'required|array', 'stage' => 'required|string']);
        // TODO: lead_board_moved × N
        return response()->json(['success' => true]);
    }

    public function bulkMessage(Request $request)
    {
        $request->validate(['lead_ids' => 'required|array', 'template_id' => 'nullable|string']);
        // TODO: message_sent × N; respect DNC + consent
        return response()->json(['success' => true]);
    }

    public function bulkTask(Request $request)
    {
        $request->validate(['lead_ids' => 'required|array', 'task_title' => 'required|string']);
        return response()->json(['success' => true]);
    }

    public function bulkMerge(Request $request)
    {
        $request->validate(['lead_ids' => 'required|array|min:2']);
        // TODO: lead_merged
        return response()->json(['success' => true]);
    }

    /**
     * POST /crm/leads/bulk/pull-valuations
     * Phase 3: Pull valuations for selected leads with VRM/VIN.
     * Returns per-lead job status for UI tracking.
     */
    public function bulkPullValuations(Request $request)
    {
        $request->validate(['lead_ids' => 'required|array']);

        $leads   = $this->mockLeads();
        $results = [];

        foreach ($request->lead_ids as $leadId) {
            $lead = collect($leads)->firstWhere('id', $leadId);

            if (!$lead || (empty($lead['vrm']) && empty($lead['vin']))) {
                $results[$leadId] = ['status' => 'skipped', 'message' => 'No VRM/VIN'];
                continue;
            }

            // Queue each lead for async fetch (in production dispatch a queued job)
            $results[$leadId] = ['status' => 'queued', 'message' => 'Queued for fetch'];
            // TODO: dispatch ValuationFetchJob::dispatch($leadId) — fires valuation_fetched on success
        }

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }
}