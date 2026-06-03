{{-- resources/views/settings/index.blade.php --}}
{{-- Phase 5 — S0: Settings → Overview --}}
@extends('layouts.app')
@section('title', 'Settings — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Settings</span>
    </nav>

    <div class="mb-6">
        <h1 class="text-xl font-semibold text-foreground">Settings & Governance</h1>
        <p class="text-sm text-muted-foreground mt-0.5">Central configuration with audit and role-based access</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        @foreach([
            [
                'route'  => 'settings.rbac',
                'icon'   => 'users',
                'colour' => 'primary',
                'title'  => 'Users & Roles',
                'desc'   => 'Manage users, teams, and permissions matrix (RBAC)',
                'badge'  => null,
            ],
            [
                'route'  => 'settings.providers',
                'icon'   => 'mail',
                'colour' => 'info',
                'title'  => 'Providers & Channels',
                'desc'   => 'Email, SMS, WhatsApp providers and domain config',
                'badge'  => null,
            ],
            [
                'route'  => 'settings.identity',
                'icon'   => 'shield-check',
                'colour' => 'success',
                'title'  => 'Identity & Compliance',
                'desc'   => 'KYC / KYB provider, required documents, override policy',
                'badge'  => null,
            ],
            [
                'route'  => 'settings.auctions',
                'icon'   => 'gavel',
                'colour' => 'warning',
                'title'  => 'Auctions Reference',
                'desc'   => 'Bid increment bands and default sniper minutes',
                'badge'  => null,
            ],
            [
                'route'  => 'settings.payments',
                'icon'   => 'credit-card',
                'colour' => 'success',
                'title'  => 'Payments',
                'desc'   => 'PSP keys, webhook URLs, mandate text',
                'badge'  => null,
            ],
            [
                'route'  => 'settings.automations',
                'icon'   => 'zap',
                'colour' => 'primary',
                'title'  => 'Automations Policy',
                'desc'   => 'Quiet hours, daily caps, approval rules, valuation fetch limits',
                'badge'  => null,
            ],
            [
                'route'  => 'settings.privacy',
                'icon'   => 'lock',
                'colour' => 'destructive',
                'title'  => 'Consent & Privacy',
                'desc'   => 'Retention periods, export masking, right-to-be-forgotten',
                'badge'  => null,
            ],
            [
                'route'  => 'settings.branding',
                'icon'   => 'palette',
                'colour' => 'warning',
                'title'  => 'Branding',
                'desc'   => 'Logo, colours, typography tokens, button radius',
                'badge'  => null,
            ],
            [
                'route'  => 'settings.environment',
                'icon'   => 'terminal',
                'colour' => 'secondary',
                'title'  => 'Environment',
                'desc'   => 'Staging / sandbox keys, seed / reset tools, safe toggles',
                'badge'  => app()->environment('production') ? null : 'Staging',
            ],
        ] as $card)
            <a href="{{ route($card['route']) }}"
               class="kt-card hover:border-{{ $card['colour'] }}/40 transition-colors group">
                <div class="kt-card-content p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex items-center justify-center size-10 rounded-lg
                                     bg-{{ $card['colour'] }}/10 group-hover:bg-{{ $card['colour'] }}/20
                                     transition-colors shrink-0">
                            <i data-lucide="{{ $card['icon'] }}" class="w-5 h-5 text-{{ $card['colour'] }}"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-semibold text-foreground">{{ $card['title'] }}</span>
                                @if($card['badge'])
                                    <span class="kt-badge kt-badge-warning kt-badge-xs">{{ $card['badge'] }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-muted-foreground">{{ $card['desc'] }}</p>
                        </div>
                        <i data-lucide="arrow-right"
                           class="w-4 h-4 text-muted-foreground group-hover:text-{{ $card['colour'] }}
                                  transition-colors shrink-0 mt-0.5"></i>
                    </div>
                </div>
            </a>
        @endforeach

    </div>

    {{-- Audit log teaser --}}
    <div class="mt-8 card border border-border rounded-xl overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-border">
            <h2 class="text-sm font-semibold text-foreground">Recent audit log</h2>
            <a href="{{ route('settings.audit') }}" class="text-xs text-primary hover:underline">View full log →</a>
        </div>
        <div class="divide-y divide-border">
            @forelse($auditLog ?? [] as $entry)
                <div class="flex items-start gap-3 p-3 hover:bg-muted/20 transition-colors">
                    <span class="flex items-center justify-center size-7 rounded-full bg-muted shrink-0 mt-0.5">
                        <i data-lucide="activity" class="w-3.5 h-3.5 text-muted-foreground"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-foreground">
                            <span class="font-medium">{{ $entry['user'] }}</span>
                            {{ $entry['action'] }}
                        </p>
                        <p class="text-xs text-muted-foreground mt-0.5">{{ $entry['target'] }}</p>
                    </div>
                    <span class="text-xs text-muted-foreground whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($entry['time'])->diffForHumans() }}
                    </span>
                </div>
            @empty
                <div class="p-6 text-center text-sm text-muted-foreground">No recent activity.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection