<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class TasksController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // T0 — Tasks Index
    // ──────────────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $activeTab = $request->input('tab', 'my');

        $allTasks = [
            ['id' => 1,  'title' => 'QA review — CS-091 BMW 5 Series',     'object_ref' => 'CS-091', 'type' => 'Listing',   'due_at' => now()->addHours(3),   'priority' => 'Urgent',  'owner' => 'Alice Morgan', 'source_module' => 'QA',      'completed' => false],
            ['id' => 2,  'title' => 'Chase missing KYB docs — AutoHub',     'object_ref' => null,     'type' => 'Lead',      'due_at' => now()->addHours(5),   'priority' => 'High',    'owner' => 'Alice Morgan', 'source_module' => 'KYC/KYB', 'completed' => false],
            ['id' => 3,  'title' => 'Approve payout — DriveMore Group',     'object_ref' => 'PAY-042','type' => 'Payout',    'due_at' => now()->subHours(1),   'priority' => 'Urgent',  'owner' => 'Ben Carter',   'source_module' => 'Finance', 'completed' => false],
            ['id' => 4,  'title' => 'Resolve dispute DSP-2204',             'object_ref' => 'DSP-2204','type' => 'Dispute',  'due_at' => now()->addDay(),      'priority' => 'High',    'owner' => 'Ben Carter',   'source_module' => 'Disputes','completed' => false],
            ['id' => 5,  'title' => 'Book collection — LOG-0505',           'object_ref' => 'LOG-0505','type' => 'Logistics','due_at' => now()->addDays(2),    'priority' => 'Normal',  'owner' => 'Clara James',  'source_module' => 'Logistics','completed' => false],
            ['id' => 6,  'title' => 'Update listing photos — CS-088',       'object_ref' => 'CS-088', 'type' => 'Listing',   'due_at' => now()->addDays(1),    'priority' => 'Normal',  'owner' => 'Clara James',  'source_module' => 'Manual',  'completed' => false],
            ['id' => 7,  'title' => 'Send deal summary to buyer',           'object_ref' => 'CS-087', 'type' => 'Deal',      'due_at' => now()->subDays(1),    'priority' => 'High',    'owner' => 'David Singh',  'source_module' => 'Deals',   'completed' => false],
            ['id' => 8,  'title' => 'Confirm auction lot order',            'object_ref' => 'AUC-006','type' => 'Auction',   'due_at' => today(),              'priority' => 'Normal',  'owner' => 'David Singh',  'source_module' => 'Auctions','completed' => false],
            ['id' => 9,  'title' => 'Send valuation to seller — CS-092',    'object_ref' => 'CS-092', 'type' => 'Valuation', 'due_at' => now()->addHours(8),   'priority' => 'Normal',  'owner' => 'Emma Walsh',   'source_module' => 'Valuations','completed' => false],
            ['id' => 10, 'title' => 'Follow up LD-1049',                    'object_ref' => 'LD-1049','type' => 'Lead',      'due_at' => today(),              'priority' => 'High',    'owner' => 'Emma Walsh',   'source_module' => 'CRM',     'completed' => false],
            ['id' => 11, 'title' => 'Archive old listings batch',           'object_ref' => null,     'type' => 'General',   'due_at' => now()->addDays(5),    'priority' => 'Low',     'owner' => 'Alice Morgan', 'source_module' => 'Manual',  'completed' => true],
            ['id' => 12, 'title' => 'Review auction settings for AUC-007',  'object_ref' => 'AUC-007','type' => 'Auction',   'due_at' => now()->addDays(3),    'priority' => 'Normal',  'owner' => null,           'source_module' => 'Auctions','completed' => false],
        ];

        // Tab scoping
        $myOwner = 'Alice Morgan'; // simulated current user

        $filtered = match ($activeTab) {
            'my'     => array_filter($allTasks, fn ($t) => $t['owner'] === $myOwner),
            'team'   => array_filter($allTasks, fn ($t) => in_array($t['owner'], ['Ben Carter', 'Clara James', 'David Singh'])),
            'queues' => array_filter($allTasks, fn ($t) => $t['owner'] === null),
            default  => array_filter($allTasks, fn ($t) => $t['owner'] === $myOwner),
        };

        // Search filter
        if ($request->search) {
            $s = $request->search;
            $filtered = array_filter($filtered, fn ($t) => stripos($t['title'], $s) !== false || stripos($t['object_ref'] ?? '', $s) !== false);
        }

        // Type filter
        if ($request->type) {
            $filtered = array_filter($filtered, fn ($t) => $t['type'] === $request->type);
        }

        // Priority filter
        if ($request->priority) {
            $filtered = array_filter($filtered, fn ($t) => $t['priority'] === $request->priority);
        }

        // Due filter
        if ($request->due) {
            $filtered = match ($request->due) {
                'today'     => array_filter($filtered, fn ($t) => $t['due_at'] && \Carbon\Carbon::parse($t['due_at'])->isToday()),
                'this_week' => array_filter($filtered, fn ($t) => $t['due_at'] && \Carbon\Carbon::parse($t['due_at'])->isSameWeek()),
                'overdue'   => array_filter($filtered, fn ($t) => $t['due_at'] && \Carbon\Carbon::parse($t['due_at'])->isPast() && !$t['completed']),
                default     => $filtered,
            };
        }

        $filtered = array_values($filtered);

        $page    = $request->input('page', 1);
        $perPage = 30;
        $slice   = array_slice($filtered, ($page - 1) * $perPage, $perPage);
        $tasks   = new LengthAwarePaginator($slice, count($filtered), $perPage, $page, ['path' => $request->url()]);

        $owners = collect([
            ['id' => 1, 'name' => 'Alice Morgan'],
            ['id' => 2, 'name' => 'Ben Carter'],
            ['id' => 3, 'name' => 'Clara James'],
            ['id' => 4, 'name' => 'David Singh'],
            ['id' => 5, 'name' => 'Emma Walsh'],
        ]);

        $stats = [
            'due_today'   => count(array_filter($allTasks, fn ($t) => $t['due_at'] && \Carbon\Carbon::parse($t['due_at'])->isToday() && !$t['completed'])),
            'overdue'     => count(array_filter($allTasks, fn ($t) => $t['due_at'] && \Carbon\Carbon::parse($t['due_at'])->isPast() && !$t['completed'])),
            'in_progress' => count(array_filter($allTasks, fn ($t) => !$t['completed'] && $t['owner'] === $myOwner)),
            'done_today'  => 3,
            'my_count'    => count(array_filter($allTasks, fn ($t) => $t['owner'] === $myOwner && !$t['completed'])),
            'team_count'  => count(array_filter($allTasks, fn ($t) => in_array($t['owner'], ['Ben Carter', 'Clara James', 'David Singh']) && !$t['completed'])),
            'queue_count' => count(array_filter($allTasks, fn ($t) => $t['owner'] === null && !$t['completed'])),
        ];

        return view('tasks.index', compact('tasks', 'stats', 'owners', 'activeTab'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Show single task
    // ──────────────────────────────────────────────────────────────────────────

    public function show(int $id): View
    {
        $task = [
            'id'            => $id,
            'title'         => 'QA review — CS-091 BMW 5 Series',
            'object_ref'    => 'CS-091',
            'type'          => 'Listing',
            'due_at'        => now()->addHours(3),
            'priority'      => 'Urgent',
            'owner'         => 'Alice Morgan',
            'source_module' => 'QA',
            'completed'     => false,
            'notes'         => 'Vehicle photos need re-check. DVLA check pending.',
        ];

        return view('tasks.show', compact('task'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Create
    // ──────────────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $id = rand(100, 999);

        if ($request->expectsJson()) {
            return response()->json(['id' => $id, 'title' => $request->input('title', 'New task')], 201);
        }

        return back()->with('success', 'Task created.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Update
    // ──────────────────────────────────────────────────────────────────────────

    public function update(Request $request, int $id): RedirectResponse
    {
        return back()->with('success', 'Task updated.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Delete
    // ──────────────────────────────────────────────────────────────────────────

    public function destroy(int $id): RedirectResponse
    {
        return back()->with('success', 'Task deleted.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Complete
    // ──────────────────────────────────────────────────────────────────────────

    public function complete(int $id): JsonResponse|RedirectResponse
    {
        if (request()->expectsJson()) {
            return response()->json(['status' => 'completed']);
        }

        return back()->with('success', 'Task marked complete.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Snooze
    // ──────────────────────────────────────────────────────────────────────────

    public function snooze(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $newDue = now()->addHour();

        if (request()->expectsJson()) {
            return response()->json(['due_at' => $newDue->toIso8601String()]);
        }

        return back()->with('success', 'Task snoozed.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Assign
    // ──────────────────────────────────────────────────────────────────────────

    public function assign(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $ownerName = 'Ben Carter';

        if (request()->expectsJson()) {
            return response()->json(['status' => 'assigned', 'owner' => $ownerName]);
        }

        return back()->with('success', 'Task assigned to ' . $ownerName);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Bulk actions
    // ──────────────────────────────────────────────────────────────────────────

    public function bulkComplete(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        return response()->json(['status' => 'ok', 'count' => count($ids)]);
    }

    public function bulkAssign(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        return response()->json(['status' => 'ok', 'count' => count($ids)]);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        return response()->json(['status' => 'ok', 'count' => count($ids)]);
    }
}