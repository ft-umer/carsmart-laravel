<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class AutomationsController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // A0 — Overview / Journeys
    // ──────────────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $journeyData = [
            ['id' => 1, 'name' => 'Lead Welcome',          'trigger' => 'lead.created',            'channels' => ['email'],             'cadence' => 'Immediate',  'status' => 'Active',  'last_run' => now()->subHours(2),  'owner' => 'Alice Morgan'],
            ['id' => 2, 'name' => 'Auction Reminder',      'trigger' => 'auction.starts_in_24h',   'channels' => ['email', 'sms'],      'cadence' => '24h before', 'status' => 'Active',  'last_run' => now()->subHours(6),  'owner' => 'Ben Carter'],
            ['id' => 3, 'name' => 'Listing Published',     'trigger' => 'listing.published',       'channels' => ['email'],             'cadence' => 'Immediate',  'status' => 'Active',  'last_run' => now()->subDays(1),   'owner' => 'Alice Morgan'],
            ['id' => 4, 'name' => 'Deal Signed Follow-up', 'trigger' => 'deal.signed',             'channels' => ['email', 'whatsapp'], 'cadence' => '+1 day',     'status' => 'Paused',  'last_run' => now()->subDays(3),   'owner' => 'Clara James'],
            ['id' => 5, 'name' => 'Vendor Onboarding',     'trigger' => 'vendor.registered',       'channels' => ['email'],             'cadence' => 'Drip 5-day', 'status' => 'Active',  'last_run' => now()->subDays(2),   'owner' => 'David Singh'],
            ['id' => 6, 'name' => 'KYC Nudge',             'trigger' => 'kyc.pending_48h',         'channels' => ['sms', 'email'],      'cadence' => '+48h',       'status' => 'Draft',   'last_run' => null,                'owner' => 'Emma Walsh'],
            ['id' => 7, 'name' => 'Logistics Update',      'trigger' => 'logistics.status_changed', 'channels' => ['sms'],               'cadence' => 'Immediate',  'status' => 'Active',  'last_run' => now()->subHours(1),  'owner' => 'Ben Carter'],
        ];

        $page     = $request->input('page', 1);
        $perPage  = 20;
        $slice    = array_slice($journeyData, ($page - 1) * $perPage, $perPage);
        $journeys = new LengthAwarePaginator($slice, count($journeyData), $perPage, $page, ['path' => $request->url()]);

        $stats = [
            'active'     => count(array_filter($journeyData, fn($j) => $j['status'] === 'Active')),
            'paused'     => count(array_filter($journeyData, fn($j) => $j['status'] === 'Paused')),
            'runs_today' => 142,
            'failures'   => 3,
        ];

        $activeTab = 'journeys';

        return view('automations.index', compact('journeys', 'stats', 'activeTab'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // A1 — Journey Builder
    // ──────────────────────────────────────────────────────────────────────────

    public function create(): View
    {
        return view('automations.edit', ['journey' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('automations.index')->with('success', 'Journey created.');
    }

    public function edit(int $id): View
    {
        $journey = [
            'id'               => $id,
            'name'             => 'Lead Welcome',
            'trigger'          => 'lead.created',
            'status'           => 'Active',
            'channels'         => ['email'],
            'cadence'          => 'Immediate',
            'quiet_hours_start' => '22:00',
            'quiet_hours_end'  => '07:00',
            'max_per_day'      => 5,
            'start_date'       => now()->subDays(30)->toDateString(),
            'end_date'         => null,
            'steps'            => [
                ['type' => 'send_email',   'template_id' => 1, 'delay_minutes' => 0],
                ['type' => 'wait',         'delay_minutes' => 1440],
                ['type' => 'send_email',   'template_id' => 2, 'delay_minutes' => 0],
            ],
            'owner'            => 'Alice Morgan',
            'published_at'     => now()->subDays(30),
        ];

        return view('automations.edit', compact('journey'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        return back()->with('success', 'Journey saved.');
    }

    public function destroy(int $id): RedirectResponse
    {
        return redirect()->route('automations.index')->with('success', 'Journey archived.');
    }

    public function pause(int $id): RedirectResponse
    {
        return back()->with('success', 'Journey paused.');
    }

    public function resume(int $id): RedirectResponse
    {
        return back()->with('success', 'Journey resumed.');
    }

    public function duplicate(int $id): RedirectResponse
    {
        return redirect()->route('automations.index')->with('success', 'Journey duplicated.');
    }

    public function publish(int $id): RedirectResponse
    {
        return back()->with('success', 'Journey is now active.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // A2 — Triggers Registry
    // ──────────────────────────────────────────────────────────────────────────

    public function triggers(): View
    {
        $triggers = [
            'Listings' => [
                ['key' => 'listing.created',   'label' => 'Listing created',   'description' => 'Fires when a new listing is submitted.',      'payload_preview' => '{"listing_id": 123, "status": "QA"}'],
                ['key' => 'listing.published',  'label' => 'Listing published', 'description' => 'Fires when a listing goes live.',             'payload_preview' => '{"listing_id": 123, "status": "Published"}'],
                ['key' => 'listing.ended',      'label' => 'Listing ended',     'description' => 'Fires when a listing closes without a deal.', 'payload_preview' => '{"listing_id": 123, "status": "Ended"}'],
            ],
            'Auctions' => [
                ['key' => 'auction.created',        'label' => 'Auction created',       'description' => 'New auction is set up.',                   'payload_preview' => '{"auction_id": 6}'],
                ['key' => 'auction.starts_in_24h',  'label' => 'Auction starts in 24h', 'description' => 'Reminder 24 hours before auction start.',  'payload_preview' => '{"auction_id": 6, "starts_at": "..."}'],
                ['key' => 'auction.lot.won',         'label' => 'Lot won',               'description' => 'A vendor wins a lot.',                     'payload_preview' => '{"lot_id": 41, "vendor_id": 7}'],
            ],
            'Leads' => [
                ['key' => 'lead.created',   'label' => 'Lead created',  'description' => 'A new lead is received.',       'payload_preview' => '{"lead_id": 1049, "source": "website"}'],
                ['key' => 'lead.qualified', 'label' => 'Lead qualified', 'description' => 'Lead is marked as qualified.',  'payload_preview' => '{"lead_id": 1049}'],
            ],
            'Deals' => [
                ['key' => 'deal.signed',   'label' => 'Deal signed',  'description' => 'Purchase agreement is signed.', 'payload_preview' => '{"deal_id": 87}'],
                ['key' => 'deal.completed', 'label' => 'Deal completed', 'description' => 'Handover is complete.',        'payload_preview' => '{"deal_id": 87}'],
            ],
            'KYC/KYB' => [
                ['key' => 'kyc.pending_48h', 'label' => 'KYC pending 48h', 'description' => 'Identity check has been pending for 48 hours.', 'payload_preview' => '{"user_id": 22}'],
                ['key' => 'kyb.approved',   'label' => 'KYB approved',   'description' => 'Vendor business verification approved.',        'payload_preview' => '{"vendor_id": 7}'],
            ],
        ];

        return view('automations.triggers', compact('triggers'));
    }

    public function triggerSchema(string $trigger): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'key'    => $trigger,
            'fields' => [
                ['name' => 'id',   'type' => 'integer', 'required' => true],
                ['name' => 'name', 'type' => 'string',  'required' => false],
            ],
        ]);
    }

    public function testFire(Request $request, string $trigger): \Illuminate\Http\JsonResponse
    {
        return response()->json(['run_id' => 'test-' . uniqid(), 'status' => 'queued']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Templates
    // ──────────────────────────────────────────────────────────────────────────

    public function templates(): View
    {
        $templates = collect([
            ['id' => 1, 'name' => 'Lead Welcome Email',        'channel' => 'Email',    'subject' => 'Welcome to Carsmart!',        'body' => 'Hi {{first_name}}, thanks for getting in touch.',  'requires_approval' => false, 'journeys' => ['Lead Welcome']],
            ['id' => 2, 'name' => 'Auction Reminder SMS',      'channel' => 'SMS',      'subject' => null,                          'body' => 'Your auction starts in 24h. Log in to bid.',        'requires_approval' => false, 'journeys' => ['Auction Reminder']],
            ['id' => 3, 'name' => 'Deal Signed Confirmation',  'channel' => 'Email',    'subject' => 'Your purchase is confirmed',  'body' => 'Hi {{first_name}}, your purchase agreement is signed.', 'requires_approval' => true, 'journeys' => ['Deal Signed Follow-up']],
            ['id' => 4, 'name' => 'KYC Nudge WhatsApp',        'channel' => 'WhatsApp', 'subject' => null,                          'body' => 'Please complete your identity check to continue.',  'requires_approval' => true,  'journeys' => ['KYC Nudge']],
        ]);

        return view('automations.templates', compact('templates'));
    }

    public function storeTemplate(Request $request): RedirectResponse
    {
        return back()->with('success', 'Template saved.');
    }

    public function updateTemplate(Request $request, int $id): RedirectResponse
    {
        return back()->with('success', 'Template updated.');
    }

    public function destroyTemplate(int $id): RedirectResponse
    {
        return back()->with('success', 'Template deleted.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // A3 — Runs & Monitoring
    // ──────────────────────────────────────────────────────────────────────────

    public function runs(Request $request): View
    {
        $runData = [
            ['id' => 1, 'journey_id' => 1, 'journey' => ['name' => 'Lead Welcome'],          'status' => 'Succeeded', 'trigger_key' => 'lead.created',          'object_ref' => 'LD-1049', 'steps_run' => 3, 'duration_ms' => 342,  'created_at' => now()->subMinutes(5)],
            ['id' => 2, 'journey_id' => 2, 'journey' => ['name' => 'Auction Reminder'],      'status' => 'Succeeded', 'trigger_key' => 'auction.starts_in_24h', 'object_ref' => 'AUC-006', 'steps_run' => 2, 'duration_ms' => 210,  'created_at' => now()->subMinutes(18)],
            ['id' => 3, 'journey_id' => 3, 'journey' => ['name' => 'Listing Published'],     'status' => 'Failed',    'trigger_key' => 'listing.published',      'object_ref' => 'CS-088',  'steps_run' => 1, 'duration_ms' => 120,  'created_at' => now()->subMinutes(40)],
            ['id' => 4, 'journey_id' => 5, 'journey' => ['name' => 'Vendor Onboarding'],     'status' => 'Succeeded', 'trigger_key' => 'vendor.registered',      'object_ref' => 'VND-021', 'steps_run' => 5, 'duration_ms' => 890,  'created_at' => now()->subHours(1)],
            ['id' => 5, 'journey_id' => 7, 'journey' => ['name' => 'Logistics Update'],      'status' => 'Skipped',   'trigger_key' => 'logistics.status_changed', 'object_ref' => 'LOG-0505', 'steps_run' => 0, 'duration_ms' => 10,   'created_at' => now()->subHours(2)],
            ['id' => 6, 'journey_id' => 1, 'journey' => ['name' => 'Lead Welcome'],          'status' => 'Succeeded', 'trigger_key' => 'lead.created',          'object_ref' => 'LD-1048', 'steps_run' => 3, 'duration_ms' => 298,  'created_at' => now()->subHours(3)],
            ['id' => 7, 'journey_id' => 2, 'journey' => ['name' => 'Auction Reminder'],      'status' => 'Failed',    'trigger_key' => 'auction.starts_in_24h', 'object_ref' => 'AUC-005', 'steps_run' => 1, 'duration_ms' => 88,   'created_at' => now()->subHours(4)],
        ];

        // Filters
        if ($request->journey_id) {
            $runData = array_filter($runData, fn($r) => $r['journey_id'] == $request->journey_id);
        }

        if ($request->status) {
            $runData = array_filter($runData, fn($r) => $r['status'] === $request->status);
        }

        $runData = array_values($runData);

        $page    = $request->input('page', 1);
        $perPage = 50;
        $slice   = array_slice($runData, ($page - 1) * $perPage, $perPage);
        $runs    = new LengthAwarePaginator($slice, count($runData), $perPage, $page, ['path' => $request->url()]);

        $journeys = collect([
            ['id' => 1, 'name' => 'Lead Welcome'],
            ['id' => 2, 'name' => 'Auction Reminder'],
            ['id' => 3, 'name' => 'Listing Published'],
            ['id' => 4, 'name' => 'Deal Signed Follow-up'],
            ['id' => 5, 'name' => 'Vendor Onboarding'],
            ['id' => 6, 'name' => 'KYC Nudge'],
            ['id' => 7, 'name' => 'Logistics Update'],
        ]);

        $stats = [
            'succeeded' => count(array_filter($runData, fn($r) => $r['status'] === 'Succeeded')),
            'failed'    => count(array_filter($runData, fn($r) => $r['status'] === 'Failed')),
            'skipped'   => count(array_filter($runData, fn($r) => $r['status'] === 'Skipped')),
        ];

        return view('automations.runs', compact('runs', 'journeys', 'stats'));
    }

    public function runLog(int $id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'id'          => $id,
            'status'      => 'Succeeded',
            'journey'     => ['name' => 'Lead Welcome'],
            'steps_run'   => 3,
            'duration_ms' => 342,
            'log'         => ['Step 1 — send_email: OK', 'Step 2 — wait: 1440min', 'Step 3 — send_email: OK'],
        ]);
    }

    public function retry(int $id): RedirectResponse
    {
        return back()->with('success', 'Retry queued.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Suppressions
    // ──────────────────────────────────────────────────────────────────────────

    public function suppressions(Request $request): View
    {
        $suppressionData = [
            ['id' => 1, 'email' => 'john@example.com',   'phone' => null,          'name' => 'John Smith',   'channel' => 'Email',    'reason' => 'Unsubscribed', 'source' => 'User',   'expires_at' => null,                   'created_at' => now()->subDays(10)],
            ['id' => 2, 'email' => null,                  'phone' => '+447700900001', 'name' => 'Sarah Jones', 'channel' => 'SMS',      'reason' => 'Opt-out',      'source' => 'Manual', 'expires_at' => now()->addDays(30),     'created_at' => now()->subDays(7)],
            ['id' => 3, 'email' => 'mike@example.com',   'phone' => null,          'name' => 'Mike Davis',   'channel' => 'Email',    'reason' => 'Bounced',      'source' => 'System', 'expires_at' => null,                   'created_at' => now()->subDays(5)],
            ['id' => 4, 'email' => null,                  'phone' => '+447700900002', 'name' => 'Lucy Brown',  'channel' => 'WhatsApp', 'reason' => 'User request',  'source' => 'Manual', 'expires_at' => now()->addDays(14),    'created_at' => now()->subDays(3)],
            ['id' => 5, 'email' => 'vendor@autohub.co.uk', 'phone' => null,         'name' => 'AutoHub Ltd',  'channel' => 'Email',    'reason' => 'Quiet hours',   'source' => 'System', 'expires_at' => now()->addDays(1),     'created_at' => now()->subHours(6)],
        ];

        if ($request->search) {
            $s = $request->search;
            $suppressionData = array_filter($suppressionData, fn($r) => stripos($r['email'] ?? '', $s) !== false || stripos($r['name'] ?? '', $s) !== false);
        }

        if ($request->reason) {
            $suppressionData = array_filter($suppressionData, fn($r) => $r['reason'] === $request->reason);
        }

        $suppressionData = array_values($suppressionData);

        $page        = $request->input('page', 1);
        $perPage     = 50;
        $slice       = array_slice($suppressionData, ($page - 1) * $perPage, $perPage);
        $suppressions = new LengthAwarePaginator($slice, count($suppressionData), $perPage, $page, ['path' => $request->url()]);

        return view('automations.suppressions', compact('suppressions'));
    }

    public function storeSuppression(Request $request): RedirectResponse
    {
        return back()->with('success', 'Suppression added.');
    }

    public function destroySuppression(int $id): RedirectResponse
    {
        return back()->with('success', 'Suppression removed.');
    }
}
