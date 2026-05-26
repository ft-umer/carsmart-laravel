{{-- A6. Locked Account
     Purpose: Explain lock state and give a clear route back.
     Copy stub: "Your account is locked. Please contact an administrator."
--}}
@extends('layouts.auth')
@section('title', 'Account Locked')

@section('content')
<div class="flex items-center justify-center grow">
    <div class="kt-card max-w-[400px] w-full mx-4">
        <div class="kt-card-content flex flex-col items-center gap-6 p-10 text-center">
            <div class="flex items-center justify-center size-20 rounded-full bg-danger/10">
                <i class="ki-filled ki-lock text-4xl text-danger"></i>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-mono mb-2">Your account is locked</h3>
                <p class="text-sm text-muted-foreground">
                    Please contact an administrator to unlock your account.
                </p>
            </div>
            <a href="{{ route('login') }}" class="kt-btn kt-btn-primary w-full flex justify-center">
                Back to sign in
            </a>
        </div>
    </div>
</div>
@endsection
