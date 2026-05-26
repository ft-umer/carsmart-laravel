{{-- A6. Session Expired
     Copy stub: "Your session has expired. Please sign in again."
--}}
@extends('layouts.auth')
@section('title', 'Session Expired')

@section('content')
<div class="flex items-center justify-center grow">
    <div class="kt-card max-w-[400px] w-full mx-4">
        <div class="kt-card-content flex flex-col items-center gap-6 p-10 text-center">
            <div class="flex items-center justify-center size-20 rounded-full bg-warning/10">
                <i class="ki-filled ki-time text-4xl text-warning"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-mono mb-2">Your session has expired</h3>
                <p class="text-sm text-muted-foreground">
                    For your security, you have been signed out after a period of inactivity.
                </p>
            </div>
            <a href="{{ route('login') }}" class="kt-btn kt-btn-primary w-full flex justify-center">
                Sign in again
            </a>
        </div>
    </div>
</div>
@endsection
