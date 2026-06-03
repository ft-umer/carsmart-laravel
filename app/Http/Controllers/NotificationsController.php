<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationsController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // N0 — Notifications Centre
    // ──────────────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $activeType = $request->input('type', 'All');
        $unreadOnly = $request->boolean('unread_only');

        $allNotifications = [
            ['id' => 'n-001', 'type' => 'Listing',    'title' => 'New listing submitted',              'summary' => 'CS-091 BMW 5 Series 2021 is ready for QA review.',              'object' => 'CS-091', 'action_url' => '#', 'read' => false, 'time' => now()->subMinutes(5)],
            ['id' => 'n-002', 'type' => 'Auction',    'title' => 'Auction AUC-2025-006 going live',    'summary' => 'Auction starts in 30 minutes — 12 lots confirmed.',              'object' => 'AUC-006','action_url' => '#', 'read' => false, 'time' => now()->subMinutes(18)],
            ['id' => 'n-003', 'type' => 'Valuation',  'title' => 'Bulk valuation job completed',       'summary' => '48 valuations fetched, 2 failed.',                              'object' => null,     'action_url' => '#', 'read' => false, 'time' => now()->subMinutes(42)],
            ['id' => 'n-004', 'type' => 'Lead',       'title' => 'New lead assigned to you',           'summary' => 'LD-1049 from Website — 2018 Mercedes C-Class enquiry.',          'object' => 'LD-1049','action_url' => '#', 'read' => true,  'time' => now()->subHours(1)],
            ['id' => 'n-005', 'type' => 'Deal',       'title' => 'Deal signed — CS-087',               'summary' => 'Vendor AutoHub Ltd has signed the purchase agreement.',          'object' => 'CS-087', 'action_url' => '#', 'read' => true,  'time' => now()->subHours(2)],
            ['id' => 'n-006', 'type' => 'Finance',    'title' => 'Payout approved',                    'summary' => '£18,400 payout to DriveMore Group has been approved.',           'object' => null,     'action_url' => '#', 'read' => true,  'time' => now()->subHours(3)],
            ['id' => 'n-007', 'type' => 'System',     'title' => 'Scheduled maintenance tonight',      'summary' => 'Platform will be read-only from 01:00–02:00 UTC.',               'object' => null,     'action_url' => '#', 'read' => false, 'time' => now()->subHours(5)],
            ['id' => 'n-008', 'type' => 'Logistics',  'title' => 'Collection delayed — LOG-0502',      'summary' => 'FastHaul reported a 4-hour delay on collection for CS-072.',     'object' => 'LOG-0502','action_url' => '#', 'read' => true,  'time' => now()->subHours(6)],
            ['id' => 'n-009', 'type' => 'Listing',    'title' => 'Listing CS-088 published',           'summary' => 'VW Polo 2020 is now live on the platform.',                      'object' => 'CS-088', 'action_url' => '#', 'read' => true,  'time' => now()->subDays(1)],
            ['id' => 'n-010', 'type' => 'Automations','title' => 'Journey "Lead Welcome" paused',      'summary' => 'Daily send limit reached — journey auto-paused.',                'object' => null,     'action_url' => '#', 'read' => true,  'time' => now()->subDays(1)],
        ];

        // Filter by type
        if ($activeType !== 'All') {
            $allNotifications = array_filter($allNotifications, fn ($n) => $n['type'] === $activeType);
        }

        // Filter unread only
        if ($unreadOnly) {
            $allNotifications = array_filter($allNotifications, fn ($n) => !$n['read']);
        }

        $allNotifications = array_values($allNotifications);

        $page  = $request->input('page', 1);
        $perPage = 30;
        $slice  = array_slice($allNotifications, ($page - 1) * $perPage, $perPage);

        $notifications = new LengthAwarePaginator($slice, count($allNotifications), $perPage, $page, [
            'path' => $request->url(),
        ]);

        $unreadCount = count(array_filter($allNotifications, fn ($n) => !$n['read']));

        $valuationSummaries = collect([
            ['journey_id' => 'vbj-001', 'succeeded' => 48, 'failed' => 2, 'completed_at' => now()->subMinutes(42)],
        ]);

        return view('notifications.index', compact(
            'notifications', 'unreadCount',
            'activeType', 'unreadOnly',
            'valuationSummaries'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Mark single notification read
    // ──────────────────────────────────────────────────────────────────────────

    public function markRead(string $id): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Mark all read
    // ──────────────────────────────────────────────────────────────────────────

    public function markAllRead(): RedirectResponse
    {
        return back()->with('success', 'All notifications marked as read.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Mute a notification type
    // ──────────────────────────────────────────────────────────────────────────

    public function mute(Request $request, string $id): JsonResponse
    {
        return response()->json(['status' => 'muted', 'type' => 'System']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Delete a notification
    // ──────────────────────────────────────────────────────────────────────────

    public function destroy(string $id): JsonResponse
    {
        return response()->json(['status' => 'deleted']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // N1 — Notification Preferences
    // ──────────────────────────────────────────────────────────────────────────

    public function preferences(): View
    {
        $prefs = [
            'digest_frequency'       => 'daily',
            'digest_time'            => '08:00',
            'personal_quiet_enabled' => true,
            'personal_quiet_start'   => '22:00',
            'personal_quiet_end'     => '07:00',
        ];

        $userPrefs = $this->defaultChannelMatrix();

        return view('notifications.preferences', compact('prefs', 'userPrefs'));
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        return back()->with('success', 'Notification preferences saved.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function defaultChannelMatrix(): array
    {
        $categories = [
            'listings', 'auctions', 'valuations', 'leads',
            'deals', 'finance', 'logistics', 'disputes',
            'kyc_kyb', 'automations', 'system',
        ];

        return collect($categories)->mapWithKeys(fn ($cat) => [
            $cat => [
                'inapp'    => true,
                'email'    => in_array($cat, ['deals', 'finance', 'system']),
                'sms'      => false,
                'whatsapp' => false,
            ],
        ])->all();
    }
}