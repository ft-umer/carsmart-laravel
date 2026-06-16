@extends('layouts.app')
@section('title','Editions — Concierge')

@section('content')

<div class="kt-container-fixed">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">Concierge Services</span>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Concierge Services</h1>
            <p class="text-sm text-muted-foreground mt-0.5">
                Manage value-added services for each deal
            </p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border border-border rounded-xl overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-muted/40 text-muted-foreground text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Deal</th>
                        <th class="p-3 text-left">Vehicle</th>
                        <th class="p-3 text-left">Transport</th>
                        <th class="p-3 text-left">Storage</th>
                        <th class="p-3 text-left">Detailing</th>
                        <th class="p-3 text-left">Insurance</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @forelse($deals as $d)
                        <tr class="hover:bg-muted/30 transition-colors">

                            <td class="p-3 font-medium text-foreground">
                                {{ $d['deal'] }}
                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $d['vehicle'] }}
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $d['transport'] ? 'success' : 'secondary' }} kt-badge-xs">
                                    {{ $d['transport'] ? 'Yes' : 'No' }}
                                </span>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $d['storage'] ? 'success' : 'secondary' }} kt-badge-xs">
                                    {{ $d['storage'] ? 'Yes' : 'No' }}
                                </span>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $d['detailing'] ? 'success' : 'secondary' }} kt-badge-xs">
                                    {{ $d['detailing'] ? 'Yes' : 'No' }}
                                </span>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $d['insurance'] ? 'success' : 'secondary' }} kt-badge-xs">
                                    {{ $d['insurance'] ? 'Yes' : 'No' }}
                                </span>
                            </td>

                            <td class="p-3 text-right">
                                <button class="kt-btn kt-btn-outline kt-btn-xs">
                                    Manage
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-muted-foreground">
                                No concierge deals available
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>

</div>

@endsection