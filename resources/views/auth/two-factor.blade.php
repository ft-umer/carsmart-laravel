{{-- A4. Two-factor Verification (optional)
     Purpose: If enabled, require a second step after correct credentials.
     Who: Roles configured for two-factor.
--}}
@extends('layouts.auth')
@section('title', 'Two-factor Verification')

@section('content')
<div class="flex items-center justify-center grow bg-center bg-no-repeat">
    <div class="kt-card max-w-[370px] w-full">
        <div class="kt-card-content flex flex-col gap-5 p-10">

            <div class="text-center mb-2.5">
                <div class="flex items-center justify-center size-16 rounded-full bg-primary/10 mx-auto mb-4">
                    <i class="ki-filled ki-shield-tick text-3xl text-primary"></i>
                </div>
                <h3 class="text-lg font-medium text-mono leading-none mb-2.5">Two-factor verification</h3>
                <p class="text-sm text-muted-foreground">Enter the 6-digit code we sent to your registered device.</p>
            </div>

            {{-- Error states --}}
            @if (session('error'))
                <div class="kt-alert kt-alert-danger flex items-center gap-2" role="alert">
                    <i class="ki-filled ki-shield-cross text-danger"></i>
                    <span class="text-sm">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="kt-alert kt-alert-danger flex items-center gap-2" role="alert">
                    <i class="ki-filled ki-shield-cross text-danger"></i>
                    <span class="text-sm">{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('two-factor.verify') }}" method="POST" class="flex flex-col gap-5">
                @csrf

                {{-- 6-digit code input --}}
                <div class="flex flex-col gap-1">
                    <label for="code" class="kt-form-label font-normal text-mono">Verification code</label>
                    <input id="code"
                           name="code"
                           type="text"
                           inputmode="numeric"
                           pattern="[0-9]{6}"
                           maxlength="6"
                           class="kt-input text-center tracking-[0.5em] text-lg @error('code') border-danger @enderror"
                           placeholder="• • • • • •"
                           required
                           autofocus
                           autocomplete="one-time-code"/>
                    @error('code')
                        <span class="text-xs text-danger mt-0.5">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">Verify</button>
            </form>

            <div class="flex items-center justify-between gap-2 text-sm">
                <form action="{{ route('two-factor.resend') }}" method="POST">
                    @csrf
                    <button type="submit" class="kt-link">Resend code</button>
                </form>
                @if(config('auth.two_factor.backup_codes', true))
                    <a href="{{ route('two-factor.backup') }}" class="kt-link">Use backup code</a>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
