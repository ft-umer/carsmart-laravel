<?php

namespace App\Http\Controllers;

class EditionsController extends Controller
{
    public function dashboard()
    {
        $kpis = [
            ['label' => 'New Submissions',      'value' => 4,  'icon' => 'ki-inbox-in'],
            ['label' => 'In Curation',          'value' => 7,  'icon' => 'ki-eye'],
            ['label' => 'Approved',             'value' => 12, 'icon' => 'ki-check-circle'],
            ['label' => 'Awaiting Photography', 'value' => 3,  'icon' => 'ki-picture'],
            ['label' => 'Ready to Feature',     'value' => 2,  'icon' => 'ki-star'],
        ];
        $upcoming = [
            ['slot' => 'Tue 14:00', 'listing' => 'LST-1180', 'vehicle' => 'Porsche 911 GT3 2022', 'channel' => 'Web + Social'],
            ['slot' => 'Fri 10:00', 'listing' => 'LST-1195', 'vehicle' => 'Ferrari 296 GTB 2023', 'channel' => 'Web'],
        ];
        $alerts = [
            ['type' => 'warning', 'msg' => 'LST-1180 — Missing provenance document (Build sheet)'],
            ['type' => 'danger',  'msg' => 'LST-1172 — Photography shoot overdue by 3 days'],
            ['type' => 'info',    'msg' => 'LST-1195 — Pricing variance vs comps exceeds 8%'],
        ];
        return view('editions.dashboard', compact('kpis', 'upcoming', 'alerts'));
    }

    public function submissions()
    {
        $items = [
            ['id' => 'SUB-001', 'source' => 'Internal', 'vehicle' => 'Lamborghini Huracán 2021', 'flags' => ['Low mileage', 'Special spec'], 'curator' => 'J. Reid', 'status' => 'New'],
            ['id' => 'SUB-002', 'source' => 'Vendor',   'vehicle' => 'McLaren 720S 2020',        'flags' => ['Limited run'],              'curator' => 'A. Mills', 'status' => 'In review'],
            ['id' => 'SUB-003', 'source' => 'Partner',  'vehicle' => 'Aston Martin DB12 2024',   'flags' => ['Historic interest'],        'curator' => 'J. Reid', 'status' => 'Converted'],
        ];
        return view('editions.submissions', compact('items'));
    }

    public function curation()
    {
        $items = [
            ['listing' => 'LST-1180', 'rarity' => 'Limited run + Special spec', 'comps' => true,  'photo_booked' => true,  'provenance' => 'Partial', 'decision' => 'Pending'],
            ['listing' => 'LST-1195', 'rarity' => 'Low mileage',                'comps' => false, 'photo_booked' => false, 'provenance' => 'None',    'decision' => 'Pending'],
            ['listing' => 'LST-1172', 'rarity' => 'Historic interest',          'comps' => true,  'photo_booked' => true,  'provenance' => 'Complete', 'decision' => 'Approved'],
        ];
        return view('editions.curation', compact('items'));
    }

    public function listings()
    {
        $items = [
            ['listing' => 'LST-1172', 'vehicle' => 'Ferrari Testarossa 1989', 'rarity' => 'Historic', 'photography' => 'Done',      'provenance' => 'Complete', 'state' => 'Published'],
            ['listing' => 'LST-1180', 'vehicle' => 'Porsche 911 GT3 2022',    'rarity' => 'Special',  'photography' => 'Booked',    'provenance' => 'Partial',  'state' => 'QA'],
            ['listing' => 'LST-1195', 'vehicle' => 'Ferrari 296 GTB 2023',    'rarity' => 'Low miles','photography' => 'Needed',    'provenance' => 'None',     'state' => 'Draft'],
        ];
        return view('editions.listings', compact('items'));
    }

    public function photography()
    {
        $jobs = [
            ['id' => 'PHO-001', 'listing' => 'LST-1172', 'vehicle' => 'Ferrari Testarossa 1989', 'provider' => 'Studio9', 'slot' => '2025-10-15 09:00', 'status' => 'Delivered'],
            ['id' => 'PHO-002', 'listing' => 'LST-1180', 'vehicle' => 'Porsche 911 GT3 2022',    'provider' => 'MotionArt','slot' => '2025-11-02 11:00', 'status' => 'Scheduled'],
        ];
        return view('editions.photography', compact('jobs'));
    }

    public function features()
    {
        $slots = [
            ['slot' => 'Tue 14:00', 'date' => '2025-11-05', 'listing' => 'LST-1180', 'channels' => 'Web + Social', 'status' => 'Scheduled'],
            ['slot' => 'Fri 10:00', 'date' => '2025-11-08', 'listing' => 'LST-1195', 'channels' => 'Web',          'status' => 'Draft'],
        ];
        return view('editions.features', compact('slots'));
    }

    public function provenance()
    {
        $docs = [
            ['type' => 'Original purchase invoice', 'listing' => 'LST-1172', 'file' => 'invoice.pdf',    'verified_by' => 'A. Mills', 'verified_on' => '2025-10-10', 'notes' => ''],
            ['type' => 'Service history',           'listing' => 'LST-1172', 'file' => 'service.pdf',   'verified_by' => 'J. Reid',  'verified_on' => '2025-10-11', 'notes' => 'Full Dealer history'],
            ['type' => 'Build sheet',               'listing' => 'LST-1180', 'file' => null,            'verified_by' => null,       'verified_on' => null,         'notes' => 'Awaiting from vendor'],
        ];
        return view('editions.provenance', compact('docs'));
    }

    public function concierge()
    {
        $deals = [
            ['deal' => 'DEL-3001', 'vehicle' => 'Ferrari Testarossa 1989', 'transport' => true, 'storage' => false, 'detailing' => true, 'insurance' => true],
        ];
        return view('editions.concierge', compact('deals'));
    }
}