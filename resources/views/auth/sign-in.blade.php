{{-- A1. Sign In
     Purpose: Allow an internal user to enter Admin or CRM and land on the correct home screen.
     Who: All internal roles.
--}}
@extends('layouts.auth')

@section('title', 'Sign In')

@push('styles')
<style>
    .branded-bg {
        background-image: url('{{ asset('assets/media/images/2600x1600/1.png') }}');
    }
    .dark .branded-bg {
        background-image: url('{{ asset('assets/media/images/2600x1600/1-dark.png') }}');
    }
</style>
@endpush

@section('content')
<div class="grid lg:grid-cols-2 grow">

    {{-- Form column --}}
    <div class="flex justify-center items-center p-8 lg:p-10 order-2 lg:order-1">
        <div class="kt-card max-w-[370px] w-full">

            <form action="{{ route('login') }}"
                  method="POST"
                  class="kt-card-content flex flex-col gap-5 p-10"
                  id="sign_in_form">
                @csrf

                {{-- Heading --}}
                <div class="text-center mb-2.5">
                    <a href="{{ route('dashboard') }}" class="inline-block mb-4">
                        <img class="h-8 max-w-none mx-auto dark:hidden" src="{{ asset('assets/media/app/default-logo.svg') }}" alt="Carsmart"/>
                        <img class="h-8 max-w-none mx-auto hidden dark:block" src="{{ asset('assets/media/app/default-logo-dark.svg') }}" alt="Carsmart"/>
                    </a>
                    <h3 class="text-lg font-medium text-mono leading-none mb-2.5">Sign in</h3>
                    <p class="text-sm text-muted-foreground">Internal operations portal</p>
                </div>

                {{-- Error banner (A1 States: Error) --}}
                @if ($errors->any() || session('error'))
                    <div class="kt-alert kt-alert-danger flex items-center gap-2" role="alert">
                        <i class="ki-filled ki-shield-cross text-danger"></i>
                        <span class="text-sm">
                            {{ session('error') ?? 'We could not sign you in. Please check your details and try again.' }}
                        </span>
                    </div>
                @endif

                {{-- Locked account redirect notice --}}
                @if (session('status') === 'locked')
                    <div class="kt-alert kt-alert-warning flex items-center gap-2" role="alert">
                        <i class="ki-filled ki-information-2 text-warning"></i>
                        <span class="text-sm">{{ __('Your account is locked. Please contact an administrator.') }}</span>
                    </div>
                @endif

                {{-- Email address (A1 required field) --}}
                <div class="flex flex-col gap-1">
                    <label for="email" class="kt-form-label font-normal text-mono">
                        Email address
                    </label>
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

                {{-- Password (A1 required, show/hide) --}}
                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between gap-1">
                        <label for="password" class="kt-form-label font-normal text-mono">Password</label>
                        <a href="{{ route('password.request') }}" class="text-sm kt-link shrink-0">
                            Forgot password
                        </a>
                    </div>
                    <div class="kt-input @error('password') border-danger @enderror"
                         data-kt-toggle-password="true">
                        <input id="password"
                               name="password"
                               type="password"
                               placeholder="Enter password"
                               required
                               autocomplete="current-password"/>
                        <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5"
                                data-kt-toggle-password-trigger="true"
                                type="button"
                                aria-label="Toggle password visibility">
                            <span class="kt-toggle-password-active:hidden">
                                <i class="ki-filled ki-eye text-muted-foreground"></i>
                            </span>
                            <span class="hidden kt-toggle-password-active:block">
                                <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                            </span>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-xs text-danger mt-0.5">{{ $message }}</span>
                    @enderror
                </div>

                {{-- App choice — A1: remembers last used --}}
                <div class="flex flex-col gap-1.5">
                    <span class="kt-form-label font-normal text-mono">Application</span>
                    <div class="flex items-center gap-5">
                        <label class="kt-label flex items-center gap-2 cursor-pointer">
                            <input class="kt-radio kt-radio-sm"
                                   type="radio"
                                   name="app_target"
                                   value="admin"
                                   {{ old('app_target', session('last_app', 'admin')) === 'admin' ? 'checked' : '' }}/>
                            <span class="text-sm text-mono">Admin</span>
                        </label>
                        <label class="kt-label flex items-center gap-2 cursor-pointer">
                            <input class="kt-radio kt-radio-sm"
                                   type="radio"
                                   name="app_target"
                                   value="crm"
                                   {{ old('app_target', session('last_app', 'admin')) === 'crm' ? 'checked' : '' }}/>
                            <span class="text-sm text-mono">Customer Relationship Management</span>
                        </label>
                    </div>
                </div>

                {{-- Remember me (A1) --}}
                <label class="kt-label">
                    <input class="kt-checkbox kt-checkbox-sm"
                           type="checkbox"
                           name="remember"
                           value="1"
                           {{ old('remember') ? 'checked' : '' }}/>
                    <span class="kt-checkbox-label">Remember me</span>
                </label>

                {{-- Sign in button --}}
                <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
                    Sign in
                </button>

            </form>
        </div>
    </div>

    {{-- Branded hero column --}}
    <div class="lg:rounded-xl lg:border lg:border-border lg:m-5 order-1 lg:order-2 bg-top xl:bg-cover bg-no-repeat branded-bg">
        <div class="flex flex-col p-8 lg:p-16 gap-4">
            <img class="h-7 max-w-none" src="{{ asset('assets/media/app/default-logo-dark.svg') }}" alt="Carsmart"/>
            <div class="flex flex-col gap-3 mt-auto">
                <h3 class="text-2xl font-semibold text-white">Secure Operations Portal</h3>
                <p class="text-base font-medium text-white/70">
                    A robust authentication gateway ensuring<br/>
                    secure, <span class="text-white font-semibold">efficient access</span> to the Carsmart<br/>
                    internal dashboard.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
