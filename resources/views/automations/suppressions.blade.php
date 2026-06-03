{{-- resources/views/automations/suppressions.blade.php --}}
{{-- Phase 5 — Automations: Suppressions list --}}
@extends('layouts.app')
@section('title', 'Suppressions — Automations')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Suppressions</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Recipients excluded from automation sends and the reasons why</p>
        </div>
        <button class="kt-btn kt-btn-mono" id="btn-add-suppression">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Add suppression
        </button>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('automations.suppressions') }}" class="card border border-border rounded-lg p-3 mb-5">
        <div class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-muted-foreground mb-1">Search</label>
                <input type="search" name="search" value="{{ $search ?? '' }}" class="kt-input w-full" placeholder="Email or name…" />
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs text-muted-foreground mb-1">Reason</label>
                <select name="reason" class="kt-input w-full">
                    <option value="">All reasons</option>
                    @foreach(['DNC','Unsubscribed','Bounced','Channel consent No','Quiet hours','Rate limit','Manual'] as $r)
                        <option value="{{ $r }}" @selected(($reason ?? '') === $r)>{{ $r }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
            <a href="{{ route('automations.suppressions') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
        </div>
    </form>

    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-muted/40">
                    <tr>
                        @foreach(['Recipient','Channel','Reason','Source','Added','Expires','Actions'] as $col)
                            <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-background">
                    @forelse($suppressions ?? [] as $s)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="p-3">
                                <div class="font-medium text-foreground text-sm">{{ $s['name'] ?? '—' }}</div>
                                <div class="text-xs text-muted-foreground">{{ $s['email'] ?? $s['phone'] ?? '—' }}</div>
                            </td>
                            <td class="p-3">
                                <span class="kt-badge kt-badge-outline kt-badge-xs">{{ $s['channel'] }}</span>
                            </td>
                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ match($s['reason']) {
                                    'DNC','Bounced' => 'destructive',
                                    'Unsubscribed'  => 'warning',
                                    'Quiet hours','Rate limit' => 'secondary',
                                    default => 'secondary'
                                } }} kt-badge-sm">{{ $s['reason'] }}</span>
                            </td>
                            <td class="p-3 text-xs text-muted-foreground">{{ $s['source'] ?? 'System' }}</td>
                            <td class="p-3 text-xs text-muted-foreground">
                                {{ \Carbon\Carbon::parse($s['created_at'])->format('d M Y') }}
                            </td>
                            <td class="p-3 text-xs text-muted-foreground">
                                {{ isset($s['expires_at']) ? \Carbon\Carbon::parse($s['expires_at'])->format('d M Y') : 'Never' }}
                            </td>
                            <td class="p-3">
                                <button class="kt-btn kt-btn-ghost kt-btn-xs text-destructive suppression-remove"
                                        data-id="{{ $s['id'] }}">
                                    Remove
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-muted-foreground">
                                No suppressions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($suppressions) && method_exists($suppressions, 'links'))
            <div class="p-4 border-t border-border">{{ $suppressions->links() }}</div>
        @endif
    </div>
</div>

{{-- Add suppression modal --}}
<div id="modal-add-suppression" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">Add suppression</h2>
            <button class="suppression-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Email or phone</label>
                <input type="text" class="kt-input w-full" placeholder="user@example.com or +447…" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Channel</label>
                <select class="kt-input w-full">
                    <option>All channels</option>
                    <option>Email</option><option>SMS</option><option>WhatsApp</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Reason</label>
                <select class="kt-input w-full">
                    <option>DNC</option><option>Unsubscribed</option><option>Bounced</option><option>Manual</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Expires (optional)</label>
                <input type="date" class="kt-input w-full" />
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="suppression-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-mono">Add suppression</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btn-add-suppression')?.addEventListener('click', () => {
    document.getElementById('modal-add-suppression').classList.remove('hidden');
    document.getElementById('modal-add-suppression').classList.add('flex');
});
document.querySelectorAll('.suppression-close').forEach(b => b.addEventListener('click', () => {
    document.getElementById('modal-add-suppression').classList.add('hidden');
    document.getElementById('modal-add-suppression').classList.remove('flex');
}));
document.querySelectorAll('.suppression-remove').forEach(btn => {
    btn.addEventListener('click', () => {
        if(confirm('Remove this suppression?')) btn.closest('tr').remove();
    });
});
</script>
@endpush

@endsection
