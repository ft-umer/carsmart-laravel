<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'listings_today' => 12,
            'listings_delta' => 3,
            'live_auctions' => 5,
            'closing_soon' => 2,
            'deals_pending' => 7,
            'payout_requests' => 4,
        ];

        $queue = [
            'qa_listings' => 6,
            'valuations' => 3,
            'objections' => 2,
            'disputes' => 1,
        ];

        $todaysAuctions = [
            [
                'name' => 'Karachi Auto Auction',
                'number' => 'AUC-1001',
                'lots' => 120,
                'status' => 'live',
                'closes_at' => '18:00',
            ],
        ];

        $alerts = [];

        return view('dashboard', compact('kpis', 'queue', 'todaysAuctions', 'alerts'));
    }
}