{{-- C1. Admin → Dashboard → Overview
     Purpose: Operational at-a-glance. Key indicators, today's auctions, alerts, personal work queue.
     Who: Admin and other roles with read access (cards vary by role).
--}}
@extends('layouts.app')

@section('title', 'Dashboard — Overview')

@section('content')
    <div class="kt-container-fixed py-6 flex flex-col gap-6">

        {{-- Page title --}}
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold text-mono">Overview</h1>
                <p class="text-sm text-muted-foreground mt-0.5">{{ now()->format('l, j F Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('auctions.live') }}" class="kt-btn kt-btn-outline kt-btn-sm">
                    <i class="ki-filled ki-pulse text-sm me-1.5"></i>
                    Live auctions
                </a>
                <a href="#" class="kt-btn kt-btn-primary kt-btn-sm">
                    <i class="ki-filled ki-plus text-sm me-1.5"></i>
                    Create listing
                </a>
            </div>
        </div>

        {{-- ── KPIs (C1: counts with links) ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Listings today --}}
            <a href="{{ route('listings.index') }}" class="kt-card hover:border-primary/40 transition-colors">
                <div class="kt-card-content p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground font-medium">Listings today</span>
                        <span class="flex items-center justify-center size-9 rounded-lg bg-primary/10">
                            <i class="ki-filled ki-row-horizontal text-primary text-base"></i>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-mono">{{ $kpis['listings_today'] ?? 0 }}</div>
                    <div class="text-xs text-muted-foreground">
                        <span class="text-success font-medium">+{{ $kpis['listings_delta'] ?? 0 }}</span> from yesterday
                    </div>
                </div>
            </a>

            {{-- Live auctions --}}
            <a href="{{ route('auctions.live') }}" class="kt-card hover:border-primary/40 transition-colors">
                <div class="kt-card-content p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground font-medium">Live auctions</span>
                        <span class="flex items-center justify-center size-9 rounded-lg bg-danger/10">
                            <i class="ki-filled ki-pulse text-danger text-base"></i>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-mono">{{ $kpis['live_auctions'] ?? 0 }}</div>
                    <div class="text-xs text-muted-foreground">
                        <span class="text-warning font-medium">{{ $kpis['closing_soon'] ?? 0 }}</span> closing soon
                    </div>
                </div>
            </a>

            {{-- Deals pending --}}
            <a href="{{ route('payments.payouts') }}" class="kt-card hover:border-primary/40 transition-colors">
                <div class="kt-card-content p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground font-medium">Deals pending</span>
                        <span class="flex items-center justify-center size-9 rounded-lg bg-warning/10">
                            <i class="ki-filled ki-time text-warning text-base"></i>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-mono">{{ $kpis['deals_pending'] ?? 0 }}</div>
                    <div class="text-xs text-muted-foreground">Awaiting completion</div>
                </div>
            </a>

            {{-- Payout requests --}}
            <a href="{{ route('payments.payouts') }}" class="kt-card hover:border-primary/40 transition-colors">
                <div class="kt-card-content p-5 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted-foreground font-medium">Payout requests</span>
                        <span class="flex items-center justify-center size-9 rounded-lg bg-success/10">
                            <i class="ki-filled ki-dollar text-success text-base"></i>
                        </span>
                    </div>
                    <div class="text-2xl font-bold text-mono">{{ $kpis['payout_requests'] ?? 0 }}</div>
                    <div class="text-xs text-muted-foreground">Pending approval</div>
                </div>
            </a>

        </div>{{-- end KPIs --}}

        <div class="grid lg:grid-cols-3 gap-4">

            {{-- ── Work queue (my items) ── --}}
            <div class="lg:col-span-2 kt-card flex flex-col">
                <div class="kt-card-header flex items-center justify-between gap-3 border-b border-border px-5 py-4">
                    <h3 class="kt-card-title text-base font-semibold text-mono">Work queue</h3>
                    <div class="flex items-center gap-2">
                        {{-- Filterable by object type --}}
                        <div class="kt-menu" data-kt-menu="true">
                            <div class="kt-menu-item" data-kt-menu-item-offset="0,10px"
                                data-kt-menu-item-placement="bottom-end" data-kt-menu-item-toggle="dropdown"
                                data-kt-menu-item-trigger="click|lg:hover">
                                <button
                                    class="kt-menu-toggle kt-btn kt-btn-ghost kt-btn-sm flex items-center gap-1.5 text-sm">
                                    All types
                                    <i class="ki-filled ki-down text-xs"></i>
                                </button>
                                <div class="kt-menu-dropdown kt-menu-default w-[180px]" data-kt-menu-dismiss="true">
                                    @foreach (['All types', 'Listings', 'Valuations', 'Objections', 'Disputes'] as $type)
                                        <div class="kt-menu-item">
                                            <a class="kt-menu-link" href="#">
                                                <span class="kt-menu-title">{{ $type }}</span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <button class="kt-btn kt-btn-ghost kt-btn-sm kt-btn-icon" title="Reassign">
                            <i class="ki-filled ki-arrow-right-left text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="kt-card-content p-0">
                    @php
                        $workQueue = [
                            [
                                'type' => 'Listing',
                                'label' => 'Listings needing quality assurance',
                                'count' => $queue['qa_listings'] ?? 0,
                                'route' => 'listings.index',
                                'icon' => 'ki-row-horizontal',
                                'color' => 'primary',
                            ],
                            [
                                'type' => 'Valuation',
                                'label' => 'Valuations waiting',
                                'count' => $queue['valuations'] ?? 0,
                                'route' => 'listings.index',
                                'icon' => 'ki-dollar',
                                'color' => 'warning',
                            ],
                            [
                                'type' => 'Objection',
                                'label' => 'Seller objections',
                                'count' => $queue['objections'] ?? 0,
                                'route' => 'disputes.index',
                                'icon' => 'ki-message-edit',
                                'color' => 'danger',
                            ],
                            [
                                'type' => 'Dispute',
                                'label' => 'Disputes',
                                'count' => $queue['disputes'] ?? 0,
                                'route' => 'disputes.index',
                                'icon' => 'ki-shield-cross',
                                'color' => 'danger',
                            ],
                        ];
                    @endphp

                    @forelse ($workQueue as $item)
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center justify-between gap-3 px-5 py-4 border-b border-border last:border-0 hover:bg-accent/40 transition-colors group">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex items-center justify-center size-8 rounded-lg bg-{{ $item['color'] }}/10 shrink-0">
                                    <i class="ki-filled {{ $item['icon'] }} text-{{ $item['color'] }} text-sm"></i>
                                </span>
                                <div>
                                    <div class="text-sm font-medium text-mono">{{ $item['label'] }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $item['type'] }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span
                                    class="kt-badge kt-badge-{{ $item['color'] }} kt-badge-outline text-xs font-semibold px-2.5 py-1">
                                    {{ $item['count'] }}
                                </span>
                                <i
                                    class="ki-filled ki-right text-muted-foreground text-xs group-hover:text-primary transition-colors"></i>
                            </div>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 px-5 text-center">
                            <i class="ki-filled ki-check-circle text-4xl text-success mb-3"></i>
                            <p class="text-sm text-muted-foreground">Nothing urgent right now. You can open Listings or
                                Auctions from the menu.</p>
                        </div>
                    @endforelse

                </div>
            </div>{{-- end work queue --}}

            {{-- ── Alerts (system) ── --}}
            <div class="kt-card flex flex-col">
                <div class="kt-card-header flex items-center justify-between gap-3 border-b border-border px-5 py-4">
                    <h3 class="kt-card-title text-base font-semibold text-mono">System alerts</h3>
                    <span class="kt-badge kt-badge-danger text-xs">{{ count($alerts ?? []) }}</span>
                </div>
                <div class="kt-card-content p-0 grow overflow-y-auto">

                    @php
                        $alerts = $alerts ?? [
                            [
                                'type' => 'identity',
                                'label' => 'Failed identity verification',
                                'count' => 2,
                                'icon' => 'ki-user-tick',
                                'route' => 'customers.index',
                            ],
                            [
                                'type' => 'business',
                                'label' => 'Failed business verification',
                                'count' => 1,
                                'icon' => 'ki-shop',
                                'route' => 'vendors.index',
                            ],
                            [
                                'type' => 'reserve',
                                'label' => 'Reserve price not set',
                                'count' => 4,
                                'icon' => 'ki-dollar',
                                'route' => 'auctions.index',
                            ],
                            [
                                'type' => 'photos',
                                'label' => 'Missing photos',
                                'count' => 7,
                                'icon' => 'ki-picture',
                                'route' => 'listings.index',
                            ],
                        ];
                    @endphp

                    @forelse ($alerts as $alert)
                        <a href="{{ route($alert['route']) }}"
                            class="flex items-center gap-3 px-5 py-3.5 border-b border-border last:border-0 hover:bg-accent/40 transition-colors group">
                            <span class="flex items-center justify-center size-7 rounded-lg bg-danger/10 shrink-0">
                                <i class="ki-filled {{ $alert['icon'] }} text-danger text-xs"></i>
                            </span>
                            <span class="text-sm text-secondary-foreground grow">{{ $alert['label'] }}</span>
                            <span class="kt-badge kt-badge-danger text-xs font-bold">{{ $alert['count'] }}</span>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 px-5 text-center">
                            <i class="ki-filled ki-check-circle text-3xl text-success mb-2"></i>
                            <p class="text-xs text-muted-foreground">No system alerts.</p>
                        </div>
                    @endforelse

                </div>
            </div>{{-- end alerts --}}

        </div>{{-- end grid --}}

        {{-- ── Today's auctions (C1) ── --}}
        <div class="kt-card">
            <div class="kt-card-header flex items-center justify-between gap-3 border-b border-border px-5 py-4">
                <h3 class="kt-card-title text-base font-semibold text-mono">Today's auctions</h3>
                <div class="flex items-center gap-2">
                    <a href="{{ route('auctions.live') }}" class="kt-btn kt-btn-primary kt-btn-sm">
                        <i class="ki-filled ki-pulse text-sm me-1.5"></i>
                        Open live auctions
                    </a>
                    <a href="{{ route('auctions.index') }}" class="kt-btn kt-btn-ghost kt-btn-sm text-sm">
                        Auction calendar
                        <i class="ki-filled ki-right text-xs ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="kt-card-content p-0">

                @php
                    $todaysAuctions = $todaysAuctions ?? [];
                @endphp

                @if (count($todaysAuctions) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-border bg-muted/40">
                                    <th
                                        class="text-start px-5 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                                        Auction</th>
                                    <th
                                        class="text-start px-5 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                                        Lots</th>
                                    <th
                                        class="text-start px-5 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                                        Status</th>
                                    <th
                                        class="text-start px-5 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                                        Closes</th>
                                    <th
                                        class="text-end px-5 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($todaysAuctions as $auction)
                                    <tr class="border-b border-border last:border-0 hover:bg-accent/30 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <div class="font-medium text-mono">{{ $auction['name'] }}</div>
                                            <div class="text-xs text-muted-foreground">{{ $auction['number'] }}</div>
                                        </td>
                                        <td class="px-5 py-3.5 text-secondary-foreground">{{ $auction['lots'] }}</td>
                                        <td class="px-5 py-3.5">
                                            @if ($auction['status'] === 'live')
                                                <span class="kt-badge kt-badge-success text-xs font-medium">
                                                    <span
                                                        class="size-1.5 rounded-full bg-success me-1.5 animate-pulse inline-block"></span>
                                                    Live
                                                </span>
                                            @elseif ($auction['status'] === 'closing_soon')
                                                <span class="kt-badge kt-badge-warning text-xs font-medium">Closing
                                                    soon</span>
                                            @else
                                                <span class="kt-badge kt-badge-secondary text-xs font-medium">Ended</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-secondary-foreground">{{ $auction['closes_at'] }}</td>
                                        <td class="px-5 py-3.5 text-end">
                                            <a href="#" class="kt-btn kt-btn-ghost kt-btn-sm kt-btn-icon"
                                                title="Open bid feed">
                                                <i class="ki-filled ki-right text-sm"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 px-5 text-center">
                        <i class="ki-filled ki-calendar text-4xl text-muted-foreground/40 mb-3"></i>
                        <p class="text-sm font-medium text-mono mb-1">No auctions today</p>
                        <p class="text-xs text-muted-foreground">Nothing urgent right now. You can open Listings or
                            Auctions from the menu.</p>
                    </div>
                @endif

            </div>
        </div>{{-- end today's auctions --}}

    </div>
@endsection
