{{-- resources/views/notifications/preferences.blade.php --}}
{{-- Phase 5 — N1: Notification Preferences (per user) --}}
@extends('layouts.app')
@section('title', 'Notification Preferences — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('notifications.index') }}" class="hover:text-foreground transition-colors">Notifications</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Notification Preferences</span>
    </nav>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('notifications.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Notification Preferences</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Control which notifications you receive and on which channel</p>
        </div>
    </div>

    <form method="POST" action="{{ route('notifications.preferences.update') }}">
        @csrf @method('PATCH')

        {{-- Channel matrix table --}}
        <div class="card border border-border rounded-xl overflow-hidden mb-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/40">
                        <tr>
                            <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide min-w-[200px]">Category</th>
                            @foreach(['In-app','Email','SMS','WhatsApp'] as $ch)
                                <th class="p-3 text-center text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                                    <div class="flex flex-col items-center gap-1">
                                        <i data-lucide="{{ match($ch) {
                                            'In-app'   => 'bell',
                                            'Email'    => 'mail',
                                            'SMS'      => 'message-square',
                                            'WhatsApp' => 'message-circle',
                                        } }}" class="w-4 h-4"></i>
                                        {{ $ch }}
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background">
                        @php
                            $categories = [
                                ['key' => 'listings',      'label' => 'Listings',          'desc' => 'New listings, status changes, QA alerts'],
                                ['key' => 'auctions',      'label' => 'Auctions',           'desc' => 'Auction starts, closing, outbids, results'],
                                ['key' => 'valuations',    'label' => 'Valuations',          'desc' => 'Valuation fetch results, deltas, failures'],
                                ['key' => 'leads',         'label' => 'Leads',              'desc' => 'New leads, assignments, follow-up reminders'],
                                ['key' => 'deals',         'label' => 'Deals',              'desc' => 'Deal created, milestones, completion'],
                                ['key' => 'finance',       'label' => 'Finance',            'desc' => 'Payout requests, fee notices, wallet events'],
                                ['key' => 'logistics',     'label' => 'Logistics',          'desc' => 'Job assigned, pickups, delivery updates'],
                                ['key' => 'disputes',      'label' => 'Disputes',           'desc' => 'New cases, SLA warnings, decisions'],
                                ['key' => 'kyc_kyb',       'label' => 'KYC / KYB',          'desc' => 'Identity checks, pending docs, overrides'],
                                ['key' => 'automations',   'label' => 'Automations',        'desc' => 'Journey failures, suppression alerts'],
                                ['key' => 'system',        'label' => 'System & security',  'desc' => 'Login events, setting changes, audit alerts'],
                            ];
                            $channels = ['inapp', 'email', 'sms', 'whatsapp'];
                        @endphp

                        @foreach($categories as $cat)
                            <tr class="hover:bg-muted/20 transition-colors">
                                <td class="p-3">
                                    <div class="font-medium text-foreground text-sm">{{ $cat['label'] }}</div>
                                    <div class="text-xs text-muted-foreground mt-0.5">{{ $cat['desc'] }}</div>
                                </td>
                                @foreach($channels as $ch)
                                    <td class="p-3 text-center">
                                        <input type="checkbox"
                                               name="prefs[{{ $cat['key'] }}][{{ $ch }}]"
                                               class="kt-checkbox mx-auto"
                                               value="1"
                                               {{ ($userPrefs[$cat['key']][$ch] ?? ($ch === 'inapp')) ? 'checked' : '' }} />
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Digest settings --}}
        <div class="card border border-border rounded-xl p-5 mb-4">
            <h2 class="text-sm font-semibold text-foreground mb-4">Digest & summary emails</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Daily digest frequency</label>
                    <select name="digest_frequency" class="kt-input w-full max-w-xs">
                        <option value="none"    @selected(($prefs['digest_frequency'] ?? 'none') === 'none')>No digest</option>
                        <option value="daily"   @selected(($prefs['digest_frequency'] ?? 'none') === 'daily')>Daily summary</option>
                        <option value="weekly"  @selected(($prefs['digest_frequency'] ?? 'none') === 'weekly')>Weekly summary</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Digest delivery time</label>
                    <input type="time" name="digest_time" class="kt-input max-w-xs"
                           value="{{ $prefs['digest_time'] ?? '08:00' }}" />
                </div>
            </div>
        </div>

        {{-- Personal quiet hours --}}
        <div class="card border border-border rounded-xl p-5 mb-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-foreground">Personal quiet hours</h2>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="personal_quiet_enabled" class="kt-checkbox" id="personal-quiet-toggle"
                           {{ ($prefs['personal_quiet_enabled'] ?? false) ? 'checked' : '' }} />
                    <span class="text-sm">Override platform default</span>
                </label>
            </div>
            <div class="grid grid-cols-2 gap-4" id="personal-quiet-fields"
                 style="{{ ($prefs['personal_quiet_enabled'] ?? false) ? '' : 'opacity:0.4;pointer-events:none' }}">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">From</label>
                    <input type="time" name="personal_quiet_start" class="kt-input w-full"
                           value="{{ $prefs['personal_quiet_start'] ?? '22:00' }}" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">To</label>
                    <input type="time" name="personal_quiet_end" class="kt-input w-full"
                           value="{{ $prefs['personal_quiet_end'] ?? '07:00' }}" />
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="kt-btn kt-btn-mono">Save preferences</button>
        </div>
    </form>

</div>

@push('scripts')
<script>
document.getElementById('personal-quiet-toggle')?.addEventListener('change', function() {
    const fields = document.getElementById('personal-quiet-fields');
    fields.style.opacity = this.checked ? '1' : '0.4';
    fields.style.pointerEvents = this.checked ? '' : 'none';
});
</script>
@endpush

@endsection
