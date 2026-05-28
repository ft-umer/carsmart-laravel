@extends('layouts.app')

@section('content')

<div class="container p-4">

<h2>Valuations</h2>

<table class="table">
@foreach($valuations as $v)
<tr>
<td>{{ $v['source'] }}</td>
<td>£{{ $v['amount'] }}</td>
</tr>
@endforeach
</table>

</div>

@endsection