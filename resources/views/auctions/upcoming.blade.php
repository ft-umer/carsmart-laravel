@extends('layouts.app')

@section('title', 'Upcoming Auctions')

@section('content')

<section class="kt-container-fixed px-4 lg:px-6 py-6">
<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
    <a href="{{ route('auctions.index') }}" class="hover:text-foreground">
        Auctions
    </a>

    <i data-lucide="chevron-right" class="w-3 h-3"></i>

    <span class="text-foreground font-medium">
        Upcoming Auctions
    </span>
</nav>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">

        <div>
            <h1 class="text-2xl font-semibold text-foreground">
                Upcoming Auctions
            </h1>

            <p class="text-sm text-muted-foreground mt-1">
                Review and prepare scheduled auction events
            </p>
        </div>

    </div>

    {{-- Table Card --}}
    <div class="card rounded-xl border border-border overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-muted/40 border-b border-border">

                    <tr>
                        <th class="p-4 text-left font-medium">ID</th>
                        <th class="p-4 text-left font-medium">Title</th>
                        <th class="p-4 text-left font-medium">Date</th>
                        <th class="p-4 text-left font-medium">Lots</th>
                        <th class="p-4 text-left font-medium w-40">Action</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-border">

                    @foreach($upcomingAuctions as $auction)

                    <tr class="hover:bg-muted/20 transition-colors">

                        <td class="p-4">
                            #{{ $auction['id'] }}
                        </td>

                        <td class="p-4 font-medium text-foreground">
                            {{ $auction['title'] }}
                        </td>

                        <td class="p-4 text-muted-foreground">
                            {{ $auction['date'] }}
                        </td>

                        <td class="p-4">
                            {{ $auction['lots'] }}
                        </td>

                        <td class="p-4">

                            <a href="{{ route('auctions.show', $auction['id']) }}"
                               class="kt-btn kt-btn-sm kt-btn-outline">
                                View
                            </a>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</section>

@endsection