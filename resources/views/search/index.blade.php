@extends('layouts.app')
@section('title','Global Search')

@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="mb-5">
        <h1 class="text-xl font-semibold text-foreground">Global Search</h1>
        <p class="text-sm text-muted-foreground mt-0.5">
            Search across listings, auctions, people, vendors, and deals
        </p>
    </div>

    {{-- Search Bar --}}
    <div class="card border border-border rounded-xl mb-6">

        <div class="p-4">

            <form method="GET" action="{{ route('search.index') }}">

                <div class="flex flex-col md:flex-row gap-3">

                    {{-- Input --}}
                    <div class="relative flex-1">

                        <i data-lucide="search"
                           class="w-4 h-4 text-muted-foreground absolute left-3 top-1/2 -translate-y-1/2">
                        </i>

                        <input
                            type="text"
                            name="q"
                            value="{{ $query }}"
                            placeholder="Search listings, auctions, people, vendors, deals…"
                            class="kt-input pl-10 w-full"
                            autofocus
                        />

                    </div>

                    {{-- Scope --}}
                    <select name="scope" class="kt-input w-[180px]">

                        <option value="all" {{ $scope=='all'?'selected':'' }}>All</option>
                        <option value="listings" {{ $scope=='listings'?'selected':'' }}>Listings</option>
                        <option value="auctions" {{ $scope=='auctions'?'selected':'' }}>Auctions</option>
                        <option value="people" {{ $scope=='people'?'selected':'' }}>People</option>
                        <option value="vendors" {{ $scope=='vendors'?'selected':'' }}>Vendors</option>
                        <option value="deals" {{ $scope=='deals'?'selected':'' }}>Deals</option>

                    </select>

                    <button class="kt-btn kt-btn-mono">
                        Search
                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- RESULTS --}}
    @if($query)

        <div class="space-y-6">

            {{-- LISTINGS --}}
            @if(!empty($results['listings']) && ($scope==='all' || $scope==='listings'))

                <div class="card border border-border rounded-xl overflow-hidden">

                    <div class="p-4 border-b border-border">
                        <h2 class="text-sm font-semibold text-foreground">Listings</h2>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[900px] text-sm">

                            <thead class="bg-muted/40 text-xs text-muted-foreground uppercase">
                                <tr>
                                    <th class="p-3 text-left">ID</th>
                                    <th class="p-3 text-left">Vehicle</th>
                                    <th class="p-3 text-left">VRM</th>
                                    <th class="p-3 text-left">Owner</th>
                                    <th class="p-3 text-left">State</th>
                                    <th class="p-3 text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border">

                                @foreach($results['listings'] as $r)
                                    <tr class="hover:bg-muted/30">

                                        <td class="p-3 font-medium text-primary">
                                            {{ $r['id'] }}
                                        </td>

                                        <td class="p-3 text-foreground">
                                            {{ $r['title'] }}
                                        </td>

                                        <td class="p-3 text-muted-foreground">
                                            {{ $r['vrm'] }}
                                        </td>

                                        <td class="p-3 text-muted-foreground">
                                            {{ $r['owner'] }}
                                        </td>

                                        <td class="p-3">
                                            <span class="kt-badge kt-badge-success kt-badge-xs">
                                                {{ $r['state'] }}
                                            </span>
                                        </td>

                                        <td class="p-3 text-right">
                                            <a href="{{ route('listings.show', $r['id']) }}"
                                               class="kt-btn kt-btn-outline kt-btn-xs">
                                                Open
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

            {{-- AUCTIONS --}}
            @if(!empty($results['auctions']) && ($scope==='all' || $scope==='auctions'))

                <div class="card border border-border rounded-xl overflow-hidden">

                    <div class="p-4 border-b border-border">
                        <h2 class="text-sm font-semibold text-foreground">Auctions</h2>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[900px] text-sm">

                            <thead class="bg-muted/40 text-xs text-muted-foreground uppercase">
                                <tr>
                                    <th class="p-3 text-left">ID</th>
                                    <th class="p-3 text-left">Title</th>
                                    <th class="p-3 text-left">Date</th>
                                    <th class="p-3 text-left">Lots</th>
                                    <th class="p-3 text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border">

                                @foreach($results['auctions'] as $r)
                                    <tr class="hover:bg-muted/30">

                                        <td class="p-3 font-medium text-primary">
                                            {{ $r['id'] }}
                                        </td>

                                        <td class="p-3">{{ $r['title'] }}</td>
                                        <td class="p-3 text-muted-foreground">{{ $r['date'] }}</td>
                                        <td class="p-3 text-muted-foreground">{{ $r['lots'] }}</td>

                                        <td class="p-3 text-right">
                                            <a href="{{ route('auctions.show', ['auction' => $r['id']]) }}"
                                               class="kt-btn kt-btn-outline kt-btn-xs">
                                                Open
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

            {{-- PEOPLE --}}
            @if(!empty($results['people']) && ($scope==='all' || $scope==='people'))

                <div class="card border border-border rounded-xl overflow-hidden">

                    <div class="p-4 border-b border-border">
                        <h2 class="text-sm font-semibold text-foreground">People</h2>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[900px] text-sm">

                            <thead class="bg-muted/40 text-xs text-muted-foreground uppercase">
                                <tr>
                                    <th class="p-3 text-left">ID</th>
                                    <th class="p-3 text-left">Name</th>
                                    <th class="p-3 text-left">Email</th>
                                    <th class="p-3 text-left">Consent</th>
                                    <th class="p-3 text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border">

                                @foreach($results['people'] as $r)
                                    <tr class="hover:bg-muted/30">

                                        <td class="p-3 font-medium">{{ $r['id'] }}</td>
                                        <td class="p-3">{{ $r['name'] }}</td>
                                        <td class="p-3 text-muted-foreground">{{ $r['email'] }}</td>
                                        <td class="p-3 text-muted-foreground">{{ $r['consent'] }}</td>

                                        <td class="p-3 text-right">
                                            <a href="{{ route('customers.index') }}"
                                               class="kt-btn kt-btn-outline kt-btn-xs">
                                                Open
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

            {{-- VENDORS --}}
            @if(!empty($results['vendors']) && ($scope==='all' || $scope==='vendors'))

                <div class="card border border-border rounded-xl overflow-hidden">

                    <div class="p-4 border-b border-border">
                        <h2 class="text-sm font-semibold text-foreground">Vendors</h2>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[900px] text-sm">

                            <thead class="bg-muted/40 text-xs text-muted-foreground uppercase">
                                <tr>
                                    <th class="p-3 text-left">ID</th>
                                    <th class="p-3 text-left">Name</th>
                                    <th class="p-3 text-left">KYB</th>
                                    <th class="p-3 text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border">

                                @foreach($results['vendors'] as $r)
                                    <tr class="hover:bg-muted/30">

                                        <td class="p-3 font-medium">{{ $r['id'] }}</td>
                                        <td class="p-3">{{ $r['name'] }}</td>

                                        <td class="p-3">
                                            <span class="kt-badge kt-badge-success kt-badge-xs">
                                                {{ $r['kyb'] }}
                                            </span>
                                        </td>

                                        <td class="p-3 text-right">
                                            <a href="{{ route('vendors.index') }}"
                                               class="kt-btn kt-btn-outline kt-btn-xs">
                                                Open
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

            {{-- DEALS --}}
            @if(!empty($results['deals']) && ($scope==='all' || $scope==='deals'))

                <div class="card border border-border rounded-xl overflow-hidden">

                    <div class="p-4 border-b border-border">
                        <h2 class="text-sm font-semibold text-foreground">Deals</h2>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full min-w-[900px] text-sm">

                            <thead class="bg-muted/40 text-xs text-muted-foreground uppercase">
                                <tr>
                                    <th class="p-3 text-left">ID</th>
                                    <th class="p-3 text-left">Vehicle</th>
                                    <th class="p-3 text-left">Amount</th>
                                    <th class="p-3 text-left">State</th>
                                    <th class="p-3 text-right">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-border">

                                @foreach($results['deals'] as $r)
                                    <tr class="hover:bg-muted/30">

                                        <td class="p-3 font-medium text-primary">{{ $r['id'] }}</td>
                                        <td class="p-3">{{ $r['title'] }}</td>
                                        <td class="p-3 text-muted-foreground">{{ $r['amount'] }}</td>

                                        <td class="p-3">
                                            <span class="kt-badge kt-badge-warning kt-badge-xs">
                                                {{ $r['state'] }}
                                            </span>
                                        </td>

                                        <td class="p-3 text-right">
                                            <a href="{{ route('deals.show', $r['id']) }}"
                                               class="kt-btn kt-btn-outline kt-btn-xs">
                                                Open
                                            </a>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

        </div>

    @else

        {{-- Saved Searches --}}
        <div class="card border border-border rounded-xl overflow-hidden">

            <div class="p-4 border-b border-border flex items-center justify-between">
                <h2 class="text-sm font-semibold text-foreground">Saved Searches</h2>

                <button class="kt-btn kt-btn-outline kt-btn-xs">
                    Save current search
                </button>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full min-w-[900px] text-sm">

                    <thead class="bg-muted/40 text-xs text-muted-foreground uppercase">
                        <tr>
                            <th class="p-3 text-left">Name</th>
                            <th class="p-3 text-left">Scope</th>
                            <th class="p-3 text-left">Visibility</th>
                            <th class="p-3 text-left">Last Run</th>
                            <th class="p-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-border">

                        @foreach($savedSearches as $s)
                            <tr class="hover:bg-muted/30">

                                <td class="p-3 font-medium">{{ $s['name'] }}</td>
                                <td class="p-3 text-muted-foreground">{{ $s['scope'] }}</td>
                                <td class="p-3">
                                    <span class="kt-badge kt-badge-secondary kt-badge-xs">
                                        {{ $s['visibility'] }}
                                    </span>
                                </td>
                                <td class="p-3 text-muted-foreground">{{ $s['last_run'] }}</td>

                                <td class="p-3 text-right">
                                    <a href="{{ route('search.index', ['q' => $s['name']]) }}"
                                       class="kt-btn kt-btn-outline kt-btn-xs">
                                        Run
                                    </a>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    @endif

</div>

@endsection