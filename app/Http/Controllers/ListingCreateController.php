<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ListingCreateController extends Controller
{
    public function create()
    {
        return view('listings.create');
    }

    public function store(Request $request)
    {
        return back()->with('success','Listing created (dummy)');
    }
}