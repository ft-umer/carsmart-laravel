@extends('layouts.app')
@section('title','Editions — Listings')

@section('content')

<div class="kt-container-fixed">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('editions.dashboard') }}" class="hover:text-foreground transition-colors">
            Editions
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">Listings</span>
    </nav>

    {{-- Header --}}
    <div class="mb-5">
        <h1 class="text-xl font-semibold text-foreground">Editions Listings</h1>
        <p class="text-sm text-muted-foreground mt-0.5">
            Manage listing quality, photography, and publication state
        </p>
    </div>

    {{-- Table --}}
    <div class="card border border-border rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-muted/40 text-muted-foreground text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Listing</th>
                        <th class="p-3 text-left">Vehicle</th>
                        <th class="p-3 text-left">Rarity</th>
                        <th class="p-3 text-left">Photography</th>
                        <th class="p-3 text-left">Provenance</th>
                        <th class="p-3 text-left">State</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @php
                        $photoColors = [
                            'Done' => 'success',
                            'Booked' => 'warning',
                            'Needed' => 'danger'
                        ];

                        $stateColors = [
                            'Published' => 'success',
                            'QA' => 'warning',
                            'Draft' => 'secondary'
                        ];

                        $provenanceColors = [
                            'Complete' => 'success',
                            'Partial' => 'warning',
                            'None' => 'danger'
                        ];
                    @endphp

                    @forelse($items as $item)
                        <tr class="hover:bg-muted/30 transition-colors">

                            <td class="p-3 font-medium text-foreground">
                                {{ $item['listing'] }}
                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $item['vehicle'] }}
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-primary kt-badge-xs">
                                    {{ $item['rarity'] }}
                                </span>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $photoColors[$item['photography']] ?? 'secondary' }} kt-badge-xs">
                                    {{ $item['photography'] }}
                                </span>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $provenanceColors[$item['provenance']] ?? 'secondary' }} kt-badge-xs">
                                    {{ $item['provenance'] }}
                                </span>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $stateColors[$item['state']] ?? 'secondary' }} kt-badge-xs">
                                    {{ $item['state'] }}
                                </span>
                            </td>

                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-1">

                                    <a href="{{ route('editions.provenance') }}"
                                       class="kt-btn kt-btn-outline kt-btn-xs">
                                        Provenance
                                    </a>

                                    <a href="{{ route('editions.photography') }}"
                                       class="kt-btn kt-btn-mono kt-btn-xs">
                                        Photography
                                    </a>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-muted-foreground">
                                No listings available
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection