<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ListingController extends Controller
{
    /**
     * L0 — Listings Index (Browse / Search)
     */
    public function index(Request $request)
    {
        $listings = $this->sampleListings();

        // Filter by search term (stub — replace with Eloquent scopes)
        if ($search = $request->get('search')) {
            $listings = array_filter($listings, fn($l) =>
                str_contains(strtolower($l['id'] . $l['vehicle'] . $l['vrm']), strtolower($search))
            );
        }

        if ($status = $request->get('status')) {
            $listings = array_filter($listings, fn($l) => $l['state'] === $status);
        }

        if ($qa = $request->get('qa')) {
            $listings = array_filter($listings, fn($l) => $l['qa'] === $qa);
        }

        $valuations = $this->sampleValuations();

        return view('listings.index', [
            'listings'   => array_values($listings),
            'valuations' => $valuations,
        ]);
    }

    /**
     * L2 — Listing Detail (Record view) — returned as HTML partial for AJAX modal
     */
    public function show($id)
    {
        $listing = collect($this->sampleListings())->firstWhere('id', $id)
            ?? $this->sampleListings()[0];

        $valuations = $this->sampleValuations();

        // Return partial HTML for modal injection
        return view('listings.partials.details', compact('listing', 'valuations'));
    }

    /**
     * L1 — Store new listing (wizard submit)
     */
    public function store(Request $request)
    {
        // TODO: validate + persist
        // Events: listing_created, media_uploaded, document_uploaded, listing_submitted_for_qa
        return response()->json([
            'success' => true,
            'message' => 'Listing created successfully.',
            'event'   => 'listing_created',
        ]);
    }

    /**
     * L8 — State transitions
     * Actions: submit_qa | approve_qa | fail_qa | publish | assign_auction | archive
     */
    public function transition(Request $request, $id)
    {
        $action = $request->input('action');

        $validTransitions = [
            'submit_qa'      => ['from' => 'Draft',          'to' => 'QA'],
            'approve_qa'     => ['from' => 'QA',             'to' => 'Ready'],
            'fail_qa'        => ['from' => 'QA',             'to' => 'Failed QA'],
            'publish'        => ['from' => 'Ready',          'to' => 'Published'],
            'assign_auction' => ['from' => 'Ready',          'to' => 'Assigned to Auction'],
            'end_auction'    => ['from' => 'Live',           'to' => 'Ended'],
            'deal_pending'   => ['from' => 'Ended',          'to' => 'Deal Pending'],
            'handover'       => ['from' => 'Deal Pending',   'to' => 'Handover'],
            'close'          => ['from' => 'Handover',       'to' => 'Closed'],
            'archive'        => ['from' => '*',              'to' => 'Archived'],
        ];

        if (!isset($validTransitions[$action])) {
            return response()->json(['error' => 'Invalid action.'], 422);
        }

        // TODO: persist state change, enforce KYC/QA gates before publish
        // Events: listing_state_changed

        return response()->json([
            'success'   => true,
            'new_state' => $validTransitions[$action]['to'],
            'event'     => 'listing_state_changed',
        ]);
    }

    /**
     * L7 — Bulk actions
     */
    public function bulk(Request $request)
    {
        $action = $request->input('action'); // assign-owner | mark-qa | enable-bin | publication-queue | create-auction | archive | pull-valuations
        $ids    = $request->input('ids', []);

        // TODO: queue jobs per action; for pull-valuations dispatch per row
        // Events: listing_bulk_updated, valuation_fetched (bulk)

        return response()->json([
            'success' => true,
            'action'  => $action,
            'count'   => count($ids),
            'event'   => 'listing_bulk_updated',
        ]);
    }

    // ---------------------------------------------------------------------------
    // Sample data (replace with Eloquent models)
    // ---------------------------------------------------------------------------

    private function sampleListings(): array
    {
        return [
            [
                'id'               => 'LST-1023',
                'vehicle'          => 'BMW 330i M Sport 2019',
                'vrm'              => 'AB19 CDE',
                'vin'              => 'WBAXX123456789',
                'mileage'          => 42000,
                'colour'           => 'Black',
                'fuel'             => 'Petrol',
                'transmission'     => 'Automatic',
                'guide'            => 14250,
                'reserve'          => 14000,
                'bin'              => false,
                'bin_price'        => 15495,
                'offer_enabled'    => true,
                'qa'               => 'Needs Review',
                'kyc_status'       => 'Pending',
                'state'            => 'Draft',
                'sale_type'        => 'CST1',
                'owner'            => 'JR',
                'user_name'        => 'John R.',
                'auction_code'     => 'AUC-1001',
                'auction_status'   => 'Scheduled',
                'valuation'        => 14200,
                'valuation_source' => 'Carsmart',
                'valuation_date'   => '2 hours ago',
                'missing_items'    => 2,
                'valuations'       => [
                    ['id' => 'v100', 'date' => '2026-05-31', 'source' => 'Carsmart', 'valuer' => 'System', 'amount' => 14200, 'delta' => '+£200', 'notes' => 'Auto-pulled', 'comps' => 0, 'used' => true],
                    ['id' => 'v99',  'date' => '2026-05-28', 'source' => 'HPI',      'valuer' => 'HPI Feed', 'amount' => 14800, 'delta' => '+£800', 'notes' => 'Market benchmark', 'comps' => 12, 'used' => false],
                ],
            ],
            [
                'id'               => 'LST-1024',
                'vehicle'          => 'Audi A6 Avant 2017',
                'vrm'              => 'KT17 ZZZ',
                'vin'              => 'WAUZZZ4G7HN123456',
                'mileage'          => 68500,
                'colour'           => 'Silver',
                'fuel'             => 'Diesel',
                'transmission'     => 'Automatic',
                'guide'            => 11750,
                'reserve'          => null,
                'bin'              => false,
                'bin_price'        => null,
                'offer_enabled'    => false,
                'qa'               => 'Pass',
                'kyc_status'       => 'Verified',
                'state'            => 'Ready',
                'sale_type'        => 'VLT2',
                'owner'            => 'AM',
                'user_name'        => 'Alice M.',
                'auction_code'     => 'AUC5',
                'auction_status'   => 'Live',
                'valuation'        => 11900,
                'valuation_source' => 'CAP',
                'valuation_date'   => '1 day ago',
                'missing_items'    => 0,
                'valuations'       => [
                    ['id' => 'v200', 'date' => '2026-05-30', 'source' => 'CAP', 'valuer' => 'CAP Expert', 'amount' => 11900, 'delta' => '+£150', 'notes' => 'Recommended', 'comps' => 8, 'used' => true],
                ],
            ],
        ];
    }

    private function sampleValuations(): array
    {
        return [
            ['date' => '2026-05-30', 'listing' => 'BMW 330i M Sport', 'source' => 'Carsmart', 'valuer' => 'System',     'amount' => 14000, 'delta' => '+2.5%', 'notes' => 'Strong market demand',    'comps' => 12, 'used' => true],
            ['date' => '2026-05-28', 'listing' => 'BMW 330i M Sport', 'source' => 'HPI',      'valuer' => 'HPI Feed',   'amount' => 15000, 'delta' => '+9.8%', 'notes' => 'Market benchmark',        'comps' => 15, 'used' => false],
            ['date' => '2026-05-25', 'listing' => 'BMW 330i M Sport', 'source' => 'CAP',      'valuer' => 'CAP Expert', 'amount' => 14250, 'delta' => '+4.3%', 'notes' => 'Recommended valuation',   'comps' => 10, 'used' => true],
        ];
    }
}
