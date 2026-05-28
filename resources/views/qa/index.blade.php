@extends('layouts.app')

@section('content')

<div class="container p-4">

<h2>QA Queue</h2>

<table class="table">
@foreach($items as $i)
<tr>
<td>{{ $i['listing'] }}</td>
<td>{{ $i['status'] }}</td>
</tr>
@endforeach
</table>

</div>

@endsection