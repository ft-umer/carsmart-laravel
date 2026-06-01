{{-- resources/views/deals/partials/_history_tab.blade.php --}}
{{-- G2: Audit log for every state change, approval, and financial posting on this deal --}}

<div class="card border border-border rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between">
        <h3 class="text-sm font-semibold">Audit history</h3>
        <button class="kt-btn kt-btn-outline kt-btn-sm" onclick="window.CS4.toast('Audit export queued.','info')">
            <i data-lucide="download" class="w-3.5 h-3.5 mr-1"></i>Export
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-muted/40">
                <tr>
                    <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">When</th>
                    <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">Event</th>
                    <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">Field / Entity</th>
                    <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">Old value</th>
                    <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">New value</th>
                    <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">By</th>
                    <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-background">
                @forelse (array_reverse($deal['audit_log'] ?? []) as $log)
                    <tr class="hover:bg-muted/20 transition-colors">
                        <td class="p-3 whitespace-nowrap text-muted-foreground">{{ $log['timestamp'] ?? '—' }}</td>
                        <td class="p-3 font-mono">{{ $log['event'] ?? '—' }}</td>
                        <td class="p-3">{{ $log['field'] ?? '—' }}</td>
                        <td class="p-3 text-muted-foreground">{{ $log['old_value'] ?? '—' }}</td>
                        <td class="p-3 font-medium">{{ $log['new_value'] ?? '—' }}</td>
                        <td class="p-3">{{ $log['by'] ?? '—' }}</td>
                        <td class="p-3 text-muted-foreground font-mono">{{ $log['ip'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-muted-foreground">
                            No audit entries.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
