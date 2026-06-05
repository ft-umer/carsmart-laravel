<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsentController extends Controller
{
    /**
     * GET /crm/consent
     * C10 — Consent & Privacy overview
     */
    public function index()
    {
        return view('crm.consent', [
            'stats' => [
                'email_consent'    => 1248,
                'sms_consent'      => 892,
                'whatsapp_consent' => 634,
                'dnc_count'        => 23,
                'total_records'    => 1856,
            ],
            'dnc_list'  => $this->mockDncList(),
            'audit_log' => $this->mockAuditLog(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Consent updates — used by Person, Lead, Vendor records
    // -------------------------------------------------------------------------

    /**
     * PATCH /crm/consent/{entity_type}/{entity_id}
     * Update channel consent for any entity (person, lead, vendor)
     * Event: consent_updated
     */
    public function update(Request $request, string $entityType, string $entityId)
    {
        $request->validate([
            'channel' => 'required|in:email,sms,whatsapp',
            'value'   => 'required|boolean',
            'reason'  => 'required|string|max:1000',
        ]);

        // TODO: Validate role can change consent for this entity type
        // TODO: Persist consent change; create audit entry (actor, before, after, reason, timestamp)
        // TODO: Fire consent_updated event

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Consent updated.');
    }

    // -------------------------------------------------------------------------
    // Do-Not-Contact
    // -------------------------------------------------------------------------

    /**
     * POST /crm/consent/{entity_type}/{entity_id}/dnc
     * Set DNC flag — blocks all outbound channels
     * Event: person_dnc_set / lead_dnc_set / vendor_dnc_set
     */
    public function setDnc(Request $request, string $entityType, string $entityId)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // TODO: Set DNC flag; add to DNC list; create audit entry
        // TODO: Fire appropriate dnc_set event; immediately suppress any queued messages

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Do-Not-Contact flag set.');
    }

    /**
     * DELETE /crm/consent/{entity_type}/{entity_id}/dnc
     * Remove DNC flag (requires reason for audit)
     */
    public function removeDnc(Request $request, string $entityType, string $entityId)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // TODO: Clear DNC flag; create audit entry with reason and actor
        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Do-Not-Contact flag removed.');
    }

    // -------------------------------------------------------------------------
    // DNC list & audit
    // -------------------------------------------------------------------------

    /**
     * GET /crm/consent/dnc
     * Paginated DNC list
     */
    public function dncList(Request $request)
    {
        return response()->json($this->mockDncList());
    }

    /**
     * GET /crm/consent/audit
     * Consent audit log — filterable by event type, actor, date range
     */
    public function auditLog(Request $request)
    {
        $log = $this->mockAuditLog();

        if ($event = $request->get('event')) {
            $log = array_filter($log, fn($e) => $e['event'] === $event);
        }

        return response()->json(array_values($log));
    }

    // -------------------------------------------------------------------------
    // RTBF (Right to be Forgotten)
    // -------------------------------------------------------------------------

    /**
     * POST /crm/consent/rtbf/{entity_id}
     * Apply right-to-be-forgotten redaction to a person record
     * Super Admin only
     */
    public function applyRtbf(Request $request, string $entityId)
    {
        $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        // TODO: Apply redaction map (name, email, phone, address, files)
        // TODO: Log RTBF application with actor, timestamp, entity_id
        // TODO: Cannot be undone

        return response()->json(['ok' => true]);
    }

    /**
     * POST /crm/consent/rtbf/test
     * Preview what would be redacted (dry-run, no persistence)
     * Admin only
     */
    public function rtbfTest(Request $request)
    {
        $request->validate([
            'entity_id' => 'required|string',
        ]);

        // Return a preview of fields that would be redacted
        return response()->json([
            'fields' => [
                'name'    => '[REDACTED]',
                'email'   => '[REDACTED]',
                'phone'   => '[REDACTED]',
                'address' => '[REDACTED]',
                'files'   => 'purged (3 files)',
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Mock helpers
    // -------------------------------------------------------------------------

    private function mockDncList(): array
    {
        return [
            ['name'=>'Maria Santos','entity_id'=>'CST-003','type'=>'Customer','set_by'=>'AM','date'=>'3 Oct 2025','reason'=>'Requested removal from all comms'],
            ['name'=>'Quick Sales Ltd','entity_id'=>'VEN-018','type'=>'Vendor','set_by'=>'SR','date'=>'28 Sep 2025','reason'=>'Legal dispute pending'],
            ['name'=>'Robert Green','entity_id'=>'LED-2088','type'=>'Lead','set_by'=>'JR','date'=>'1 Oct 2025','reason'=>'Unsubscribed via email link'],
        ];
    }

    private function mockAuditLog(): array
    {
        return [
            ['when'=>'2 Oct 2025 14:32','actor'=>'AM','event'=>'consent_updated','subject'=>'Jane Doe (CST-001)','field'=>'email','before'=>'false','after'=>'true','reason'=>'Customer opted in via website form'],
            ['when'=>'3 Oct 2025 10:15','actor'=>'AM','event'=>'dnc_set','subject'=>'Maria Santos (CST-003)','field'=>'dnc','before'=>'false','after'=>'true','reason'=>'Requested removal from all comms'],
            ['when'=>'1 Oct 2025 09:00','actor'=>'JR','event'=>'consent_updated','subject'=>'Robert Green (LED-2088)','field'=>'sms','before'=>'true','after'=>'false','reason'=>'Unsubscribed via link'],
            ['when'=>'28 Sep 2025 16:42','actor'=>'SR','event'=>'dnc_set','subject'=>'Quick Sales Ltd (VEN-018)','field'=>'dnc','before'=>'false','after'=>'true','reason'=>'Legal dispute pending'],
        ];
    }
}
