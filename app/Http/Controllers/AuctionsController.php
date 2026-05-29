<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuctionsController extends Controller
{
   public function index()
{
    $auctions = collect([

        [
            'id' => 1001,
            'title' => 'Dubai Luxury Vehicles Auction',
            'status' => 'Live',
            'visibility' => 'Public',
            'owner' => 'Trade Auto Group',
            'lots' => 48,
            'live' => 31,
            'ended' => 12,
            'reserve_met' => '78%',
            'start_date' => '2026-05-29 10:00 AM',
            'end_date' => '2026-05-29 06:00 PM',
        ],

        [
            'id' => 1002,
            'title' => 'Premium SUV Liquidation',
            'status' => 'Planned',
            'visibility' => 'Private',
            'owner' => 'Prestige Motors',
            'lots' => 26,
            'live' => 0,
            'ended' => 0,
            'reserve_met' => '0%',
            'start_date' => '2026-05-31 09:00 AM',
            'end_date' => '2026-05-31 04:00 PM',
        ],

        [
            'id' => 1003,
            'title' => 'Commercial Fleet Auction',
            'status' => 'Published',
            'visibility' => 'Public',
            'owner' => 'Metro Fleet Services',
            'lots' => 65,
            'live' => 0,
            'ended' => 0,
            'reserve_met' => '0%',
            'start_date' => '2026-06-02 11:00 AM',
            'end_date' => '2026-06-02 07:00 PM',
        ],

        [
            'id' => 1004,
            'title' => 'Salvage & Insurance Vehicles',
            'status' => 'Paused',
            'visibility' => 'Private',
            'owner' => 'National Recovery Yard',
            'lots' => 39,
            'live' => 14,
            'ended' => 8,
            'reserve_met' => '42%',
            'start_date' => '2026-05-28 08:00 AM',
            'end_date' => '2026-05-30 05:00 PM',
        ],

        [
            'id' => 1005,
            'title' => 'Classic Cars Evening Auction',
            'status' => 'Ended',
            'visibility' => 'Public',
            'owner' => 'Heritage Auto House',
            'lots' => 18,
            'live' => 18,
            'ended' => 18,
            'reserve_met' => '91%',
            'start_date' => '2026-05-20 05:00 PM',
            'end_date' => '2026-05-20 10:00 PM',
        ],

    ]);

    return view('auctions.index', compact('auctions'));
}

    public function show($auction)
    {
        $auctionData = [
            'id' => $auction,
            'title' => 'Premium Vehicle Auction',
            'status' => 'Live',
            'owner' => 'Admin',
            'total_lots' => 48,
            'active_bidders' => 14,
        ];

        return view('auctions.detail', compact('auctionData'));
    }

    public function lotDetail($auction, $lot)
    {
        $lotData = [
            'auction_id' => $auction,
            'lot_id' => $lot,
            'title' => 'Lot #' . $lot,
            'vehicle' => '2024 Mercedes-Benz S580',
            'state' => 'Live',
            'current_bid' => '$124,500',
            'reserve_status' => 'Reserve Met',
            'participants' => [
                [
                    'name' => 'Trade Auto Group',
                    'status' => 'Active',
                    'last_bid' => '$124,500',
                ],
                [
                    'name' => 'Prestige Motors',
                    'status' => 'Watching',
                    'last_bid' => '$121,000',
                ],
            ],
            'bid_feed' => [
                [
                    'time' => '12:10 PM',
                    'vendor' => 'Trade Auto Group',
                    'amount' => '$124,500',
                    'type' => 'Live Bid',
                ],
                [
                    'time' => '12:08 PM',
                    'vendor' => 'Prestige Motors',
                    'amount' => '$121,000',
                    'type' => 'Proxy Bid',
                ],
            ],
        ];


        return view('auctions.lots-detail', compact('lotData'));
    }

    public function live()
    {
        $liveAuctions = [
            [
                'id' => 1,
                'title' => 'Premium Vehicle Auction',
                'status' => 'Live',
                'lots' => 48,
            ],
            [
                'id' => 2,
                'title' => 'Luxury Car Event',
                'status' => 'Running',
                'lots' => 26,
            ],
        ];

        return view('auctions.live', compact('liveAuctions'));
    }

    public function upcoming()
    {
        $upcomingAuctions = [
            [
                'id' => 1,
                'title' => 'Classic Cars Auction',
                'date' => '2026-06-10',
                'lots' => 32,
            ],
            [
                'id' => 2,
                'title' => 'Luxury SUV Event',
                'date' => '2026-06-15',
                'lots' => 18,
            ],
        ];

        return view('auctions.upcoming', compact('upcomingAuctions'));
    }

    public function closed()
    {
        $closedAuctions = [
            [
                'id' => 1,
                'title' => 'Sports Cars Auction',
                'ended_at' => '2026-05-20',
                'sold_lots' => 28,
            ],
            [
                'id' => 2,
                'title' => 'Electric Vehicles Auction',
                'ended_at' => '2026-05-18',
                'sold_lots' => 41,
            ],
        ];

        return view('auctions.closed', compact('closedAuctions'));
    }
    
    public function bids()
{
    $bids = [
        [
            'auction' => 'Premium Vehicle Auction',
            'lot' => 'Lot #12',
            'bidder' => 'Trade Auto Group',
            'amount' => '$124,500',
            'status' => 'Winning',
        ],
        [
            'auction' => 'Luxury SUV Event',
            'lot' => 'Lot #08',
            'bidder' => 'Prestige Motors',
            'amount' => '$98,000',
            'status' => 'Outbid',
        ],
    ];

    return view('auctions.bids', compact('bids'));
}
}
