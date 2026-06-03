<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportsController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // R0 — Reports Overview
    // ──────────────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $range = $request->input('range', '30d');

        $valuationCoverage = 84;

        $covStats = [
            ['label' => 'With valuation',  'value' => '84%',    'delta' => '+3%',  'up' => true],
            ['label' => 'Avg age',          'value' => '6 days', 'delta' => '-1d',  'up' => true],
            ['label' => 'Failures',         'value' => '12',     'delta' => '-4',   'up' => true],
            ['label' => 'Applied rate',     'value' => '71%',    'delta' => '+2%',  'up' => true],
        ];

        $deltaStats = [
            ['label' => 'Median |Δ|',      'value' => '£850',   'delta' => '-£120', 'up' => true],
            ['label' => '90th pctile |Δ|', 'value' => '£3,200', 'delta' => '+£200', 'up' => false],
            ['label' => 'Applied rate',     'value' => '71%',    'delta' => '+2%',   'up' => true],
            ['label' => 'Not applied',      'value' => '29%',    'delta' => '-2%',   'up' => true],
        ];

        return view('reports.index', compact('range', 'valuationCoverage', 'covStats', 'deltaStats'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // R1 — Individual report
    // ──────────────────────────────────────────────────────────────────────────

    public function show(Request $request, string $report): View
    {
        $range = $request->input('range', '30d');

        [$title, $description, $metrics, $columns, $rows] = match ($report) {
            'valuation-coverage'  => $this->valuationCoverageReport(),
            'valuation-delta'     => $this->valuationDeltaReport(),
            'listings-funnel'     => $this->listingsFunnelReport(),
            'auction-performance' => $this->auctionPerformanceReport(),
            'lead-conversion'     => $this->leadConversionReport(),
            'vendor-participation'=> $this->vendorParticipationReport(),
            'revenue-fees'        => $this->revenueFeesReport(),
            'wallet-payouts'      => $this->walletPayoutsReport(),
            'logistics-sla'       => $this->logisticsSlaReport(),
            'disputes-sla'        => $this->disputesSlaReport(),
            'comms-metrics'       => $this->commsMetricsReport(),
            default               => abort(404, 'Unknown report.'),
        };

        $owners     = ['Alice Morgan', 'Ben Carter', 'Clara James', 'David Singh', 'Emma Walsh'];
        $reportSlug = $report;

        // Blade uses $reportTitle / $reportDescription (not $title/$description)
        $reportTitle       = $title;
        $reportDescription = $description;

        return view('reports.show', compact(
            'reportSlug', 'reportTitle', 'reportDescription',
            'metrics', 'columns', 'rows',
            'range', 'owners'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // R2 — Custom Report Builder
    // ──────────────────────────────────────────────────────────────────────────

    public function custom(): View
    {
        $savedReports = collect([
            ['id' => 1, 'name' => 'Weekly valuation coverage', 'shared' => true],
            ['id' => 2, 'name' => 'Monthly lead conversion',    'shared' => false],
            ['id' => 3, 'name' => 'Vendor participation Q2',    'shared' => true],
        ]);

        return view('reports.custom', compact('savedReports'));
    }

    public function run(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'columns' => ['ID', 'Title', 'Value', 'Status', 'Date'],
            'rows'    => [
                ['CS-001', 'BMW 3 Series', '£24,500', 'Active', '2025-05-01'],
                ['CS-002', 'Audi A4',      '£19,800', 'Ended',  '2025-05-03'],
                ['CS-003', 'Mercedes C200','£27,000', 'Active', '2025-05-07'],
            ],
        ]);
    }

    public function save(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['id' => rand(10, 99), 'name' => $request->input('name', 'Saved report')]);
    }

    public function scheduleEmail(Request $request): \Illuminate\Http\RedirectResponse
    {
        return back()->with('success', 'Report schedule saved.');
    }

    public function export(Request $request, string $report): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $csv = "ID,Title,Value,Status\nCS-001,BMW 3 Series,£24500,Active\nCS-002,Audi A4,£19800,Ended\n";
        return response()->streamDownload(fn () => print($csv), "{$report}.csv");
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Report builders (dummy data)
    // ──────────────────────────────────────────────────────────────────────────

    private function valuationCoverageReport(): array
    {
        $metrics = [
            ['label' => '% with valuation', 'value' => '84%'],
            ['label' => 'Avg valuation age', 'value' => '6 days'],
            ['label' => 'Total failures',    'value' => '12'],
            ['label' => 'Applied rate',      'value' => '71%'],
        ];

        $rowData = [
            ['listing' => 'CS-001 BMW 3 Series 2022', 'valuation' => '£24,500', 'source' => 'CAP HPI', 'time' => '2 days ago', 'guide' => '£23,800', 'delta_gbp' => '+£700', 'delta_pct' => '+2.9%', 'owner' => 'Alice Morgan', 'status' => 'Applied'],
            ['listing' => 'CS-002 Audi A4 2021',      'valuation' => '£19,800', 'source' => 'Cazana',  'time' => '5 days ago', 'guide' => '£20,200', 'delta_gbp' => '-£400', 'delta_pct' => '-2.0%', 'owner' => 'Ben Carter',  'status' => 'Applied'],
            ['listing' => 'CS-003 Mercedes C200 2020','valuation' => '£27,000', 'source' => 'CAP HPI', 'time' => '1 day ago',  'guide' => '£26,500', 'delta_gbp' => '+£500', 'delta_pct' => '+1.9%', 'owner' => 'Clara James', 'status' => 'Pending'],
            ['listing' => 'CS-004 VW Golf 2023',      'valuation' => '£16,400', 'source' => 'Cazana',  'time' => '3 days ago', 'guide' => '£16,000', 'delta_gbp' => '+£400', 'delta_pct' => '+2.5%', 'owner' => 'David Singh', 'status' => 'Applied'],
            ['listing' => 'CS-005 Ford Focus 2020',   'valuation' => '£11,200', 'source' => 'Manual',  'time' => '7 days ago', 'guide' => '£11,500', 'delta_gbp' => '-£300', 'delta_pct' => '-2.6%', 'owner' => 'Emma Walsh',  'status' => 'Applied'],
        ];

        $rows = $this->paginate($rowData);

        return [
            'Valuation coverage',
            'Listings and leads with at least one valuation in the period',
            $metrics,
            ['Listing', 'Latest valuation', 'Source', 'Time', 'Guide', 'Δ £', 'Δ %', 'Owner', 'Status'],
            $rows,
        ];
    }

    private function valuationDeltaReport(): array
    {
        $metrics = [
            ['label' => 'Median |Δ|',      'value' => '£850'],
            ['label' => '90th pctile |Δ|', 'value' => '£3,200'],
            ['label' => 'Applied rate',     'value' => '71%'],
            ['label' => 'Not applied',      'value' => '29%'],
        ];

        $rowData = [
            ['listing' => 'CS-001 BMW 3 Series', 'valuation' => '£24,500', 'guide' => '£23,800', 'delta_gbp' => '+£700',  'delta_pct' => '+2.9%', 'applied' => 'Yes', 'source' => 'CAP HPI', 'date' => '01 May 2025'],
            ['listing' => 'CS-002 Audi A4',      'valuation' => '£19,800', 'guide' => '£20,200', 'delta_gbp' => '-£400',  'delta_pct' => '-2.0%', 'applied' => 'Yes', 'source' => 'Cazana',  'date' => '03 May 2025'],
            ['listing' => 'CS-003 Mercedes C200','valuation' => '£27,000', 'guide' => '£26,500', 'delta_gbp' => '+£500',  'delta_pct' => '+1.9%', 'applied' => 'No',  'source' => 'CAP HPI', 'date' => '07 May 2025'],
            ['listing' => 'CS-006 Porsche Macan','valuation' => '£52,000', 'guide' => '£48,800', 'delta_gbp' => '+£3,200','delta_pct' => '+6.6%', 'applied' => 'No',  'source' => 'Manual',  'date' => '09 May 2025'],
        ];

        $rows = $this->paginate($rowData);

        return [
            'Valuation delta',
            'Distribution of |valuation − guide| across the period',
            $metrics,
            ['Listing', 'Valuation £', 'Guide £', 'Δ £', 'Δ %', 'Applied', 'Source', 'Date'],
            $rows,
        ];
    }

    private function listingsFunnelReport(): array
    {
        $stages = ['Created', 'QA', 'Ready', 'Published', 'Assigned', 'Live', 'Ended', 'Deal Pending', 'Handover', 'Closed'];
        $counts = [142, 130, 118, 105, 89, 74, 61, 45, 38, 29];

        $metrics = collect($stages)->map(fn ($s, $i) => ['label' => $s, 'value' => $counts[$i]])->all();

        return ['Listings funnel', 'Created → QA → Ready → Published → Live → Closed', $metrics, $stages, collect()];
    }

    private function auctionPerformanceReport(): array
    {
        $metrics = [
            ['label' => 'Total auctions',        'value' => '38'],
            ['label' => 'Total lots',             'value' => '214'],
            ['label' => 'Reserve met %',          'value' => '76%'],
            ['label' => 'Avg uplift vs guide',    'value' => '+4.2%'],
        ];

        $rowData = [
            ['auction' => 'AUC-2025-001', 'lots' => 12, 'bidders' => 34, 'reserve_met' => '83%', 'avg_uplift' => '+5.1%', 'sniper_ext' => 3],
            ['auction' => 'AUC-2025-002', 'lots' => 8,  'bidders' => 21, 'reserve_met' => '75%', 'avg_uplift' => '+3.8%', 'sniper_ext' => 1],
            ['auction' => 'AUC-2025-003', 'lots' => 15, 'bidders' => 47, 'reserve_met' => '80%', 'avg_uplift' => '+4.6%', 'sniper_ext' => 6],
            ['auction' => 'AUC-2025-004', 'lots' => 5,  'bidders' => 18, 'reserve_met' => '60%', 'avg_uplift' => '+1.2%', 'sniper_ext' => 0],
        ];

        return [
            'Auction performance',
            'Lots, bidders, reserve-met %, avg uplift',
            $metrics,
            ['Auction', 'Lots', 'Bidders', 'Reserve met %', 'Avg uplift', 'Sniper ext.'],
            $this->paginate($rowData),
        ];
    }

    private function leadConversionReport(): array
    {
        $metrics = [
            ['label' => 'First response avg', 'value' => '38 min'],
            ['label' => 'Qualified %',         'value' => '62%'],
            ['label' => 'Conversion %',        'value' => '28%'],
            ['label' => 'Time to convert',     'value' => '4.2 days'],
        ];

        $rowData = [
            ['lead' => 'LD-1041', 'source' => 'Website',  'owner' => 'Alice Morgan', 'first_response' => '22 min',  'qualified' => 'Yes', 'converted' => 'Yes', 'days_to_convert' => 3],
            ['lead' => 'LD-1042', 'source' => 'Referral', 'owner' => 'Ben Carter',   'first_response' => '1h 4min', 'qualified' => 'Yes', 'converted' => 'No',  'days_to_convert' => '—'],
            ['lead' => 'LD-1043', 'source' => 'Portal',   'owner' => 'Clara James',  'first_response' => '15 min',  'qualified' => 'No',  'converted' => 'No',  'days_to_convert' => '—'],
            ['lead' => 'LD-1044', 'source' => 'Website',  'owner' => 'David Singh',  'first_response' => '55 min',  'qualified' => 'Yes', 'converted' => 'Yes', 'days_to_convert' => 7],
        ];

        return [
            'Lead conversion',
            'First response time, qualified %, conversion to listing',
            $metrics,
            ['Lead', 'Source', 'Owner', 'First response', 'Qualified', 'Converted', 'Days to convert'],
            $this->paginate($rowData),
        ];
    }

    private function vendorParticipationReport(): array
    {
        $metrics = [
            ['label' => 'Invited',        'value' => '92'],
            ['label' => 'Active bidders', 'value' => '61'],
            ['label' => 'Wins',           'value' => '148'],
            ['label' => 'Avg purchase',   'value' => '£21,400'],
        ];

        $rowData = [
            ['vendor' => 'AutoHub Ltd',      'kyb_state' => 'Approved', 'card_on_file' => 'Yes', 'active_bidder' => 'Yes', 'wins' => 14, 'avg_purchase' => '£23,100'],
            ['vendor' => 'DriveMore Group',  'kyb_state' => 'Approved', 'card_on_file' => 'Yes', 'active_bidder' => 'Yes', 'wins' => 9,  'avg_purchase' => '£18,500'],
            ['vendor' => 'Premier Cars',     'kyb_state' => 'Pending',  'card_on_file' => 'No',  'active_bidder' => 'No',  'wins' => 0,  'avg_purchase' => '—'],
            ['vendor' => 'Swift Autos',      'kyb_state' => 'Approved', 'card_on_file' => 'Yes', 'active_bidder' => 'Yes', 'wins' => 21, 'avg_purchase' => '£19,800'],
        ];

        return [
            'Vendor participation',
            'Invited, accepted, active bidders, wins',
            $metrics,
            ['Vendor', 'KYB state', 'Card on file', 'Active bidder', 'Wins', 'Avg purchase'],
            $this->paginate($rowData),
        ];
    }

    private function revenueFeesReport(): array
    {
        $metrics = [
            ['label' => 'Subscription rev', 'value' => '£14,200'],
            ['label' => 'Transaction fees', 'value' => '£8,950'],
            ['label' => 'Credits issued',   'value' => '£1,200'],
            ['label' => 'Net revenue',      'value' => '£21,950'],
        ];

        $rowData = [
            ['period' => 'Jan 2025', 'subscriptions' => '£4,800', 'transaction_fees' => '£2,900', 'credits' => '£400',  'net' => '£7,300'],
            ['period' => 'Feb 2025', 'subscriptions' => '£4,600', 'transaction_fees' => '£2,750', 'credits' => '£350',  'net' => '£7,000'],
            ['period' => 'Mar 2025', 'subscriptions' => '£4,800', 'transaction_fees' => '£3,300', 'credits' => '£450',  'net' => '£7,650'],
        ];

        return [
            'Revenue & fees',
            'Subscription, transaction fees, credits, net',
            $metrics,
            ['Period', 'Subscriptions', 'Transaction fees', 'Credits', 'Net'],
            $this->paginate($rowData),
        ];
    }

    private function walletPayoutsReport(): array
    {
        $metrics = [
            ['label' => 'Total balance',      'value' => '£187,400'],
            ['label' => 'Holds',              'value' => '£12,300'],
            ['label' => 'Avg approval time',  'value' => '1.4 days'],
            ['label' => 'Exceptions',         'value' => '3'],
        ];

        $rowData = [
            ['vendor' => 'AutoHub Ltd',     'balance' => '£24,500', 'holds' => '£0',     'last_payout' => '28 Apr 2025', 'status' => 'Clear'],
            ['vendor' => 'DriveMore Group', 'balance' => '£11,200', 'holds' => '£3,500', 'last_payout' => '30 Apr 2025', 'status' => 'Hold'],
            ['vendor' => 'Swift Autos',     'balance' => '£19,800', 'holds' => '£0',     'last_payout' => '02 May 2025', 'status' => 'Clear'],
            ['vendor' => 'Apex Vehicles',   'balance' => '£8,900',  'holds' => '£8,800', 'last_payout' => '14 Apr 2025', 'status' => 'Exception'],
        ];

        return [
            'Wallet & payouts',
            'Balances, holds, payout approval times, exceptions',
            $metrics,
            ['Vendor', 'Balance', 'Holds', 'Last payout', 'Status'],
            $this->paginate($rowData),
        ];
    }

    private function logisticsSlaReport(): array
    {
        $metrics = [
            ['label' => 'Avg quote→schedule', 'value' => '3.1 hrs'],
            ['label' => 'On-time pickups',     'value' => '88%'],
            ['label' => 'On-time deliveries',  'value' => '91%'],
            ['label' => 'Issues count',        'value' => '7'],
        ];

        $rowData = [
            ['job' => 'LOG-0501', 'provider' => 'MoveCars UK', 'quote_to_schedule' => '2h 15m', 'pickup_status'  => 'On time',  'delivery_status' => 'On time'],
            ['job' => 'LOG-0502', 'provider' => 'FastHaul',    'quote_to_schedule' => '4h 05m', 'pickup_status'  => 'Delayed',  'delivery_status' => 'On time'],
            ['job' => 'LOG-0503', 'provider' => 'MoveCars UK', 'quote_to_schedule' => '1h 50m', 'pickup_status'  => 'On time',  'delivery_status' => 'Delayed'],
            ['job' => 'LOG-0504', 'provider' => 'AutoTransit', 'quote_to_schedule' => '3h 20m', 'pickup_status'  => 'On time',  'delivery_status' => 'On time'],
        ];

        return [
            'Logistics SLA',
            'Quote to schedule time, on-time pickups/deliveries',
            $metrics,
            ['Job', 'Provider', 'Quote→schedule', 'Pickup status', 'Delivery status'],
            $this->paginate($rowData),
        ];
    }

    private function disputesSlaReport(): array
    {
        $metrics = [
            ['label' => 'Ack within 24h %',    'value' => '94%'],
            ['label' => 'Decision within 5d %', 'value' => '87%'],
            ['label' => 'Upheld',               'value' => '31'],
            ['label' => 'Dismissed',            'value' => '48'],
        ];

        $rowData = [
            ['dispute' => 'DSP-2201', 'ack_hours' => '4h',   'decision_days' => '2d', 'outcome' => 'Upheld'],
            ['dispute' => 'DSP-2202', 'ack_hours' => '18h',  'decision_days' => '5d', 'outcome' => 'Dismissed'],
            ['dispute' => 'DSP-2203', 'ack_hours' => '1h',   'decision_days' => '3d', 'outcome' => 'Dismissed'],
            ['dispute' => 'DSP-2204', 'ack_hours' => '27h',  'decision_days' => '7d', 'outcome' => 'Upheld'],
        ];

        return [
            'Disputes SLA',
            'Acknowledge within 24h, decision within 5 days, outcomes',
            $metrics,
            ['Dispute', 'Ack hours', 'Decision days', 'Outcome'],
            $this->paginate($rowData),
        ];
    }

    private function commsMetricsReport(): array
    {
        $metrics = [
            ['label' => 'Total sends',          'value' => '14,820'],
            ['label' => 'Delivery rate',        'value' => '98.2%'],
            ['label' => 'Open/read rate',       'value' => '43.7%'],
            ['label' => 'Quiet-hr suppressions','value' => '312'],
        ];

        $rowData = [
            ['channel' => 'Email',     'sent' => '9,200',  'delivered' => '9,024', 'read' => '4,100', 'responded' => '820', 'suppressed' => '180'],
            ['channel' => 'SMS',       'sent' => '3,800',  'delivered' => '3,762', 'read' => '2,850', 'responded' => '340', 'suppressed' => '90'],
            ['channel' => 'WhatsApp',  'sent' => '1,820',  'delivered' => '1,798', 'read' => '1,540', 'responded' => '610', 'suppressed' => '42'],
        ];

        return [
            'Communications metrics',
            'Send volume by channel, delivery/read/response rates',
            $metrics,
            ['Channel', 'Sent', 'Delivered', 'Read', 'Responded', 'Suppressed'],
            $this->paginate($rowData),
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function paginate(array $items, int $perPage = 50): LengthAwarePaginator
    {
        $page  = request()->input('page', 1);
        $slice = array_slice($items, ($page - 1) * $perPage, $perPage);
        return new LengthAwarePaginator($slice, count($items), $perPage, $page, [
            'path' => request()->url(),
        ]);
    }
}