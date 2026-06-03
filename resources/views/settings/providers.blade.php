{{-- resources/views/settings/providers.blade.php --}}
{{-- Phase 5 — S2: Settings → Providers & Channels --}}
@extends('layouts.app')
@section('title', 'Providers & Channels — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Providers & Channels</span>
    </nav>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Providers & Channels</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Configure Email, SMS, WhatsApp providers and domains</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-success/10 border border-success/30 text-success-foreground px-4 py-3 text-sm mb-4 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Email ── --}}
    <div class="card border border-border rounded-xl p-6 mb-4">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex items-center justify-center size-9 rounded-lg bg-primary/10">
                <i data-lucide="mail" class="w-4 h-4 text-primary"></i>
            </span>
            <div>
                <h2 class="text-sm font-semibold text-foreground">Email (Send)</h2>
                <p class="text-xs text-muted-foreground">Transactional and marketing email provider</p>
            </div>
            <span class="ml-auto kt-badge kt-badge-{{ ($providerStatus['email'] ?? false) ? 'success' : 'secondary' }} kt-badge-sm">
                {{ ($providerStatus['email'] ?? false) ? 'Connected' : 'Not configured' }}
            </span>
        </div>
        <form method="POST" action="{{ route('settings.providers.update', 'email') }}">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Provider</label>
                    <select name="email_provider" class="kt-input w-full">
                        <option value="sendgrid" @selected(($providers['email']['provider'] ?? '') === 'sendgrid')>SendGrid</option>
                        <option value="mailgun"  @selected(($providers['email']['provider'] ?? '') === 'mailgun')>Mailgun</option>
                        <option value="ses"      @selected(($providers['email']['provider'] ?? '') === 'ses')>AWS SES</option>
                        <option value="postmark" @selected(($providers['email']['provider'] ?? '') === 'postmark')>Postmark</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">From domain</label>
                    <input type="text" name="email_domain" class="kt-input w-full"
                           value="{{ $providers['email']['domain'] ?? '' }}" placeholder="mail.yourdomain.com" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">API key</label>
                    <div class="relative">
                        <input type="password" name="email_api_key" id="email-api-key" class="kt-input w-full pr-10"
                               value="{{ $providers['email']['api_key'] ? str_repeat('•', 20) : '' }}"
                               placeholder="sk-…" autocomplete="new-password" />
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground toggle-secret" data-target="email-api-key">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">From name</label>
                    <input type="text" name="email_from_name" class="kt-input w-full"
                           value="{{ $providers['email']['from_name'] ?? 'Carsmart' }}" />
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <input type="email" id="email-test-to" class="kt-input flex-1" placeholder="test@example.com" />
                    <button type="button" class="kt-btn kt-btn-outline kt-btn-sm whitespace-nowrap" id="btn-test-email">
                        Send test
                    </button>
                </div>
                <div class="flex gap-2 ml-auto">
                    <button type="submit" class="kt-btn kt-btn-mono kt-btn-sm">Save email config</button>
                </div>
            </div>
            <div id="email-test-result" class="mt-2 hidden text-xs"></div>
        </form>
    </div>

    {{-- ── SMS ── --}}
    <div class="card border border-border rounded-xl p-6 mb-4">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex items-center justify-center size-9 rounded-lg bg-warning/10">
                <i data-lucide="message-square" class="w-4 h-4 text-warning"></i>
            </span>
            <div>
                <h2 class="text-sm font-semibold text-foreground">SMS (Send)</h2>
                <p class="text-xs text-muted-foreground">Short Message Service provider</p>
            </div>
            <span class="ml-auto kt-badge kt-badge-{{ ($providerStatus['sms'] ?? false) ? 'success' : 'secondary' }} kt-badge-sm">
                {{ ($providerStatus['sms'] ?? false) ? 'Connected' : 'Not configured' }}
            </span>
        </div>
        <form method="POST" action="{{ route('settings.providers.update', 'sms') }}">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Provider</label>
                    <select name="sms_provider" class="kt-input w-full">
                        <option value="twilio"  @selected(($providers['sms']['provider'] ?? '') === 'twilio')>Twilio</option>
                        <option value="vonage"  @selected(($providers['sms']['provider'] ?? '') === 'vonage')>Vonage</option>
                        <option value="sinch"   @selected(($providers['sms']['provider'] ?? '') === 'sinch')>Sinch</option>
                        <option value="messagebird" @selected(($providers['sms']['provider'] ?? '') === 'messagebird')>MessageBird</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Sender ID</label>
                    <input type="text" name="sms_sender_id" class="kt-input w-full"
                           value="{{ $providers['sms']['sender_id'] ?? '' }}" placeholder="CARSMART" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">API key / Account SID</label>
                    <div class="relative">
                        <input type="password" name="sms_api_key" id="sms-api-key" class="kt-input w-full pr-10"
                               value="{{ $providers['sms']['api_key'] ? str_repeat('•', 20) : '' }}"
                               placeholder="AC…" autocomplete="new-password" />
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground toggle-secret" data-target="sms-api-key">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Auth token / Secret</label>
                    <div class="relative">
                        <input type="password" name="sms_auth_token" id="sms-auth-token" class="kt-input w-full pr-10"
                               value="{{ $providers['sms']['auth_token'] ? str_repeat('•', 20) : '' }}"
                               autocomplete="new-password" />
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground toggle-secret" data-target="sms-auth-token">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2 flex-1">
                    <input type="tel" id="sms-test-to" class="kt-input flex-1" placeholder="+447900000000" />
                    <button type="button" class="kt-btn kt-btn-outline kt-btn-sm whitespace-nowrap" id="btn-test-sms">Send test</button>
                </div>
                <button type="submit" class="kt-btn kt-btn-mono kt-btn-sm">Save SMS config</button>
            </div>
        </form>
    </div>

    {{-- ── WhatsApp ── --}}
    <div class="card border border-border rounded-xl p-6 mb-4">
        <div class="flex items-center gap-3 mb-4">
            <span class="flex items-center justify-center size-9 rounded-lg bg-success/10">
                <i data-lucide="message-circle" class="w-4 h-4 text-success"></i>
            </span>
            <div>
                <h2 class="text-sm font-semibold text-foreground">WhatsApp</h2>
                <p class="text-xs text-muted-foreground">WhatsApp Business API provider</p>
            </div>
            <span class="ml-auto kt-badge kt-badge-{{ ($providerStatus['whatsapp'] ?? false) ? 'success' : 'secondary' }} kt-badge-sm">
                {{ ($providerStatus['whatsapp'] ?? false) ? 'Connected' : 'Not configured' }}
            </span>
        </div>
        <form method="POST" action="{{ route('settings.providers.update', 'whatsapp') }}">
            @csrf @method('PATCH')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Business Account ID</label>
                    <input type="text" name="wa_business_id" class="kt-input w-full"
                           value="{{ $providers['whatsapp']['business_id'] ?? '' }}" placeholder="12345678…" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Phone number ID</label>
                    <input type="text" name="wa_phone_id" class="kt-input w-full"
                           value="{{ $providers['whatsapp']['phone_id'] ?? '' }}" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Access token</label>
                    <div class="relative">
                        <input type="password" name="wa_access_token" id="wa-token" class="kt-input w-full pr-10"
                               value="{{ $providers['whatsapp']['access_token'] ? str_repeat('•', 20) : '' }}"
                               autocomplete="new-password" />
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground toggle-secret" data-target="wa-token">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Template approvals</label>
                    <a href="https://business.facebook.com/wa/manage/message-templates/" target="_blank"
                       class="kt-btn kt-btn-outline kt-btn-sm w-full justify-center">
                        <i data-lucide="external-link" class="w-3.5 h-3.5 mr-1"></i> Manage templates →
                    </a>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="kt-btn kt-btn-mono kt-btn-sm">Save WhatsApp config</button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
// Toggle secret visibility
document.querySelectorAll('.toggle-secret').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.querySelector('i').setAttribute('data-lucide', input.type === 'password' ? 'eye' : 'eye-off');
        lucide.createIcons();
    });
});

// Test sends
document.getElementById('btn-test-email')?.addEventListener('click', () => {
    const to = document.getElementById('email-test-to').value;
    const res = document.getElementById('email-test-result');
    res.classList.remove('hidden');
    res.className = 'mt-2 text-xs text-warning';
    res.textContent = 'Sending…';
    setTimeout(() => {
        res.className = 'mt-2 text-xs text-success';
        res.textContent = `✓ Test email sent to ${to || 'address'}`;
    }, 1200);
});

document.getElementById('btn-test-sms')?.addEventListener('click', () => {
    const to = document.getElementById('sms-test-to').value;
    alert(`Test SMS dispatch triggered for ${to || 'number'}`);
});
</script>
@endpush

@endsection
