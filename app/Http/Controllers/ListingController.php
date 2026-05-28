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
                'guide' => 14250,
                'reserve' => 14000,
                'qa' => 'Needs',
                'state' => 'Draft',
                'owner' => 'JR',
                'valuations' => [
                    [
                        'id' => 'v100',
                        'date' => now()->toISOString(),
                        'source' => 'Website',
                        'valuer' => 'ProviderX',
                        'amount' => 14200,
                        'notes' => 'Autopulled'
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
                'qa' => 'Pass',
                'state' => 'Ready',
                'owner' => 'AM',
                'valuations' => []
            ]
        ];

        return view('listings.index', compact('listings'));
    }
}