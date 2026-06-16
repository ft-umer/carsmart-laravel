<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        $scope = $request->input('scope', 'all');

        $results = [];
        if ($query) {
            $results = [
                'listings' => [
                    ['id' => 'LST-1023', 'title' => 'BMW 330i 2019', 'vrm' => 'AB19 CDE', 'owner' => 'J. Reid',  'state' => 'Ready'],
                    ['id' => 'LST-1045', 'title' => 'Audi A4 2020',  'vrm' => 'CD20 EFG', 'owner' => 'A. Mills', 'state' => 'Draft'],
                ],
                'auctions' => [
                    ['id' => 'AUC-205', 'title' => 'October Prime Sale', 'date' => '12 Oct 10:00–16:00', 'lots' => 45],
                ],
                'people' => [
                    ['id' => 'CST-001', 'name' => 'Jane Doe', 'email' => 'jane@example.com', 'consent' => 'Email ✔'],
                ],
                'vendors' => [
                    ['id' => 'VND-010', 'name' => 'Fast Cars Ltd', 'kyb' => 'Verified'],
                ],
                'deals' => [
                    ['id' => 'DEL-3112', 'title' => 'BMW 330i', 'amount' => '£14,000', 'state' => 'Pending'],
                ],
            ];
        }

        $savedSearches = [
            ['name' => 'Listings needing QA',    'scope' => 'Listings', 'visibility' => 'Private', 'last_run' => '2025-10-12'],
            ['name' => 'Vendors KYB Pending',    'scope' => 'Vendors',  'visibility' => 'Team',    'last_run' => '2025-10-10'],
            ['name' => 'Open Disputes this week','scope' => 'Disputes', 'visibility' => 'Org',     'last_run' => '2025-10-14'],
        ];

        return view('search.index', compact('query', 'scope', 'results', 'savedSearches'));
    }

    public function auditLog(Request $request)
    {
        $entries = [
            ['time' => '2025-10-14 14:03', 'actor' => 'A. Mills', 'object' => 'Listing LST-1023', 'action' => 'Updated',       'summary' => 'Reserve £— → £12,500',         'result' => 'Success'],
            ['time' => '2025-10-14 13:50', 'actor' => 'J. Reid',  'object' => 'Auction AUC-205',  'action' => 'State changed', 'summary' => 'Draft → Published',             'result' => 'Success'],
            ['time' => '2025-10-14 12:10', 'actor' => 'System',   'object' => 'Wallet WLT-009',   'action' => 'Payout',        'summary' => '£4,200 released to vendor',      'result' => 'Success'],
            ['time' => '2025-10-14 11:45', 'actor' => 'A. Mills', 'object' => 'Person CST-001',   'action' => 'Consent update','summary' => 'Email consent Off → On',         'result' => 'Success'],
            ['time' => '2025-10-13 16:30', 'actor' => 'J. Reid',  'object' => 'Listing LST-1045', 'action' => 'Deleted',       'summary' => 'Listing removed (duplicate)',     'result' => 'Success'],
            ['time' => '2025-10-13 09:15', 'actor' => 'B. Carter','object' => 'User USR-021',     'action' => 'Sign in',       'summary' => 'Failed — invalid credentials',   'result' => 'Failure'],
        ];
        return view('search.audit', compact('entries'));
    }

    public function help()
    {
        $categories = [
            ['icon' => 'ki-element-11', 'title' => 'Getting started',          'articles' => 12],
            ['icon' => 'ki-row-horizontal','title' => 'Listings & QA',         'articles' => 18],
            ['icon' => 'ki-price-tag',   'title' => 'Auctions',                'articles' => 14],
            ['icon' => 'ki-people',      'title' => 'People & CRM',            'articles' => 9],
            ['icon' => 'ki-dollar',      'title' => 'Deals & Payments',        'articles' => 11],
            ['icon' => 'ki-truck',       'title' => 'Logistics',               'articles' => 7],
            ['icon' => 'ki-shield-cross','title' => 'Disputes',                'articles' => 6],
            ['icon' => 'ki-book-open',   'title' => 'Editions',                'articles' => 5],
            ['icon' => 'ki-chart',       'title' => 'Reports & Settings',      'articles' => 10],
        ];
        $releaseNotes = [
            ['version' => 'v1.14', 'date' => '2025-10-10', 'notes' => ['Added Editions curation queue', 'Fixed export masking on People', 'Valuation delta reports added']],
            ['version' => 'v1.13', 'date' => '2025-09-25', 'notes' => ['Live Console extensions UI', 'Sniper extension timer display']],
        ];
        return view('search.help', compact('categories', 'releaseNotes'));
    }
}