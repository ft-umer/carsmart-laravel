<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    // G1 — DSAR
    public function dsar()
    {
        $queue = [
            ['id' => 'DSAR-001', 'subject' => 'Jane Doe',     'email' => 'jane@example.com', 'submitted' => '2025-10-01', 'sla_due' => '2025-10-31', 'owner' => 'A. Mills', 'status' => 'In progress'],
            ['id' => 'DSAR-002', 'subject' => 'Fast Cars Ltd','email' => 'info@fastcars.co',  'submitted' => '2025-10-05', 'sla_due' => '2025-11-04', 'owner' => 'J. Reid',  'status' => 'New'],
        ];
        $generated = [
            ['bundle' => 'DSAR-001-bundle.zip', 'subject' => 'Jane Doe', 'requested' => '2025-10-01', 'generated' => '2025-10-14', 'expires' => '2025-10-21'],
        ];
        return view('compliance.dsar', compact('queue', 'generated'));
    }

    // G2 — Right-to-erasure
    public function erasure()
    {
        $queue = [
            ['id' => 'RTF-001', 'subject' => 'John Smith', 'reason' => 'Customer request', 'submitted' => '2025-10-03', 'owner' => 'A. Mills', 'status' => 'Review'],
            ['id' => 'RTF-002', 'subject' => 'Quick Autos','reason' => 'Account closure',  'submitted' => '2025-10-08', 'owner' => 'J. Reid',  'status' => 'New'],
        ];
        return view('compliance.erasure', compact('queue'));
    }

    // G3 — Consent logs
    public function consentLogs()
    {
        $entries = [
            ['time' => '2025-10-14 11:45', 'subject' => 'Jane Doe',    'channel' => 'Email',   'before' => 'No',  'after' => 'Yes', 'actor' => 'J. Reid',  'reason' => 'Customer opted in online', 'source' => 'UI'],
            ['time' => '2025-10-13 09:00', 'subject' => 'Mark Hill',   'channel' => 'SMS',     'before' => 'Yes', 'after' => 'No',  'actor' => 'System',   'reason' => 'DNC import',               'source' => 'Import'],
            ['time' => '2025-10-12 14:30', 'subject' => 'Fast Cars Ltd','channel' => 'WhatsApp','before' => 'No',  'after' => 'Yes', 'actor' => 'A. Mills', 'reason' => 'Verbal consent captured',  'source' => 'UI'],
        ];
        return view('compliance.consent-logs', compact('entries'));
    }

    // G4 — KYC/KYB overrides log
    public function kycOverrides()
    {
        $entries = [
            ['time' => '2025-10-10 10:00', 'subject' => 'Fast Cars Ltd', 'before' => 'Needs docs', 'after' => 'Verified', 'actor' => 'Super Admin', 'reason' => 'Director confirmed via video call', 'attachment' => 'call-recording.mp4'],
            ['time' => '2025-09-28 15:20', 'subject' => 'Jane Doe',      'before' => 'In review',  'after' => 'Failed',   'actor' => 'Super Admin', 'reason' => 'Identity mismatch confirmed',      'attachment' => 'evidence.pdf'],
        ];
        return view('compliance.kyc-overrides', compact('entries'));
    }

    // G5 — Security & Sessions
    public function sessions()
    {
        $active = [
            ['user' => 'A. Mills', 'role' => 'Admin',  'ip' => '192.168.1.10', 'agent' => 'Chrome/Mac',   'started' => '2025-10-14 09:00', 'last_active' => '5 min ago'],
            ['user' => 'J. Reid',  'role' => 'CRM',    'ip' => '192.168.1.22', 'agent' => 'Safari/iPhone', 'started' => '2025-10-14 08:30', 'last_active' => '2 min ago'],
        ];
        $history = [
            ['time' => '2025-10-14 09:00', 'user' => 'A. Mills',  'result' => 'Success', 'ip' => '192.168.1.10', 'agent' => 'Chrome/Mac',    'location' => 'London, UK'],
            ['time' => '2025-10-14 08:30', 'user' => 'J. Reid',   'result' => 'Success', 'ip' => '192.168.1.22', 'agent' => 'Safari/iPhone', 'location' => 'Manchester, UK'],
            ['time' => '2025-10-13 16:30', 'user' => 'B. Carter', 'result' => 'Failed',  'ip' => '10.0.0.99',   'agent' => 'Firefox/Win',   'location' => 'Unknown'],
        ];
        return view('compliance.sessions', compact('active', 'history'));
    }

    // G6 — Integrations
    public function integrations()
    {
        $integrations = [
            ['name' => 'Payment Service', 'icon' => 'ki-dollar',    'public_key' => 'pk_live_xxxx', 'webhook' => 'https://app.carsmart.co/webhooks/stripe', 'status' => 'Active'],
            ['name' => 'Identity / KYC',  'icon' => 'ki-shield',    'public_key' => 'key_xxxx',     'webhook' => 'https://app.carsmart.co/webhooks/kyc',    'status' => 'Active'],
            ['name' => 'Logistics',       'icon' => 'ki-truck',     'public_key' => 'lg_xxxx',      'webhook' => 'https://app.carsmart.co/webhooks/logistics','status' => 'Active'],
        ];
        return view('compliance.integrations', compact('integrations'));
    }

    // G7 — Anti-fraud & rate limits
    public function antiFraud()
    {
        $rules = [
            ['area' => 'Bids',      'metric' => 'Per minute', 'threshold' => 10,  'action' => 'Flag'],
            ['area' => 'Payouts',   'metric' => 'Per day',    'threshold' => 5,   'action' => 'Review'],
            ['area' => 'Messaging', 'metric' => 'Per hour',   'threshold' => 50,  'action' => 'Block'],
        ];
        $exceptions = [
            ['subject' => 'Fast Cars Ltd', 'rule' => 'Bids per minute', 'window' => '7 days', 'reason' => 'Auction event', 'actor' => 'A. Mills'],
        ];
        return view('compliance.anti-fraud', compact('rules', 'exceptions'));
    }

    // G8 — Retention enforcement
    public function retention()
    {
        return view('compliance.retention');
    }
}