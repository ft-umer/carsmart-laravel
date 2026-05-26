{{-- A3. Reset Password
     Purpose: Let a user set a new password using a valid token.
     Who: All internal users with a valid reset link.
--}}
@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
<div class="flex items-center justify-center grow bg-center bg-no-repeat">
    <div class="kt-card max-w-[370px] w-full">
        <div class="kt-card-content flex flex-col gap-5 p-10">

            <div class="text-center mb-2.5">
                <img class="h-8 max-w-none mx-auto dark:hidden mb-4" src="{{ asset('assets/media/app/default-logo.svg') }}" alt="Carsmart"/>
                <img class="h-8 max-w-none mx-auto hidden dark:block mb-4" src="{{ asset('assets/media/app/default-logo-dark.svg') }}" alt="Carsmart"/>
                <h3 class="text-lg font-medium text-mono leading-none mb-2.5">Reset password</h3>
                <p class="text-sm text-muted-foreground">Choose a strong new password.</p>
            </div>

            {{-- Token expired state --}}
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

            <form action="{{ route('password.update') }}" method="POST" class="flex flex-col gap-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}"/>

                <div class="flex flex-col gap-1">
                    <label for="email" class="kt-form-label font-normal text-mono">Email address</label>
                    <input id="email" name="email" type="email"
                           class="kt-input @error('email') border-danger @enderror"
                           value="{{ old('email', request()->email) }}"
                           placeholder="email@example.com" required autocomplete="email"/>
                </div>

                {{-- New password (A3) --}}
                <div class="flex flex-col gap-1">
                    <label for="password" class="kt-form-label font-normal text-mono">New password</label>
                    <div class="kt-input @error('password') border-danger @enderror" data-kt-toggle-password="true">
                        <input id="password" name="password" type="password"
                               placeholder="Enter new password" required autocomplete="new-password"/>
                        <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5"
                                data-kt-toggle-password-trigger="true" type="button" aria-label="Toggle password visibility">
                            <span class="kt-toggle-password-active:hidden"><i class="ki-filled ki-eye text-muted-foreground"></i></span>
                            <span class="hidden kt-toggle-password-active:block"><i class="ki-filled ki-eye-slash text-muted-foreground"></i></span>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-xs text-danger mt-0.5">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-muted-foreground mt-0.5">Minimum 8 characters, mix of letters and numbers.</p>
                </div>

                {{-- Confirm new password (A3) --}}
                <div class="flex flex-col gap-1">
                    <label for="password_confirmation" class="kt-form-label font-normal text-mono">Confirm new password</label>
                    <div class="kt-input" data-kt-toggle-password="true">
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               placeholder="Repeat new password" required autocomplete="new-password"/>
                        <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5"
                                data-kt-toggle-password-trigger="true" type="button" aria-label="Toggle password visibility">
                            <span class="kt-toggle-password-active:hidden"><i class="ki-filled ki-eye text-muted-foreground"></i></span>
                            <span class="hidden kt-toggle-password-active:block"><i class="ki-filled ki-eye-slash text-muted-foreground"></i></span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">Set password</button>
            </form>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-sm kt-link">Back to sign in</a>
            </div>

        </div>
    </div>
</div>
@endsection
