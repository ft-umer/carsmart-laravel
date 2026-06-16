{{-- resources/views/editions/dashboard.blade.php --}}
@extends('layouts.app')
@section('title','Editions — Dashboard')

@section('content')

<div class="kt-container-fixed">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">Editions Dashboard</span>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Editions Dashboard</h1>
            <p class="text-sm text-muted-foreground mt-0.5">
                Overview of submissions, alerts, and upcoming features
            </p>
        </div>

        <a href="{{ route('editions.submissions') }}" class="kt-btn kt-btn-mono">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
            New Submission
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach($kpis as $kpi)
            <div class="card border border-border rounded-xl p-4">
                <div class="text-2xl font-bold text-foreground">
                    {{ $kpi['value'] }}
                </div>
                <div class="text-sm text-muted-foreground mt-1">
                    {{ $kpi['label'] }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        {{-- Alerts --}}
        <div class="lg:col-span-5">
            <div class="card border border-border rounded-xl overflow-hidden">
                
                <div class="p-4 border-b border-border">
                    <h3 class="font-semibold text-foreground">
                        Alerts & Actions Needed
                    </h3>
                </div>

                <div class="p-4 space-y-3">
                    @forelse($alerts as $alert)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-muted/30 border border-border">
                            <i data-lucide="info" class="w-4 h-4 mt-0.5 text-primary"></i>
                            <div class="text-sm text-foreground">
                                {{ $alert['msg'] }}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground">
                            No alerts at the moment.
                        </p>
                    @endforelse
                </div>

            </div>
        </div>

        {{-- Upcoming Features --}}
        <div class="lg:col-span-7">
            <div class="card border border-border rounded-xl overflow-hidden">

                <div class="flex items-center justify-between p-4 border-b border-border">
                    <h3 class="font-semibold text-foreground">
                        Upcoming Features Schedule
                    </h3>

                    <a href="{{ route('editions.features') }}" class="kt-btn kt-btn-outline kt-btn-sm">
                        View all
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px] text-sm">
                        <thead class="bg-muted/40 text-muted-foreground text-xs uppercase">
                            <tr>
                                <th class="p-3 text-left">Slot</th>
                                <th class="p-3 text-left">Listing</th>
                                <th class="p-3 text-left">Vehicle</th>
                                <th class="p-3 text-left">Channel</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-border">
                            @forelse($upcoming as $f)
                                <tr class="hover:bg-muted/30 transition-colors">
                                    <td class="p-3">
                                        <span class="kt-badge kt-badge-outline kt-badge-xs">
                                            {{ $f['slot'] }}
                                        </span>
                                    </td>
                                    <td class="p-3 font-medium text-foreground">
                                        {{ $f['listing'] }}
                                    </td>
                                    <td class="p-3 text-muted-foreground">
                                        {{ $f['vehicle'] }}
                                    </td>
                                    <td class="p-3 text-muted-foreground">
                                        {{ $f['channel'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-muted-foreground">
                                        No upcoming features scheduled
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

</div>

@endsection