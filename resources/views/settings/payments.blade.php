{{-- resources/views/settings/payments.blade.php --}}
{{-- Phase 5 — S5: Settings → Payments --}}
@extends('layouts.app')
@section('title', 'Payments — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed ">

 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Payments</span>
    </nav>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Payments</h1>
            <p class="text-sm text-muted-foreground mt-0.5">PSP keys, webhook URLs, and merchant-initiated mandate text</p>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.payments.update') }}">
        @csrf @method('PATCH')

        {{-- PSP Keys --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center size-9 rounded-lg bg-success/10">
                    <i data-lucide="credit-card" class="w-4 h-4 text-success"></i>
                </span>
                <div>
                    <h2 class="text-sm font-semibold text-foreground">Payment service provider</h2>
                    <p class="text-xs text-muted-foreground">Stripe / GoCardless / Braintree</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Provider</label>
                    <select name="psp_provider" class="kt-input w-full max-w-xs">
                        <option value="stripe"      @selected(($settings['psp_provider'] ?? '') === 'stripe')>Stripe</option>
                        <option value="gocardless"  @selected(($settings['psp_provider'] ?? '') === 'gocardless')>GoCardless</option>
                        <option value="braintree"   @selected(($settings['psp_provider'] ?? '') === 'braintree')>Braintree</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1 font-medium">Public key (publishable)</label>
                        <input type="text" name="psp_public_key" class="kt-input w-full"
                               value="{{ $settings['psp_public_key'] ?? '' }}" placeholder="pk_live_…" />
                    </div>
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1 font-medium">Secret key</label>
                        <div class="relative">
                            <input type="password" name="psp_secret_key" id="psp-secret" class="kt-input w-full pr-10"
                                   value="{{ $settings['psp_secret_key'] ? str_repeat('•', 24) : '' }}"
                                   placeholder="sk_live_…" autocomplete="new-password" />
                            <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground toggle-secret" data-target="psp-secret">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Webhook URL (display only) --}}
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Webhook endpoint URL</label>
                    <div class="flex items-center gap-2">
                        <input type="text" class="kt-input flex-1 bg-muted/30 text-muted-foreground text-xs font-mono" readonly
                               id="webhook-url"
                               value="{{ url('/webhooks/payments') }}" />
                        <button type="button" class="kt-btn kt-btn-outline kt-btn-sm whitespace-nowrap" id="btn-copy-webhook">
                            <i data-lucide="copy" class="w-3.5 h-3.5 mr-1"></i> Copy
                        </button>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">Add this URL to your PSP's webhook settings to receive payment events.</p>
                </div>

                {{-- Webhook secret --}}
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Webhook signing secret</label>
                    <div class="relative max-w-sm">
                        <input type="password" name="psp_webhook_secret" id="psp-whsec" class="kt-input w-full pr-10"
                               value="{{ $settings['psp_webhook_secret'] ? str_repeat('•', 24) : '' }}"
                               placeholder="whsec_…" autocomplete="new-password" />
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground toggle-secret" data-target="psp-whsec">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                {{-- Merchant-initiated text --}}
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">
                        Mandate / merchant-initiated payment text
                    </label>
                    <textarea name="mandate_text" class="kt-input w-full" rows="4"
                              placeholder="By confirming this payment, you authorise Carsmart Ltd (trading as Carsmart) to…">{{ $settings['mandate_text'] ?? '' }}</textarea>
                    <p class="text-xs text-muted-foreground mt-1">Displayed to customers at checkout and on payment confirmation emails.</p>
                </div>
            </div>
        </div>

        {{-- Fee config --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <h2 class="text-sm font-semibold text-foreground mb-4">Fee defaults</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Buyer fee (%)</label>
                    <input type="number" name="buyer_fee_pct" class="kt-input w-full"
                           value="{{ $settings['buyer_fee_pct'] ?? '5' }}" min="0" max="100" step="0.1" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Seller fee (%)</label>
                    <input type="number" name="seller_fee_pct" class="kt-input w-full"
                           value="{{ $settings['seller_fee_pct'] ?? '3' }}" min="0" max="100" step="0.1" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Minimum fee (£)</label>
                    <input type="number" name="min_fee_pounds" class="kt-input w-full"
                           value="{{ $settings['min_fee_pounds'] ?? '50' }}" min="0" step="1" />
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="kt-btn kt-btn-mono">Save payment settings</button>
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

document.getElementById('btn-copy-webhook')?.addEventListener('click', () => {
    navigator.clipboard.writeText(document.getElementById('webhook-url').value)
        .then(() => {
            const btn = document.getElementById('btn-copy-webhook');
            btn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 mr-1"></i> Copied!';
            lucide.createIcons();
            setTimeout(() => {
                btn.innerHTML = '<i data-lucide="copy" class="w-3.5 h-3.5 mr-1"></i> Copy';
                lucide.createIcons();
            }, 2000);
        });
});
</script>
@endpush

@endsection
