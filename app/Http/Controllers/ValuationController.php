<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * ValuationController
 *
 * Handles L4 — Valuations Module
 * Routes:
 *   GET  /valuations                                  → index  (standalone module view)
 *   POST /listings/{listingId}/valuations             → store  (manual add)
 *   POST /listings/{listingId}/valuations/pull        → pull   (fetch from provider)
 *   POST /listings/{listingId}/valuations/apply       → apply  (apply to guide/reserve)
 *   DELETE /listings/{listingId}/valuations/{id}      → destroy (remove row)
 */
class ValuationController extends Controller
{
    // =========================================================================
    // GET /valuations — Standalone module index (L4)
    // =========================================================================

    public function index(Request $request)
    {
        // TODO: load from DB with listing relationship
        $valuations      = $this->sampleValuations();
        $recommendedGuide = 14250;
        $reserveLow       = 13500;
        $reserveHigh      = 14000;
        $valuationStatus  = 'idle';

        return view('listings.index', compact(
            'valuations',
            'recommendedGuide',
            'reserveLow',
            'reserveHigh',
            'valuationStatus'
        ));
    }

    // =========================================================================
    // POST /listings/{listingId}/valuations — Manual add (L4)
    // =========================================================================

    public function store(Request $request, $listingId)
    {
        $request->validate([
            'source_type' => 'required|in:Internal,External',
            'amount'      => 'required|numeric|min:0',
            'provider'    => 'nullable|string|max:100',
            'valuer'      => 'nullable|string|max:150',
            'notes'       => 'nullable|string|max:1000',
            'comps'       => 'nullable|string',
        ]);

        // TODO: persist valuation record linked to $listingId
        // If apply_guide or apply_reserve checked, also update listing pricing
        // and dispatch valuation_applied event.

        $events = ['valuation_added'];

        if ($request->boolean('apply_guide') || $request->boolean('apply_reserve')) {
            $events[] = 'valuation_applied';
        }

        return response()->json([
            'success' => true,
            'message' => 'Valuation added.',
            'events'  => $events,
        ]);
    }

    // =========================================================================
    // POST /listings/{listingId}/valuations/pull — Fetch from provider (L4)
    // =========================================================================

    public function pull(Request $request, $listingId)
    {
        // TODO: resolve provider (Carsmart / HPI / CAP / Motorway / Autotrader etc.)
        //       call external API, store result, fire event.
        // On rate-limit: return 429 with retry_after.
        // On provider down / missing VRM-VIN: return 422 with reason.

        // Stub success response:
        return response()->json([
            'success'  => true,
            'message'  => 'Valuation fetched successfully.',
            'amount'   => 14350,
            'source'   => 'Carsmart',
            'delta'    => '+£100',
            'event'    => 'valuation_fetched',
        ]);

        // Stub failure examples (uncomment to test UI states):
        // return response()->json(['error' => 'Provider unavailable. Please retry.'], 422);
        // return response()->json(['error' => 'Rate limit reached. Try again in 60 seconds.', 'retry_after' => 60], 429);
        // return response()->json(['error' => 'VRM not found with provider.'], 422);
    }

    // =========================================================================
    // POST /listings/{listingId}/valuations/apply — Apply to pricing (L4)
    // =========================================================================

    public function apply(Request $request, $listingId)
    {
        $request->validate([
            'valuation_id'  => 'required|string',
            'apply_guide'   => 'boolean',
            'apply_reserve' => 'boolean',
        ]);

        // TODO: load listing from DB.
        //       Enforce: if BIN active, reserve must not exceed BIN price.
        //       Log before → after delta in audit.
        //       Dispatch valuation_applied event.

        return response()->json([
            'success'  => true,
            'message'  => 'Pricing updated.',
            'event'    => 'valuation_applied',
            'guide'    => $request->boolean('apply_guide')   ? 14350 : null,
            'reserve'  => $request->boolean('apply_reserve') ? 14350 : null,
        ]);
    }

    // =========================================================================
    // DELETE /listings/{listingId}/valuations/{valuationId} — Remove row (L4)
    // =========================================================================

    public function destroy($listingId, $valuationId)
    {
        // TODO: soft-delete valuation record from DB.
        // Audit entry: valuation removed, actor, timestamp.

        return response()->json([
            'success' => true,
            'message' => 'Valuation removed.',
        ]);
    }

    // =========================================================================
    // Sample data — replace with Eloquent models
    // =========================================================================

    private function sampleValuations(): array
    {
        return [
            [
                'id'      => 'v100',
                'date'    => '2026-05-31',
                'listing' => 'BMW 330i M Sport (LST-1023)',
                'source'  => 'Carsmart',
                'valuer'  => 'System',
                'amount'  => 14200,
                'delta'   => '+2.5%',
                'notes'   => 'Auto-pulled',
                'comps'   => 0,
                'used'    => true,
            ],
            [
                'id'      => 'v99',
                'date'    => '2026-05-28',
                'listing' => 'BMW 330i M Sport (LST-1023)',
                'source'  => 'HPI',
                'valuer'  => 'HPI Feed',
                'amount'  => 14800,
                'delta'   => '+9.8%',
                'notes'   => 'Market benchmark',
                'comps'   => 12,
                'used'    => false,
            ],
            [
                'id'      => 'v200',
                'date'    => '2026-05-30',
                'listing' => 'Audi A6 Avant (LST-1024)',
                'source'  => 'CAP',
                'valuer'  => 'CAP Expert',
                'amount'  => 11900,
                'delta'   => '+1.3%',
                'notes'   => 'Recommended',
                'comps'   => 8,
                'used'    => true,
            ],
        ];
    }
}