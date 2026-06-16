@extends('layouts.app')
@section('title','Editions — Curation Queue')

@section('content')

<div class="kt-container-fixed">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('editions.dashboard') }}" class="hover:text-foreground transition-colors">
            Editions
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">Curation Queue</span>
    </nav>

    {{-- Header --}}
    <div class="mb-5">
        <h1 class="text-xl font-semibold text-foreground">Curation Queue</h1>
        <p class="text-sm text-muted-foreground mt-0.5">
            Review listings and make curation decisions
        </p>
    </div>

    {{-- Table --}}
    <div class="card border border-border rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-muted/40 text-muted-foreground text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Listing</th>
                        <th class="p-3 text-left">Rarity</th>
                        <th class="p-3 text-left">Comps</th>
                        <th class="p-3 text-left">Photography</th>
                        <th class="p-3 text-left">Provenance</th>
                        <th class="p-3 text-left">Decision</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @php
                        $pc = [
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
                                {{ $item['rarity'] }}
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $item['comps'] ? 'success' : 'danger' }} kt-badge-xs">
                                    {{ $item['comps'] ? 'Done' : 'Missing' }}
                                </span>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $item['photo_booked'] ? 'success' : 'warning' }} kt-badge-xs">
                                    {{ $item['photo_booked'] ? 'Booked' : 'Needed' }}
                                </span>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $pc[$item['provenance']] ?? 'secondary' }} kt-badge-xs">
                                    {{ $item['provenance'] }}
                                </span>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $item['decision'] === 'Approved' ? 'success' : 'info' }} kt-badge-xs">
                                    {{ $item['decision'] }}
                                </span>
                            </td>

                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-1">

                                    <button class="kt-btn kt-btn-mono kt-btn-xs">
                                        Approve
                                    </button>

                                    <button class="kt-btn kt-btn-outline kt-btn-xs">
                                        Reject
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-muted-foreground">
                                No items in curation queue
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection