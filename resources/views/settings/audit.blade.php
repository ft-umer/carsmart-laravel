{{-- resources/views/settings/audit.blade.php --}}
{{-- Phase 5 — S0b: Settings → Audit Log --}}
@extends('layouts.app')
@section('title', 'Audit Log — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">
 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Audit Log</span>
    </nav>

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Audit Log</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Full history of configuration and user management changes</p>
        </div>
    </div>

    {{-- Log table --}}
    <div class="card border border-border rounded-xl overflow-hidden">

        <div class="flex items-center justify-between px-4 py-3 border-b border-border bg-muted/30">
            <span class="text-xs text-muted-foreground">
                Showing {{ $log->count() }} entries
            </span>
            <span class="text-xs text-muted-foreground">All times in UTC</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/20">
                        <th class="p-3 text-left text-xs font-medium text-muted-foreground">User</th>
                        <th class="p-3 text-left text-xs font-medium text-muted-foreground">Action</th>
                        <th class="p-3 text-left text-xs font-medium text-muted-foreground">Target</th>
                        <th class="p-3 text-left text-xs font-medium text-muted-foreground whitespace-nowrap">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($log ?? [] as $entry)
                        <tr class="hover:bg-muted/20 transition-colors">
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center justify-center size-7 rounded-full bg-muted shrink-0 text-xs font-semibold text-muted-foreground">
                                        {{ strtoupper(substr($entry['user'], 0, 2)) }}
                                    </span>
                                    <span class="font-medium text-foreground whitespace-nowrap">{{ $entry['user'] }}</span>
                                </div>
                            </td>
                            <td class="p-3 text-foreground">{{ $entry['action'] }}</td>
                            <td class="p-3">
                                <span class="kt-badge kt-badge-outline kt-badge-xs font-mono">{{ $entry['target'] }}</span>
                            </td>
                            <td class="p-3 text-muted-foreground whitespace-nowrap" title="{{ \Carbon\Carbon::parse($entry['time'])->toDateTimeString() }}">
                                {{ \Carbon\Carbon::parse($entry['time'])->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-muted-foreground">No audit entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($log) && method_exists($log, 'links'))
            <div class="p-4 border-t border-border">{{ $log->links() }}</div>
        @endif

    </div>

</div>
@endsection