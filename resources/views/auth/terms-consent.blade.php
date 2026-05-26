{{-- A5. First-time Terms Consent
     Purpose: Capture acceptance of internal terms and privacy notices on first sign in.
     Who: First-time users or when terms change.
--}}
@extends('layouts.auth')
@section('title', 'Terms and Notices')

@section('content')
<div class="flex items-center justify-center grow py-10 px-4">
    <div class="kt-card max-w-[540px] w-full">
        <div class="kt-card-content flex flex-col gap-5 p-8 lg:p-10">

            <div class="text-center mb-2.5">
                <img class="h-8 max-w-none mx-auto dark:hidden mb-4" src="{{ asset('assets/media/app/default-logo.svg') }}" alt="Carsmart"/>
                <img class="h-8 max-w-none mx-auto hidden dark:block mb-4" src="{{ asset('assets/media/app/default-logo-dark.svg') }}" alt="Carsmart"/>
                <h3 class="text-lg font-medium text-mono leading-none mb-2.5">Terms and notices</h3>
                <p class="text-sm text-muted-foreground">Please read and accept the following before continuing.</p>
            </div>

            {{-- Scrollable terms text --}}
            <div class="kt-card bg-muted/30 border border-border rounded-lg">
                <div class="h-[240px] overflow-y-auto p-5 text-sm text-secondary-foreground leading-relaxed kt-scrollable-y-auto"
                     data-kt-scrollable="true">
                    <h4 class="font-semibold text-mono mb-3">Internal Use Terms</h4>
                    <p class="mb-3">By accessing and using the Carsmart internal operations platform, you agree to use it solely for authorised business purposes in line with your assigned role and responsibilities.</p>
                    <p class="mb-3">You must keep your credentials confidential and immediately report any suspected unauthorised access. Access is logged and audited. Misuse may result in disciplinary action.</p>
                    <p class="mb-3">All data accessed through this platform is confidential. You must not share, copy, or export data outside of approved workflows.</p>
                    <h4 class="font-semibold text-mono mb-3 mt-4">Privacy Notice</h4>
                    <p class="mb-3">Your activity within this platform is recorded for security and compliance purposes. Data is retained in accordance with our internal data retention policy.</p>
                    <p>For questions, contact your system administrator or the Compliance team.</p>
                </div>
            </div>

            <form action="{{ route('terms.accept') }}" method="POST" class="flex flex-col gap-4">
                @csrf

                <label class="kt-label flex items-start gap-3 cursor-pointer">
                    <input class="kt-checkbox kt-checkbox-sm mt-0.5" type="checkbox" name="agreed" value="1" required/>
                    <span class="kt-checkbox-label text-sm text-secondary-foreground leading-relaxed">
                        I have read and agree to the internal terms of use and privacy notice.
                    </span>
                </label>

                <div class="flex items-center gap-3">
                    <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow">
                        I agree — Continue
                    </button>
                    <a href="#" class="kt-btn kt-btn-outline" target="_blank" rel="noopener">
                        <i class="ki-filled ki-document text-sm me-1.5"></i>
                        View privacy notice
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
