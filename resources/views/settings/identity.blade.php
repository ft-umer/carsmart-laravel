{{-- resources/views/settings/identity.blade.php --}}
{{-- Phase 5 — S3: Settings → Identity & Compliance --}}
@extends('layouts.app')
@section('title', 'Identity & Compliance — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Identity & Compliance</span>
    </nav>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Identity & Compliance</h1>
            <p class="text-sm text-muted-foreground mt-0.5">KYC / KYB provider, required documents, and override policy</p>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.identity.update') }}">
        @csrf @method('PATCH')

        {{-- Provider --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <h2 class="text-sm font-semibold text-foreground mb-4">KYC / KYB Provider</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Provider</label>
                    <select name="kyc_provider" class="kt-input w-full">
                        <option value="onfido"   @selected(($settings['kyc_provider'] ?? '') === 'onfido')>Onfido</option>
                        <option value="jumio"    @selected(($settings['kyc_provider'] ?? '') === 'jumio')>Jumio</option>
                        <option value="yoti"     @selected(($settings['kyc_provider'] ?? '') === 'yoti')>Yoti</option>
                        <option value="sumsub"   @selected(($settings['kyc_provider'] ?? '') === 'sumsub')>Sumsub</option>
                        <option value="manual"   @selected(($settings['kyc_provider'] ?? '') === 'manual')>Manual review</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">API key</label>
                    <div class="relative">
                        <input type="password" name="kyc_api_key" id="kyc-api-key" class="kt-input w-full pr-10"
                               value="{{ $settings['kyc_api_key'] ? str_repeat('•', 20) : '' }}"
                               autocomplete="new-password" />
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground toggle-secret" data-target="kyc-api-key">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Required documents --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <h2 class="text-sm font-semibold text-foreground mb-4">Required documents per role</h2>
            @foreach([
                ['Individual seller', 'individual_docs'],
                ['Business seller (KYB)', 'business_docs'],
                ['Buyer / bidder', 'buyer_docs'],
            ] as [$roleLabel, $fieldKey])
                <div class="mb-4 last:mb-0">
                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">{{ $roleLabel }}</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach([
                            'passport'          => 'Passport',
                            'driving_licence'   => "Driving licence",
                            'national_id'       => 'National ID',
                            'utility_bill'      => 'Utility bill',
                            'bank_statement'    => 'Bank statement',
                            'company_cert'      => 'Company certificate',
                            'vat_reg'           => 'VAT registration',
                            'selfie'            => 'Selfie / liveness',
                        ] as $docKey => $docLabel)
                            <label class="flex items-center gap-2 text-sm cursor-pointer p-1.5 rounded hover:bg-muted/30">
                                <input type="checkbox" name="{{ $fieldKey }}[]" value="{{ $docKey }}" class="kt-checkbox"
                                       {{ in_array($docKey, $settings[$fieldKey] ?? []) ? 'checked' : '' }} />
                                {{ $docLabel }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- States mapping --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <h2 class="text-sm font-semibold text-foreground mb-4">State labels mapping</h2>
            <div class="space-y-2">
                @foreach([
                    'not_required' => 'Not required',
                    'pending'      => 'Pending',
                    'needs_docs'   => 'Needs documents',
                    'in_review'    => 'In review',
                    'verified'     => 'Verified',
                    'failed'       => 'Failed',
                ] as $key => $default)
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-mono text-muted-foreground w-28 shrink-0">{{ $key }}</span>
                        <input type="text" name="state_label[{{ $key }}]" class="kt-input flex-1"
                               value="{{ $settings['state_labels'][$key] ?? $default }}" />
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Override policy --}}
        @can('super-admin')
        <div class="card border border-border rounded-xl p-6 mb-4 border-warning/30 bg-warning/5">
            <div class="flex items-start gap-3 mb-4">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-warning shrink-0 mt-0.5"></i>
                <div>
                    <h2 class="text-sm font-semibold text-foreground">KYC Override policy</h2>
                    <p class="text-xs text-muted-foreground">Super Admin only. All overrides are logged and audited.</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Roles allowed to override</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                            <input type="checkbox" class="kt-checkbox" checked disabled /> Super Admin
                        </label>
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                            <input type="checkbox" name="override_compliance" class="kt-checkbox"
                                   {{ ($settings['override_compliance'] ?? false) ? 'checked' : '' }} /> Compliance
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Require override reason <span class="text-destructive">*</span></label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                            <input type="radio" name="require_reason" value="1" class="kt-radio"
                                   {{ ($settings['require_reason'] ?? true) ? 'checked' : '' }} /> Yes (required)
                        </label>
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                            <input type="radio" name="require_reason" value="0" class="kt-radio" /> No
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Require supporting attachment</label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                            <input type="radio" name="require_attachment" value="1" class="kt-radio"
                                   {{ ($settings['require_attachment'] ?? true) ? 'checked' : '' }} /> Yes (required)
                        </label>
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                            <input type="radio" name="require_attachment" value="0" class="kt-radio" /> No
                        </label>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <div class="flex justify-end">
            <button type="submit" class="kt-btn kt-btn-mono">Save identity settings</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelectorAll('.toggle-secret').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.querySelector('i').setAttribute('data-lucide', input.type === 'password' ? 'eye' : 'eye-off');
        lucide.createIcons();
    });
});
</script>
@endpush

@endsection
