@extends('layouts.app')

@section('content')

<div class="container p-4">

<h2>Create Listing</h2>

<form method="POST" action="/listings/store">
@csrf

<input class="form-control mb-2" placeholder="VRM">
<input class="form-control mb-2" placeholder="Make/Model">
<input class="form-control mb-2" placeholder="Mileage">

<button class="btn btn-success">Save</button>

</form>

</div>

@endsection