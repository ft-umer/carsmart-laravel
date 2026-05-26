@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="p-6">
    <h1 class="text-2xl font-bold mb-2">
        Welcome to {{ config('app.name') }}
    </h1>

    <p class="text-gray-600">
        Your dashboard system is ready.
    </p>
</div>

@endsection