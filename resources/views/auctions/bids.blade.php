@extends('layouts.app')

@section('title', 'Auction Bids')

@section('content')

<section class="kt-container-fixed px-4 lg:px-6 py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-2xl font-semibold text-foreground">
                Auction Bids
            </h1>

            <p class="text-sm text-muted-foreground mt-1">
                Monitor all live and historical bidding activity
            </p>
        </div>
    </div>

    {{-- Card --}}
    <div class="card rounded-xl border border-border overflow-hidden">

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-muted/40 border-b border-border">
                    <tr>
                        <th class="p-4 text-left font-medium">Auction</th>
                        <th class="p-4 text-left font-medium">Lot</th>
                        <th class="p-4 text-left font-medium">Bidder</th>
                        <th class="p-4 text-left font-medium">Bid Amount</th>
                        <th class="p-4 text-left font-medium">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @foreach($bids as $bid)
                    <tr class="hover:bg-muted/20 transition-colors">

                        <td class="p-4">
                            {{ $bid['auction'] }}
                        </td>

                        <td class="p-4">
                            {{ $bid['lot'] }}
                        </td>

                        <td class="p-4 font-medium text-foreground">
                            {{ $bid['bidder'] }}
                        </td>

                        <td class="p-4 font-semibold text-green-500">
                            {{ $bid['amount'] }}
                        </td>

                        <td class="p-4">

                            @if($bid['status'] == 'Winning')

                                <span class="kt-badge kt-badge-success kt-badge-outline">
                                    Winning
                                </span>

                            @else

                                <span class="kt-badge kt-badge-danger kt-badge-outline">
                                    Outbid
                                </span>

                            @endif

                        </td>

                    </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</section>

@endsection