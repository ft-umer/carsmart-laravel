<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PayoutsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | P4: Payout approvals queue
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $status = $request->input('status', '');
        $owner  = $request->input('owner', '');

        // --- Replace with real Eloquent query ---
        $payouts = $this->mockPayouts();

        if ($status) {
            $payouts = array_filter($payouts, fn($p) => ($p['status'] ?? '') === $status);
        }
        if ($owner) {
            $payouts = array_filter($payouts, fn($p) => ($p['owner'] ?? '') === $owner);
        }

        \Illuminate\Support\Facades\Log::info('payouts_index_viewed', [
            'user' => $request->user()?->id,
        ]);

        return view('payments.payouts', [
            'payouts' => array_values($payouts),
            'total'   => count($payouts),
            'status'  => $status,
            'owner'   => $owner,
            'owners'  => ['AM', 'JB', 'SK', 'CL'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Approve payout
    |--------------------------------------------------------------------------
    */
    public function approve(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $payout = $this->findPayout($id);

        // Guard: handover docs must be complete
        if (!($payout['handover_docs_complete'] ?? false)) {
            return back()->withErrors(['id' => 'Handover documents must be complete before approving payout.']);
        }

        // Guard: optional two-person approval check
        if (config('carsmart.two_person_payout_approval', false)) {
            $previousApprover = collect($payout['approval_log'] ?? [])
                ->where('action', 'Approved')
                ->pluck('by')
                ->first();
            if ($previousApprover && $previousApprover === $request->user()?->name) {
                return back()->withErrors(['id' => 'A second approver is required. You already approved this payout.']);
            }
        }

        // --- Replace with real approval + fund release ---

        \Illuminate\Support\Facades\Log::info('payout_approved', [
            'user'      => $request->user()?->id,
            'payout_id' => $id,
            'note'      => $validated['note'] ?? null,
        ]);

        return back()->with('success', 'Payout approved and queued for release.');
    }

    /*
    |--------------------------------------------------------------------------
    | Reject payout
    |--------------------------------------------------------------------------
    */
    public function reject(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // --- Replace with real rejection + notification ---

        \Illuminate\Support\Facades\Log::info('payout_rejected', [
            'user'      => $request->user()?->id,
            'payout_id' => $id,
            'reason'    => $validated['reason'],
        ]);

        return back()->with('success', 'Payout rejected. Requester notified.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / mock data
    |--------------------------------------------------------------------------
    */
    private function findPayout(string $id): ?array
    {
        return collect($this->mockPayouts())->firstWhere('id', $id);
    }

    private function mockPayouts(): array
    {
        $now = now();
        return [
            [
                'id'                      => 'PAY-0042',
                'ref'                     => 'PAY-0042',
                'vendor_name'             => 'Premium Autos',
                'amount'                  => 22050,
                'destination'             => 'Bank account ••••9988',
                'deal_ref'                => 'DEL-3098',
                'deal_id'                 => 'DEL-3098',
                'note'                    => 'Handover confirmed. All docs uploaded. V5C and photos present.',
                'requested_by'            => 'JB',
                'requested_at'            => $now->copy()->subHours(4)->format('d M Y H:i'),
                'status'                  => 'Pending',
                'resolved_at'             => null,
                'owner'                   => 'AM',
                'handover_docs_complete'  => true,
                'documents'               => [
                    ['name' => 'V5C.pdf',            'present' => true],
                    ['name' => 'Condition photos',   'present' => true],
                    ['name' => 'Buyer signature',    'present' => true],
                    ['name' => 'Seller signature',   'present' => true],
                ],
                'approval_log' => [],
                'activity'     => [
                    ['description' => 'Payout request submitted', 'date' => $now->copy()->subHours(4)->format('d M Y H:i'), 'by' => 'JB'],
                ],
            ],
            [
                'id'                      => 'PAY-0039',
                'ref'                     => 'PAY-0039',
                'vendor_name'             => 'Fast Cars Ltd',
                'amount'                  => 13650,
                'destination'             => 'Bank account ••••4421',
                'deal_ref'                => 'DEL-3088',
                'deal_id'                 => 'DEL-3088',
                'note'                    => 'Vehicle collected and delivered. Seller happy.',
                'requested_by'            => 'SK',
                'requested_at'            => $now->copy()->subDays(3)->format('d M Y H:i'),
                'status'                  => 'Approved',
                'resolved_at'             => $now->copy()->subDays(2)->format('d M Y H:i'),
                'owner'                   => 'AM',
                'handover_docs_complete'  => true,
                'documents'               => [
                    ['name' => 'V5C.pdf',         'present' => true],
                    ['name' => 'MOT certificate', 'present' => true],
                    ['name' => 'Photos',          'present' => true],
                ],
                'approval_log' => [
                    ['action' => 'Approved', 'by' => 'AM', 'at' => $now->copy()->subDays(2)->format('d M Y H:i'), 'note' => 'All clear.'],
                ],
                'activity'     => [],
            ],
            [
                'id'                      => 'PAY-0038',
                'ref'                     => 'PAY-0038',
                'vendor_name'             => 'City Motors',
                'amount'                  => 8900,
                'destination'             => 'Bank account ••••7712',
                'deal_ref'                => 'DEL-3077',
                'deal_id'                 => 'DEL-3077',
                'note'                    => 'All done.',
                'requested_by'            => 'CL',
                'requested_at'            => $now->copy()->subDays(5)->format('d M Y H:i'),
                'status'                  => 'Rejected',
                'resolved_at'             => $now->copy()->subDays(4)->format('d M Y H:i'),
                'owner'                   => 'JB',
                'handover_docs_complete'  => false,
                'documents'               => [
                    ['name' => 'V5C.pdf', 'present' => false],
                    ['name' => 'Photos',  'present' => false],
                ],
                'approval_log' => [
                    ['action' => 'Rejected', 'by' => 'AM', 'at' => $now->copy()->subDays(4)->format('d M Y H:i'), 'note' => 'V5C and photos missing. Resubmit once complete.'],
                ],
                'activity'     => [],
            ],
        ];
    }
}