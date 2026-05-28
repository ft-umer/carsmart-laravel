@extends('layouts.app')

@section('content')

<div class="container-fluid p-4">

<h3>{{ $listing['ref'] }} - {{ $listing['vehicle'] }}</h3>

<div class="row">

<div class="col-md-8">

<div class="card p-3">
<h5>Overview</h5>
<p>State: {{ $listing['state'] }}</p>
<p>Reserve: £{{ $listing['reserve'] }}</p>
<p>BIN: £{{ $listing['bin'] }}</p>
<p>Valuation: £{{ $listing['valuation'] }}</p>
</div>

</div>

<div class="col-md-4">
@include('listings.partials.valuation-panel')
</div>

</div>

</div>

@endsection