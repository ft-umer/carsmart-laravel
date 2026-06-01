{{-- resources/views/deals/partials/_comms_tab.blade.php --}}
<div class="card border border-border rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between gap-3">
        <h3 class="text-sm font-semibold">Communications</h3>
        <div class="flex gap-2">
            <select id="comms-template-select" class="kt-input kt-input-sm text-xs">
                <option value="">Quick template…</option>
                <option value="collection_booked">Collection booked</option>
                <option value="handover_reminder">Handover reminder</option>
                <option value="docs_required">Documents required</option>
                <option value="payout_approved">Payout approved</option>
            </select>
            <button id="btn-send-comms" class="kt-btn kt-btn-mono kt-btn-sm">Send</button>
        </div>
    </div>

    {{-- Message thread --}}
    <div class="flex flex-col h-[400px]">
        <div class="flex-1 overflow-auto p-4 space-y-3" id="comms-thread">
            @forelse ($deal['communications'] ?? [] as $msg)
                <div class="flex gap-3 {{ ($msg['direction'] ?? 'inbound') === 'outbound' ? 'flex-row-reverse' : '' }}">
                    <div class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold
                                flex items-center justify-center shrink-0">
                        {{ substr($msg['from'] ?? 'S', 0, 1) }}
                    </div>
                    <div class="max-w-[80%]">
                        <div class="flex items-center gap-2 mb-1
                                    {{ ($msg['direction'] ?? '') === 'outbound' ? 'flex-row-reverse' : '' }}">
                            <span class="text-xs font-medium">{{ $msg['from'] ?? '—' }}</span>
                            <span class="text-xs text-muted-foreground">{{ $msg['sent_at'] ?? '' }}</span>
                            @if ($msg['channel'] ?? null)
                                <span class="kt-badge kt-badge-outline kt-badge-sm">{{ $msg['channel'] }}</span>
                            @endif
                        </div>
                        <div class="px-3 py-2 rounded-xl text-sm
                                    {{ ($msg['direction'] ?? '') === 'outbound'
                                        ? 'bg-primary text-primary-foreground rounded-tr-sm'
                                        : 'bg-muted/40 rounded-tl-sm' }}">
                            {{ $msg['body'] ?? '' }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-xs text-muted-foreground py-8">No messages yet.</div>
            @endforelse
        </div>

        {{-- Compose --}}
        <div class="border-t border-border p-3 flex gap-2 items-end bg-muted/10">
            <textarea id="comms-message" class="kt-input flex-1 text-sm resize-none"
                      rows="2" placeholder="Type a message…"></textarea>
            <div class="flex flex-col gap-1.5">
                <select id="comms-channel" class="kt-input text-xs">
                    <option>Email</option>
                    <option>SMS</option>
                    <option>WhatsApp</option>
                </select>
                <button id="btn-send-message" class="kt-btn kt-btn-mono kt-btn-sm">
                    <i data-lucide="send" class="w-3.5 h-3.5 mr-1"></i>Send
                </button>
            </div>
        </div>
    </div>
</div>
