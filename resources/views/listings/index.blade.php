@extends('layouts.app')

@section('title', 'Listings')

@section('content')

<section id="listings-module"
class="kt-container-fixed grow px-4 lg:px-6 py-6">

    @include('listings.partials.topbar')

    @include('listings.partials.filters')

    @include('listings.partials.listings-table')

    @include('listings.partials.valuations-table')

</section>

@include('listings.modals.listing-detail-modal')

@include('listings.modals.add-valuation-modal')

@include('listings.modals.apply-pricing-modal')

@include('listings.modals.create-listing-modal')

@include('listings.modals.quick-view-modal')

@include('listings.scripts.listings-scripts')

@endsection