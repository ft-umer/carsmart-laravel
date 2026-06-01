<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class LogisticsQuotesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | L1: Quotes index
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        // --- Replace with real quote history query ---
        $quoteHistory = collect($this->mockQuoteHistory());

        \Illuminate\Support\Facades\Log::info('logistics_quotes_viewed', [
            'user' => $request->user()?->id,
        ]);

        return view('logistics.quotes', [
            'quoteHistory' => $quoteHistory,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get quotes (AJAX POST)
    | Called by the JS "Get quotes" button via fetch or form POST
    |--------------------------------------------------------------------------
    */
    public function getQuotes(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pickup'       => 'required|string|max:10',
            'drop'         => 'required|string|max:10',
            'date'         => 'nullable|date|after_or_equal:today',
            'window'       => 'nullable|in:AM,PM,Any',
            'vehicle_size' => 'nullable|in:small,medium,large,oversized',
            'deal_ref'     => 'nullable|string',
            'notes'        => 'nullable|string|max:500',
        ]);

        \Illuminate\Support\Facades\Log::info('logistics_quote_requested', [
            'user'   => $request->user()?->id,
            'pickup' => $validated['pickup'],
            'drop'   => $validated['drop'],
        ]);

        // --- Replace with real provider API calls or internal rate card ---
        // If no integration: mark simulated = true, use internal rate card.
        $quotes = $this->simulateQuotes($validated);

        return response()->json([
            'quotes'    => $quotes,
            'simulated' => !$this->hasProviderIntegration(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Select a quote (creates a pending job)
    |--------------------------------------------------------------------------
    */
    public function selectQuote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider'     => 'required|string',
            'quote'        => 'required|numeric',
            'deal_ref'     => 'nullable|string',
            'pickup'       => 'required|string',
            'drop'         => 'required|string',
            'date'         => 'nullable|date',
            'window'       => 'nullable|string',
            'vehicle_size' => 'nullable|string',
        ]);

        // --- Replace with real job creation ---
        $jobRef = 'JOB-' . strtoupper(substr(uniqid(), -5));

        \Illuminate\Support\Facades\Log::info('logistics_quote_selected', [
            'user'     => $request->user()?->id,
            'provider' => $validated['provider'],
            'quote'    => $validated['quote'],
            'job_ref'  => $jobRef,
        ]);

        return response()->json([
            'success' => true,
            'job_ref' => $jobRef,
            'message' => "Quote selected. Job {$jobRef} created.",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    private function hasProviderIntegration(): bool
    {
        return config('carsmart.transport_provider_enabled', false);
    }

    private function simulateQuotes(array $params): array
    {
        // Rough distance-based estimate (stub)
        $base = rand(60, 120);
        return [
            ['name' => 'AutoTransport Pro',    'sla' => '1–2 business days', 'earliest' => 'Tomorrow AM', 'quote' => $base + 20, 'rating' => 4.8, 'simulated' => false],
            ['name' => 'SwiftCar Logistics',   'sla' => '2–3 business days', 'earliest' => 'Thu PM',       'quote' => $base + 5,  'rating' => 4.5, 'simulated' => false],
            ['name' => 'National Vehicle Move', 'sla' => '3–5 business days', 'earliest' => 'Fri Any',     'quote' => $base,      'rating' => 4.2, 'simulated' => false],
            ['name' => 'Internal rate card',   'sla' => 'Estimate only',     'earliest' => 'Flexible',     'quote' => $base + 10, 'rating' => null,'simulated' => true],
        ];
    }

    private function mockQuoteHistory(): array
    {
        $now = now();
        return [
            [
                'id'                => 'QRQ-001',
                'requested_at'      => $now->copy()->subDays(2)->format('d M Y H:i'),
                'pickup'            => 'SW1A 1AA',
                'drop'              => 'M1 1AE',
                'deal_ref'          => 'DEL-3098',
                'window'            => 'AM',
                'selected_provider' => 'AutoTransport Pro',
                'quote'             => 89,
            ],
            [
                'id'                => 'QRQ-002',
                'requested_at'      => $now->copy()->subDays(5)->format('d M Y H:i'),
                'pickup'            => 'B1 1BB',
                'drop'              => 'E1 6RF',
                'deal_ref'          => 'DEL-3077',
                'window'            => 'PM',
                'selected_provider' => null,
                'quote'             => null,
            ],
        ];
    }
}