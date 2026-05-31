@extends('layouts.app')

@section('title', 'Listings')

@section('content')

<section id="listings-module" class="kt-container-fixed grow px-4 lg:px-6 py-6">

    @include('listings.partials.topbar')

    {{-- LISTINGS VIEW --}}
    <div id="view-listings">
        <div id="listings-filters">
            @include('listings.partials.filters')
        </div>
        @include('listings.partials.listings-table')
    </div>

    {{-- VALUATIONS VIEW --}}
    <div id="view-valuations" class="hidden">
        @php
            $valuations = $valuations ?? [];
            $recommendedGuide = 14250;
            $reserveLow = 13500;
            $reserveHigh = 14000;
            $valuationStatus = 'idle';
        @endphp
        @include('listings.partials.valuation-panel')
    </div>

    {{-- QA QUEUE VIEW --}}
    <div id="view-qa" class="hidden">
        @include('listings.partials.qa-queue')
    </div>

    {{-- PUBLICATION QUEUE VIEW --}}
    <div id="view-publication" class="hidden">
        @include('listings.partials.publication-queue')
    </div>

    {{-- EXCHANGE PROPOSALS VIEW --}}
    <div id="view-exchange" class="hidden">
        @include('listings.partials.exchange-proposals')
    </div>

    {{-- LIFECYCLE VIEW --}}
    <div id="view-lifecycle" class="hidden">
        @include('listings.partials.lifecycle')
    </div>

</section>

{{-- MODALS --}}
@include('listings.modals.listing-detail-modal')
@include('listings.modals.add-valuation-modal')
@include('listings.modals.apply-pricing-modal')
@include('listings.modals.create-listing-modal')
@include('listings.modals.quick-view-modal')

@include('listings.scripts.listings-scripts')

@endsection
