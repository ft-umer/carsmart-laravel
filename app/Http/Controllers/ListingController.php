<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index()
    {
        $listings = [
            [
                'id' => 'LST-1023',
                'vehicle' => 'BMW 330i M Sport 2019',
                'vrm' => 'AB19 CDE',
                'mileage' => 42000,

                // Core pricing (Phase 1)
                'guide' => 14250,
                'reserve' => 14000,
                'bin' => true,

                // QA system (Phase 1 compliant states)
                'qa' => 'Needs Review',

                // Lifecycle state machine
                'state' => 'Draft',

                // Ownership
                'owner' => 'JR',

                // Phase 1 valuation system (expanded)
                'valuation' => 14200,
                'valuation_source' => 'Carsmart',
                'valuation_time' => now()->subMinutes(2)->toISOString(),

                // Computed UI field (important for Phase 1 index)
                'delta' => 200,

                // Full audit-ready valuation history
                'valuations' => [
                    [
                        'id' => 'v100',
                        'date' => now()->subMinutes(2)->toISOString(),
                        'source' => 'Carsmart',
                        'valuer' => 'ProviderX',
                        'amount' => 14200,
                        'notes' => 'Autopulled from API'
                    ],
                    [
                        'id' => 'v099',
                        'date' => now()->subDay()->toISOString(),
                        'source' => 'Manual',
                        'valuer' => 'JR',
                        'amount' => 14000,
                        'notes' => 'Manual adjustment'
                    ]
                ]
            ],

            [
                'id' => 'LST-1024',
                'vehicle' => 'Audi A6 Avant 2017',
                'vrm' => 'KT17 ZZZ',
                'mileage' => 68500,

                'guide' => 11750,
                'reserve' => null,
                'bin' => false,

                'qa' => 'Passed',
                'state' => 'Ready',
                'owner' => 'AM',

                // No valuation yet (Phase 1 supports empty state)
                'valuation' => null,
                'valuation_source' => null,
                'valuation_time' => null,

                'delta' => null,

                'valuations' => []
            ]
        ];

        return view('listings.index', compact('listings'));
    }
}