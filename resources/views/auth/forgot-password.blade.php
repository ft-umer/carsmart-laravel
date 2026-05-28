{{-- A2. Forgot Password
     Purpose: Start password reset by sending a one-time link.
     Who: All internal users.
--}}
@extends('layouts.auth')
@section('title', 'Forgot Password')

@section('content')
<div class="flex items-center justify-center grow bg-center bg-no-repeat">
    <div class="kt-card max-w-[370px] w-full">
        <div class="kt-card-content flex flex-col gap-5 p-10">

            <div class="text-center mb-2.5">
                <a href="{{ route('login') }}" class="inline-block mb-4">
                    <img id="logoHeader" class="h-8 max-w-none mx-auto" />
                </a>
                <h3 class="text-lg font-medium text-mono leading-none mb-2.5">Forgot your password?</h3>
                <p class="text-sm text-muted-foreground">Enter your email and we will send you a reset link.</p>
            </div>

            {{-- Success state (A2: generic — do not disclose whether account exists) --}}
            @if (session('status'))
                <div class="kt-alert kt-alert-success flex items-center gap-2" role="alert">
                    <i class="ki-filled ki-check-circle text-success"></i>
                    <span class="text-sm">Check your email — if an account exists you will receive a reset link shortly.</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="kt-alert kt-alert-danger flex items-center gap-2" role="alert">
                    <i class="ki-filled ki-shield-cross text-danger"></i>
                    <span class="text-sm">Something went wrong. Please try again.</span>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="flex flex-col gap-5">
                @csrf

                <div class="flex flex-col gap-1">
                    <label for="email" class="kt-form-label font-normal text-mono">Email address</label>
                    <input id="email"
                           name="email"
                           type="email"
                           class="kt-input @error('email') border-danger @enderror"
                           placeholder="email@example.com"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="email"/>
                    @error('email')
                        <span class="text-xs text-danger mt-0.5">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
                    Send reset link
                </button>
            </form>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm kt-link">Back to sign in</a>
            </div>

        </div>
    </div>
</div>
@endsection
