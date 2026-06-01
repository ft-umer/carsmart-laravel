<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReconciliationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | P5: Reconciliation index
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        // --- Replace with real query for last run + exceptions ---
        $lastRun    = $this->mockLastRun();
        $exceptions = $this->mockExceptions();

        \Illuminate\Support\Facades\Log::info('reconciliation_viewed', [
            'user' => $request->user()?->id,
        ]);

        return view('payments.reconciliation', [
            'lastRun'    => $lastRun,
            'exceptions' => $exceptions,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Run auto-match against uploaded CSV
    |--------------------------------------------------------------------------
    */
    public function run(Request $request): RedirectResponse
    {
        $request->validate([
            'settlement_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('settlement_file');

        // --- Replace with real CSV parsing + matching logic ---
        // 1. Parse CSV rows (date, reference, amount, type, description)
        // 2. For each row, try to match against invoices/ledger entries by reference/amount
        // 3. Matched rows → mark reconciled
        // 4. Unmatched / mismatched rows → create Exception records
        // 5. Store run summary

        \Illuminate\Support\Facades\Log::info('reconciliation_run', [
            'user'     => $request->user()?->id,
            'filename' => $file->getClientOriginalName(),
        ]);

        return back()->with('success', 'Auto-match complete. Review exceptions below.');
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve exception
    |--------------------------------------------------------------------------
    */
    public function resolveException(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'resolution_note' => 'nullable|string|max:500',
        ]);

        // --- Replace with real resolution update ---

        \Illuminate\Support\Facades\Log::info('recon_exception_resolved', [
            'user'         => $request->user()?->id,
            'exception_id' => $id,
            'note'         => $validated['resolution_note'] ?? null,
        ]);

        return back()->with('success', 'Exception resolved.');
    }

    /*
    |--------------------------------------------------------------------------
    | Write off exception
    |--------------------------------------------------------------------------
    */
    public function writeOff(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // --- Replace with real write-off logic ---

        \Illuminate\Support\Facades\Log::info('recon_exception_writtenoff', [
            'user'         => $request->user()?->id,
            'exception_id' => $id,
            'reason'       => $validated['reason'],
        ]);

        return back()->with('success', 'Exception written off.');
    }

    /*
    |--------------------------------------------------------------------------
    | Export exceptions CSV
    |--------------------------------------------------------------------------
    */
    public function exportExceptions()
    {
        // --- Replace with real CSV export ---
        return back()->with('success', 'Exceptions CSV queued — check your email.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / mock data
    |--------------------------------------------------------------------------
    */
    private function mockLastRun(): ?array
    {
        return [
            'run_at'     => now()->copy()->subHours(2)->format('d M Y H:i'),
            'total'      => 42,
            'matched'    => 38,
            'exceptions' => 4,
        ];
    }

    private function mockExceptions(): array
    {
        $now = now();
        return [
            [
                'id'          => 'EXC-001',
                'item'        => 'Transaction fee',
                'ref'         => 'TXN-88812',
                'amount'      => 350,
                'expected'    => 300,
                'reason'      => 'Amount mismatch — PSP charged £50 more than invoice',
                'resolved'    => false,
                'resolved_at' => null,
            ],
            [
                'id'          => 'EXC-002',
                'item'        => 'Deposit capture',
                'ref'         => 'DEL-3099',
                'amount'      => 500,
                'expected'    => 500,
                'reason'      => 'Reference not found in ledger — possible duplicate',
                'resolved'    => false,
                'resolved_at' => null,
            ],
            [
                'id'          => 'EXC-003',
                'item'        => 'Monthly subscription',
                'ref'         => 'INV-2024-001',
                'amount'      => 1440,
                'expected'    => 1440,
                'reason'      => 'Match found but posting date differs by 2 days',
                'resolved'    => true,
                'resolved_at' => $now->copy()->subHours(1)->format('d M Y H:i'),
            ],
            [
                'id'          => 'EXC-004',
                'item'        => 'Refund',
                'ref'         => 'REF-0041',
                'amount'      => 250,
                'expected'    => 0,
                'reason'      => 'Unexpected refund — no matching dispute or credit note',
                'resolved'    => false,
                'resolved_at' => null,
            ],
        ];
    }
}