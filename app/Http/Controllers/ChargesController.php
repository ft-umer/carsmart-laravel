<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ChargesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | P1: Charges & Fees index
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $vendor   = $request->input('vendor', '');
        $period   = $request->input('period', 'this_month');
        $type     = $request->input('type', '');
        $status   = $request->input('status', '');

        // --- Replace with real Eloquent query ---
        $invoices = $this->mockInvoices();

        if ($vendor) {
            $invoices = array_filter($invoices, fn($i) => ($i['vendor_id'] ?? '') === $vendor);
        }
        if ($type) {
            $invoices = array_filter($invoices, fn($i) => ($i['type'] ?? '') === $type);
        }
        if ($status) {
            $invoices = array_filter($invoices, fn($i) => ($i['status'] ?? '') === $status);
        }

        \Illuminate\Support\Facades\Log::info('charges_index_viewed', [
            'user' => $request->user()?->id,
        ]);

        return view('payments.charges', [
            'invoices' => array_values($invoices),
            'total'    => count($invoices),
            'vendors'  => $this->vendorList(),
            'vendor'   => $vendor,
            'period'   => $period,
            'type'     => $type,
            'status'   => $status,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate invoice (POST)
    |--------------------------------------------------------------------------
    */
    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|string',
            'period'    => 'required|string',
            'type'      => 'required|in:monthly,transaction',
        ]);

        // --- Replace with real invoice generation ---
        $ref = 'INV-' . strtoupper(substr(uniqid(), -6));

        \Illuminate\Support\Facades\Log::info('invoice_generated', [
            'user'      => $request->user()?->id,
            'vendor_id' => $validated['vendor_id'],
            'ref'       => $ref,
        ]);

        return back()->with('success', "Invoice {$ref} created as draft.");
    }

    /*
    |--------------------------------------------------------------------------
    | Send invoice to vendor
    |--------------------------------------------------------------------------
    */
    public function send(Request $request, string $id): RedirectResponse
    {
        // Validate invoice is in Draft state
        // --- Replace with real send logic (email/notification) ---

        \Illuminate\Support\Facades\Log::info('invoice_sent', [
            'user'       => $request->user()?->id,
            'invoice_id' => $id,
        ]);

        return back()->with('success', 'Invoice sent to vendor.');
    }

    /*
    |--------------------------------------------------------------------------
    | Mark invoice as paid
    |--------------------------------------------------------------------------
    */
    public function markPaid(Request $request, string $id): RedirectResponse
    {
        // --- Replace with real mark-paid logic ---

        \Illuminate\Support\Facades\Log::info('invoice_paid', [
            'user'       => $request->user()?->id,
            'invoice_id' => $id,
        ]);

        return back()->with('success', 'Invoice marked as paid.');
    }

    /*
    |--------------------------------------------------------------------------
    | Issue credit note
    |--------------------------------------------------------------------------
    */
    public function creditNote(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:500',
        ]);

        // --- Replace with real credit note logic ---

        \Illuminate\Support\Facades\Log::info('credit_note_issued', [
            'user'       => $request->user()?->id,
            'invoice_id' => $id,
            'amount'     => $validated['amount'],
        ]);

        return back()->with('success', 'Credit note issued.');
    }

    /*
    |--------------------------------------------------------------------------
    | Export PDF
    |--------------------------------------------------------------------------
    */
    public function exportPdf(string $id)
    {
        // --- Replace with real PDF export (Snappy / DomPDF) ---
        return back()->with('success', 'PDF export queued — check your email.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / mock data
    |--------------------------------------------------------------------------
    */
    private function vendorList(): array
    {
        return [
            ['id' => 'VND-001', 'name' => 'Fast Cars Ltd'],
            ['id' => 'VND-002', 'name' => 'Premium Autos'],
            ['id' => 'VND-003', 'name' => 'City Motors'],
        ];
    }

    private function mockInvoices(): array
    {
        $now = now();
        return [
            [
                'id'          => 'INV-001',
                'ref'         => 'INV-2024-001',
                'vendor_id'   => 'VND-001',
                'vendor_name' => 'Fast Cars Ltd',
                'period'      => 'May 2024',
                'type'        => 'monthly',
                'subtotal'    => 1200,
                'tax'         => 240,
                'total'       => 1440,
                'status'      => 'Issued',
                'line_items'  => [
                    ['description' => 'Monthly subscription', 'reference' => 'PLAN-PRO', 'amount' => 500],
                    ['description' => 'Transaction fee — DEL-3112', 'reference' => 'DEL-3112', 'amount' => 350],
                    ['description' => 'Transaction fee — DEL-3098', 'reference' => 'DEL-3098', 'amount' => 350],
                ],
            ],
            [
                'id'          => 'INV-002',
                'ref'         => 'INV-2024-002',
                'vendor_id'   => 'VND-002',
                'vendor_name' => 'Premium Autos',
                'period'      => 'May 2024',
                'type'        => 'monthly',
                'subtotal'    => 800,
                'tax'         => 160,
                'total'       => 960,
                'status'      => 'Paid',
                'line_items'  => [
                    ['description' => 'Monthly subscription', 'reference' => 'PLAN-STD', 'amount' => 300],
                    ['description' => 'Transaction fee — DEL-3099', 'reference' => 'DEL-3099', 'amount' => 500],
                ],
            ],
            [
                'id'          => 'INV-003',
                'ref'         => 'INV-2024-003',
                'vendor_id'   => 'VND-003',
                'vendor_name' => 'City Motors',
                'period'      => 'May 2024',
                'type'        => 'transaction',
                'subtotal'    => 275,
                'tax'         => 55,
                'total'       => 330,
                'status'      => 'Draft',
                'line_items'  => [
                    ['description' => 'Transaction fee — DEL-3105', 'reference' => 'DEL-3105', 'amount' => 275],
                ],
            ],
        ];
    }
}