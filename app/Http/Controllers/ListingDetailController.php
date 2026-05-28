<?php

namespace App\Http\Controllers;

class ListingDetailController extends Controller
{
    public function show($id)
    {
        $listing = [
            'id'=>$id,
            'ref'=>"LST-10$id",
            'vehicle'=>'BMW 330i M Sport',
            'vrm'=>'AB19 CDE',
            'state'=>'Draft',
            'reserve'=>15000,
            'bin'=>16500,
            'valuation'=>14200,
        ];

        $valuations = [
            ['source'=>'Carsmart','amount'=>14000,'time'=>'2h ago'],
            ['source'=>'HPI','amount'=>14500,'time'=>'1h ago'],
            ['source'=>'Motorway','amount'=>15000,'time'=>'30m ago'],
        ];

        return view('listings.show', compact('listing','valuations'));
    }
}