@extends('layouts.app')

@section('title', ($auctionData['ref'] ?? 'Auction') . ' - Auctions')

@section('content')

<div class="kt-container-fixed py-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs text-muted-foreground mb-5">
        <a href="{{ route('auctions.index') }}" class="hover:text-foreground">
            Auctions
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">
            {{ $auctionData['ref'] }}
        </span>
    </div>

    {{-- Header --}}
    <div class="card p-6 mb-5">

        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">

            <div>

                <h1 class="text-2xl font-semibold">
                    {{ $auctionData['title'] }}
                </h1>

                <div class="flex flex-wrap gap-2 mt-3">

                    <span class="kt-badge kt-badge-success">
                        {{ $auctionData['status'] }}
                    </span>

                    <span class="kt-badge kt-badge-primary">
                        {{ $auctionData['visibility'] }}
                    </span>

                    <span class="kt-badge kt-badge-outline">
                        {{ $auctionData['ref'] }}
                    </span>

                </div>

            </div>

            <div class="flex flex-wrap gap-2">

                <button class="kt-btn kt-btn-outline">
                    Edit Auction
                </button>

                <button class="kt-btn kt-btn-mono">
                    Open Console
                </button>

            </div>

        </div>

    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-2 xl:grid-cols-5 gap-4 mb-5">

        <div class="card p-4">
            <div class="text-xs text-muted-foreground">Lots</div>
            <div class="text-2xl font-semibold">
                {{ $auctionData['total_lots'] }}
            </div>
        </div>

        <div class="card p-4">
            <div class="text-xs text-muted-foreground">Active Bidders</div>
            <div class="text-2xl font-semibold">
                {{ $auctionData['active_bidders'] }}
            </div>
        </div>

        <div class="card p-4">
            <div class="text-xs text-muted-foreground">Reserve Met</div>
            <div class="text-2xl font-semibold">
                {{ $auctionData['reserve_met'] }}
            </div>
        </div>

        <div class="card p-4">
            <div class="text-xs text-muted-foreground">Start Date</div>
            <div>{{ $auctionData['start_date'] }}</div>
        </div>

        <div class="card p-4">
            <div class="text-xs text-muted-foreground">End Date</div>
            <div>{{ $auctionData['end_date'] }}</div>
        </div>

    </div>

    {{-- Tabs --}}
    <div class="card overflow-hidden">

        <div class="border-b border-border flex flex-wrap gap-2 p-3">

            <button class="auction-tab kt-btn kt-btn-mono kt-btn-sm"
                    data-tab="overview">
                Overview
            </button>

            <button class="auction-tab kt-btn kt-btn-ghost kt-btn-sm"
                    data-tab="lots">
                Lots
            </button>

            <button class="auction-tab kt-btn kt-btn-ghost kt-btn-sm"
                    data-tab="participants">
                Participants
            </button>

            <button class="auction-tab kt-btn kt-btn-ghost kt-btn-sm"
                    data-tab="bid-feed">
                Bid Feed
            </button>

            <button class="auction-tab kt-btn kt-btn-ghost kt-btn-sm"
                    data-tab="activity">
                Activity
            </button>

        </div>

        {{-- OVERVIEW --}}
        <div id="tab-overview" class="auction-tab-content p-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="card p-5">

                    <h3 class="font-semibold mb-4">
                        Auction Details
                    </h3>

                    <div class="space-y-3 text-sm">

                        <div class="flex justify-between">
                            <span>Reference</span>
                            <strong>{{ $auctionData['ref'] }}</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>Owner</span>
                            <strong>{{ $auctionData['owner'] }}</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>Status</span>
                            <strong>{{ $auctionData['status'] }}</strong>
                        </div>

                    </div>

                </div>

                <div class="card p-5">

                    <h3 class="font-semibold mb-4">
                        Performance
                    </h3>

                    <div class="space-y-3">

                        <div class="flex justify-between">
                            <span>Lots</span>
                            <strong>{{ $auctionData['total_lots'] }}</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>Bidders</span>
                            <strong>{{ $auctionData['active_bidders'] }}</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>Reserve Met</span>
                            <strong>{{ $auctionData['reserve_met'] }}</strong>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- LOTS --}}
        <div id="tab-lots" class="auction-tab-content hidden p-6">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead>
                        <tr>
                            <th class="p-3 text-left">Lot</th>
                            <th class="p-3 text-left">Vehicle</th>
                            <th class="p-3 text-left">Current Bid</th>
                            <th class="p-3 text-left">State</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($auctionData['lots'] as $lot)

                        <tr class="border-t border-border">
                            <td class="p-3">#{{ $lot['id'] }}</td>
                            <td class="p-3">{{ $lot['vehicle'] }}</td>
                            <td class="p-3">{{ $lot['current_bid'] }}</td>
                            <td class="p-3">{{ $lot['state'] }}</td>
                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

        {{-- PARTICIPANTS --}}
        <div id="tab-participants" class="auction-tab-content hidden p-6">

            <div class="space-y-3">

                @foreach($auctionData['participants'] as $participant)

                    <div class="card p-4">

                        <div class="flex justify-between">

                            <div>
                                <div class="font-medium">
                                    {{ $participant['name'] }}
                                </div>

                                <div class="text-xs text-muted-foreground">
                                    {{ $participant['status'] }}
                                </div>
                            </div>

                            <strong>
                                {{ $participant['bids'] }} bids
                            </strong>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

        {{-- BID FEED --}}
        <div id="tab-bid-feed" class="auction-tab-content hidden p-6">

            <div class="space-y-3">

                @foreach($auctionData['bid_feed'] as $bid)

                    <div class="card p-4">

                        <div class="flex justify-between">

                            <div>
                                {{ $bid['participant'] }}
                            </div>

                            <strong>
                                {{ $bid['amount'] }}
                            </strong>

                        </div>

                        <div class="text-xs text-muted-foreground mt-1">
                            {{ $bid['time'] }}
                        </div>

                    </div>

                @endforeach

            </div>

        </div>

        {{-- ACTIVITY --}}
        <div id="tab-activity" class="auction-tab-content hidden p-6">

            <div class="space-y-4">

                @foreach($auctionData['activity'] as $item)

                    <div class="flex gap-3">

                        <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>

                        <div>

                            <div class="font-medium">
                                {{ $item['description'] }}
                            </div>

                            <div class="text-xs text-muted-foreground">
                                {{ $item['date'] }}
                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const tabs = document.querySelectorAll('.auction-tab');

    tabs.forEach(tab => {

        tab.addEventListener('click', () => {

            document.querySelectorAll('.auction-tab-content')
                .forEach(c => c.classList.add('hidden'));

            document.getElementById(
                'tab-' + tab.dataset.tab
            ).classList.remove('hidden');

            tabs.forEach(t => {
                t.classList.remove('kt-btn-mono');
                t.classList.add('kt-btn-ghost');
            });

            tab.classList.remove('kt-btn-ghost');
            tab.classList.add('kt-btn-mono');

        });

    });

});
</script>

@endsection