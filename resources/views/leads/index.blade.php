@extends('layouts.app')

@section('title', 'Leads — CRM')

@section('content')
<section id="listings-module"
class="kt-container-fixed grow px-4 lg:px-6 py-6">

    @include('leads.partials.toolbar')

    @include('leads.partials.filters')

    @include('leads.partials.flash')

    @if ($view !== 'board')
        <div class="grid grid-cols-1 xl:grid-cols-[1fr,380px] gap-5">

            @include('leads.partials.table')

            @include('leads.partials.quick-view')

        </div>
    @endif

    @if ($view === 'board')
        @include('leads.partials.board')
    @endif

    @include('leads.partials.modals.quick-add')

    @include('leads.partials.modals.delete')

    @include('leads.partials.modals.add-valuation')

    @include('leads.partials.modals.apply-pricing')

    @include('leads.partials.modals.bulk-assign')

    @include('leads.partials.modals.bulk-stage')

    @include('leads.partials.toast-container')

</section>
@endsection

@push('scripts')
    @include('leads.partials.scripts')
@endpush
