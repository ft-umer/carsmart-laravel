<?php
namespace App\Http\Controllers;

class ValuationController extends Controller
{
    public function index()
    {
        $valuations = [
            ['source'=>'Carsmart','amount'=>14000],
            ['source'=>'HPI','amount'=>15000],
        ];

        return view('valuations.index', compact('valuations'));
    }

    public function pull($id)
    {
        return back()->with('success','Valuation fetched (dummy)');
    }

    public function add($id)
    {
        return back()->with('success','Valuation added');
    }

    public function apply($id)
    {
        return back()->with('success','Applied to pricing');
    }
}