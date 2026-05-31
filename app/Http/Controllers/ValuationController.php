<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValuationController extends Controller
{
    /**
     * L4 — Valuations Module (standalone index)
     */
    public function index()
    {
        $valuations = $this->sampleValuations();

        $recommendedGuide = 14250;
        $reserveLow       = 13500;
        $reserveHigh      = 14000;
        $valuationStatus  = 'idle'; // idle | fetching | success | failed

        return view('listings.partials.valuation-panel', compact(
            'valuations', 'recommendedGuide', 'reserveLow', 'reserveHigh', 'valuationStatus'
        ));
    }

    /**
     * Pull latest valuation from external provider.
     * Events: valuation_fetched
     */
    public function pull($id)
    {
        // TODO: dispatch job to external provider (Carsmart, HPI, Motorway, etc.)
        // Returns JSON for AJAX; blade fallback redirects back.

        if (request()->expectsJson()) {
            return response()->json([
                'success'  => true,
                'amount'   => 14200,
                'source'   => 'Carsmart',
                'delta'    => '+£200',
                'delta_pct'=> '+1.4%',
                'timestamp'=> now()->toISOString(),
                'event'    => 'valuation_fetched',
            ]);
        }

        return back()->with('success', 'Valuation fetched successfully.');
    }

    /**
     * Store a manual valuation.
     * Events: valuation_added
     */
    public function store(Request $request, $listingId)
    {
        $request->validate([
            'source' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1',
        ]);

        // TODO: persist valuation record with listing_id, actor, before-state
        // Events: valuation_added

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'event'   => 'valuation_added',
            ]);
        }

        return back()->with('success', 'Valuation added.');
    }

    /**
     * Apply valuation to listing pricing (Guide and/or Reserve).
     * Events: valuation_applied
     */
    public function apply(Request $request, $listingId)
    {
        $applyGuide   = $request->boolean('apply_guide', true);
        $applyReserve = $request->boolean('apply_reserve', false);
        $amount       = $request->input('amount');

        // Validation: Reserve cannot exceed BIN if BIN is active
        // TODO: fetch listing, check bin_price
        // if ($applyReserve && $listing->bin_price && $amount > $listing->bin_price) {
        //     return response()->json(['error' => 'Reserve cannot exceed BIN price.'], 422);
        // }

        // TODO: persist pricing update with before→after delta audit log
        // Events: valuation_applied

        if ($request->expectsJson()) {
            return response()->json([
                'success'      => true,
                'apply_guide'  => $applyGuide,
                'apply_reserve'=> $applyReserve,
                'amount'       => $amount,
                'event'        => 'valuation_applied',
            ]);
        }

        return back()->with('success', 'Pricing updated from valuation.');
    }

    // ---------------------------------------------------------------------------

    private function sampleValuations(): array
    {
        return [
            ['date' => '2026-05-30', 'listing' => 'BMW 330i M Sport', 'source' => 'Carsmart', 'valuer' => 'System',     'amount' => 14000, 'delta' => '+2.5%', 'notes' => 'Strong market demand',  'comps' => 12, 'used' => true],
            ['date' => '2026-05-28', 'listing' => 'BMW 330i M Sport', 'source' => 'HPI',      'valuer' => 'HPI Feed',   'amount' => 15000, 'delta' => '+9.8%', 'notes' => 'Market benchmark',      'comps' => 15, 'used' => false],
            ['date' => '2026-05-25', 'listing' => 'BMW 330i M Sport', 'source' => 'CAP',      'valuer' => 'CAP Expert', 'amount' => 14250, 'delta' => '+4.3%', 'notes' => 'Recommended valuation', 'comps' => 10, 'used' => true],
        ];
    }
}
