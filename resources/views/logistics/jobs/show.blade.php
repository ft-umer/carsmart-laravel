{{-- resources/views/logistics/jobs/show.blade.php --}}
{{-- Phase 4 — L2/L3/L4: Job Detail + Handover Checklist + Transport Chat --}}
@extends('layouts.app')
@section('title', ($job['ref'] ?? 'Job') . ' — Logistics')

@section('content')

@php
    $jCls = match ($job['status'] ?? '') {
        'Scheduled'  => 'kt-badge-info',
        'In transit' => 'kt-badge-warning',
        'Delivered'  => 'kt-badge-success',
        'Issue'      => 'kt-badge-destructive',
        default      => 'kt-badge-outline',
    };
@endphp
<div class="kt-container-fixed">

<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
    <a href="{{ route('logistics.jobs.index') }}" class="hover:text-foreground">Jobs</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-foreground font-medium">{{ $job['ref'] ?? 'Job detail' }}</span>
</nav>

{{-- Header --}}
<div class="card border border-border rounded-xl px-5 py-4 mb-5">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 flex-wrap mb-1">
                <h1 class="text-xl font-semibold">{{ $job['ref'] ?? 'JOB-' . $job['id'] }}</h1>
                <span class="kt-badge {{ $jCls }}">{{ $job['status'] ?? 'Unknown' }}</span>
                @if ($job['deal_ref'] ?? null)
                    <a href="{{ route('deals.show', $job['deal_id']) }}"
                       class="kt-badge kt-badge-outline hover:kt-badge-primary transition-colors text-xs">
                        {{ $job['deal_ref'] }}
                    </a>
                @endif
            </div>
            <p class="text-sm text-muted-foreground">
                {{ $job['pickup_address'] ?? '—' }} → {{ $job['drop_address'] ?? '—' }}
                <span class="mx-2">·</span>
                {{ $job['slot'] ?? '—' }}
                <span class="mx-2">·</span>
                Provider: <strong class="text-foreground">{{ $job['provider'] ?? 'Unassigned' }}</strong>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if (in_array($job['status'] ?? '', ['Scheduled']))
                <button id="btn-mark-transit" class="kt-btn kt-btn-outline">
                    <i data-lucide="truck" class="w-4 h-4 mr-1"></i>Mark in transit
                </button>
            @endif
            @if (in_array($job['status'] ?? '', ['Scheduled','In transit']))
                <button id="btn-mark-delivered" class="kt-btn kt-btn-mono">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>Mark delivered
                </button>
            @endif
            <button id="btn-upload-proof" class="kt-btn kt-btn-outline">
                <i data-lucide="upload" class="w-4 h-4 mr-1"></i>Upload proof
            </button>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="border-b border-border mb-5 overflow-x-auto">
    <div class="flex gap-1 min-w-max px-1 pt-1">
        @foreach(['Overview','Checklist','Documents','Activity'] as $tab)
            <button data-job-tab="{{ Str::slug($tab) }}"
                    class="job-tab-btn kt-btn kt-btn-sm {{ $loop->first ? 'kt-btn-mono' : 'kt-btn-ghost' }}">
                {{ $tab }}
            </button>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-[1fr,360px] gap-5">

    {{-- Left: tab content --}}
    <div>

        {{-- ── Overview ─────────────────────────────────────────────────── --}}
        <div id="job-tab-overview" class="job-tab-content space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="card border border-border rounded-xl p-4 space-y-2">
                    <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Pickup</h4>
                    <div class="text-sm">{{ $job['pickup_address'] ?? '—' }}</div>
                    <div class="text-xs text-muted-foreground">{{ $job['pickup_contact'] ?? '' }}</div>
                </div>
                <div class="card border border-border rounded-xl p-4 space-y-2">
                    <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Drop</h4>
                    <div class="text-sm">{{ $job['drop_address'] ?? '—' }}</div>
                    <div class="text-xs text-muted-foreground">{{ $job['drop_contact'] ?? '' }}</div>
                </div>
                <div class="card border border-border rounded-xl p-4 space-y-2">
                    <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Slot &amp; tracking</h4>
                    <div class="text-sm font-medium">{{ $job['slot'] ?? '—' }}</div>
                    @if ($job['tracking_ref'] ?? null)
                        <div class="font-mono text-xs bg-muted px-2 py-1 rounded">{{ $job['tracking_ref'] }}</div>
                    @endif
                </div>
            </div>

            {{-- Vehicle --}}
            <div class="card border border-border rounded-xl p-4">
                <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">Vehicle</h4>
                <div class="flex items-center gap-4">
                    <div>
                        <div class="font-medium">{{ $job['vehicle_title'] ?? '—' }}</div>
                        @if ($job['vrm'] ?? null)
                            <span class="font-mono text-xs bg-muted px-2 py-0.5 rounded mt-1 inline-block">
                                {{ $job['vrm'] }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Checklist (L3: Collection & Handover) ────────────────────── --}}
        <div id="job-tab-checklist" class="job-tab-content hidden">
            @include('logistics.partials._handover_checklist', ['job' => $job])
        </div>

        {{-- ── Documents ────────────────────────────────────────────────── --}}
        <div id="job-tab-documents" class="job-tab-content hidden space-y-3">
            <div class="card border border-border rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between">
                    <h4 class="text-sm font-semibold">Proof of collection / delivery</h4>
                    <button id="btn-upload-doc-tab" class="kt-btn kt-btn-mono kt-btn-sm">
                        <i data-lucide="upload" class="w-3.5 h-3.5 mr-1"></i>Upload
                    </button>
                </div>
                <div class="p-4">
                    @if (!empty($job['documents']))
                        <div class="space-y-2">
                            @foreach ($job['documents'] as $doc)
                                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-border bg-muted/10">
                                    <i data-lucide="file-text" class="w-4 h-4 text-muted-foreground shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium truncate">{{ $doc['name'] }}</div>
                                        <div class="text-xs text-muted-foreground">
                                            {{ $doc['uploaded_at'] ?? '' }} · {{ $doc['uploaded_by'] ?? '' }}
                                        </div>
                                    </div>
                                    <a href="{{ $doc['url'] ?? '#' }}" target="_blank"
                                       class="kt-btn kt-btn-ghost kt-btn-sm shrink-0">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-sm text-muted-foreground">
                            <i data-lucide="folder-open" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                            No documents uploaded yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Activity ──────────────────────────────────────────────────── --}}
        <div id="job-tab-activity" class="job-tab-content hidden">
            @include('deals.partials._activity_tab', ['deal' => ['activity' => $job['activity'] ?? []]])
        </div>
    </div>

    {{-- ── Right: Transport Chat (L4) ─────────────────────────────────────── --}}
    <aside id="chat" class="card border border-border rounded-xl overflow-hidden flex flex-col
                            sticky top-[86px] h-[calc(100vh-120px)]">

        <div class="px-4 py-3 border-b border-border bg-muted/20 shrink-0 flex items-center justify-between">
            <div>
                <div class="text-sm font-semibold flex items-center gap-2">
                    <i data-lucide="message-circle" class="w-4 h-4 opacity-60"></i>
                    Transport chat
                </div>
                <div class="text-xs text-muted-foreground mt-0.5">
                    {{ $job['provider'] ?? 'Provider' }} · {{ $job['ref'] ?? '' }}
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span class="text-xs text-muted-foreground">Live</span>
            </div>
        </div>

        {{-- Quick templates --}}
        <div class="px-3 py-2 border-b border-border bg-muted/10 shrink-0">
            <div class="flex gap-1.5 overflow-x-auto pb-1 min-w-max">
                @foreach(['Running late','At pickup','At drop-off','Delay — road closure','Delivered successfully'] as $tpl)
                    <button data-tpl="{{ $tpl }}"
                            class="tpl-btn kt-btn kt-btn-outline kt-btn-sm whitespace-nowrap text-xs">
                        {{ $tpl }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-auto p-4 space-y-3" id="chat-thread">
            @forelse ($job['chat_messages'] ?? [] as $msg)
                <div class="flex gap-3 {{ ($msg['direction'] ?? '') === 'outbound' ? 'flex-row-reverse' : '' }}">
                    <div class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold
                                flex items-center justify-center shrink-0">
                        {{ substr($msg['from'] ?? 'T', 0, 1) }}
                    </div>
                    <div class="max-w-[80%] space-y-1">
                        <div class="flex items-center gap-2
                                    {{ ($msg['direction'] ?? '') === 'outbound' ? 'flex-row-reverse' : '' }}">
                            <span class="text-xs font-medium">{{ $msg['from'] ?? '—' }}</span>
                            <span class="text-xs text-muted-foreground">{{ $msg['sent_at'] ?? '' }}</span>
                        </div>
                        <div class="px-3 py-2 rounded-xl text-sm
                                    {{ ($msg['direction'] ?? '') === 'outbound'
                                        ? 'bg-primary text-primary-foreground rounded-tr-sm'
                                        : 'bg-muted/40 rounded-tl-sm' }}">
                            {{ $msg['body'] ?? '' }}
                        </div>
                        @if (!empty($msg['attachments']))
                            @foreach ($msg['attachments'] as $att)
                                <div class="flex items-center gap-2 px-2 py-1.5 rounded-lg border border-border
                                            bg-background text-xs hover:bg-muted/30 transition-colors">
                                    <i data-lucide="paperclip" class="w-3.5 h-3.5 opacity-60 shrink-0"></i>
                                    <span class="truncate flex-1">{{ $att['name'] ?? 'Attachment' }}</span>
                                    <span class="text-muted-foreground shrink-0">{{ $att['size'] ?? '' }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-xs text-muted-foreground py-6">
                    No messages yet. Start the conversation.
                </div>
            @endforelse
        </div>

        {{-- Compose --}}
        <div class="border-t border-border p-3 shrink-0 space-y-2 bg-muted/10">
            <textarea id="chat-input" class="kt-input w-full text-sm resize-none" rows="2"
                      placeholder="Type a message…"></textarea>
            <div class="flex items-center justify-between gap-2">
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="file" id="chat-attachment" class="hidden" accept="image/*,application/pdf"
                           onchange="handleChatAttachment(this)" />
                    <span class="kt-btn kt-btn-ghost kt-btn-sm px-2" onclick="document.getElementById('chat-attachment').click()">
                        <i data-lucide="paperclip" class="w-4 h-4"></i>
                    </span>
                    <span class="text-xs text-muted-foreground">Max 25 MB</span>
                </label>
                <button id="btn-send-chat" class="kt-btn kt-btn-mono kt-btn-sm">
                    <i data-lucide="send" class="w-3.5 h-3.5 mr-1"></i>Send
                </button>
            </div>
            <div id="attachment-preview" class="hidden text-xs text-muted-foreground flex items-center gap-2">
                <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                <span id="attachment-name"></span>
                <button onclick="clearAttachment()" class="text-destructive ml-auto">Remove</button>
            </div>
        </div>
    </aside>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>
@include('partials._phase4_js')
</div>

<script>
(function () {
    const { toast, auditEvent } = window.CS4;

    /* Tab switching */
    document.addEventListener('click', e => {
        const btn = e.target.closest('.job-tab-btn');
        if (!btn) return;
        const tab = btn.dataset.jobTab;
        document.querySelectorAll('.job-tab-btn').forEach(b => {
            b.classList.toggle('kt-btn-mono', b.dataset.jobTab === tab);
            b.classList.toggle('kt-btn-ghost', b.dataset.jobTab !== tab);
        });
        document.querySelectorAll('.job-tab-content').forEach(c =>
            c.classList.toggle('hidden', c.id !== 'job-tab-' + tab)
        );
    });

    /* Status actions */
    document.getElementById('btn-mark-transit')?.addEventListener('click', () => {
        auditEvent('job_in_transit', { id: '{{ $job["id"] }}' });
        toast('Job marked in transit.', 'success');
    });
    document.getElementById('btn-mark-delivered')?.addEventListener('click', () => {
        auditEvent('job_delivered', { id: '{{ $job["id"] }}' });
        toast('Job delivered. Complete the handover checklist.', 'success');
        document.querySelector('[data-job-tab="checklist"]')?.click();
    });
    document.getElementById('btn-upload-proof')?.addEventListener('click', () =>
        document.querySelector('[data-job-tab="documents"]')?.click()
    );
    document.getElementById('btn-upload-doc-tab')?.addEventListener('click', () =>
        toast('File upload UI — wire to a real file input in production.', 'info')
    );

    /* Templates */
    document.addEventListener('click', e => {
        const btn = e.target.closest('.tpl-btn');
        if (!btn) return;
        const input = document.getElementById('chat-input');
        if (input) input.value = btn.dataset.tpl;
        input?.focus();
    });

    /* Send chat */
    document.getElementById('btn-send-chat')?.addEventListener('click', () => {
        const input = document.getElementById('chat-input');
        const body  = input?.value?.trim();
        if (!body) { toast('Type a message first.', 'warning'); return; }
        const thread = document.getElementById('chat-thread');
        const msg = document.createElement('div');
        msg.className = 'flex gap-3 flex-row-reverse';
        msg.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-primary/10 text-primary text-xs font-bold
                        flex items-center justify-center shrink-0">Me</div>
            <div class="max-w-[80%]">
                <div class="flex items-center gap-2 flex-row-reverse mb-1">
                    <span class="text-xs font-medium">You</span>
                    <span class="text-xs text-muted-foreground">just now</span>
                </div>
                <div class="px-3 py-2 rounded-xl rounded-tr-sm text-sm bg-primary text-primary-foreground">
                    ${body}
                </div>
            </div>`;
        thread?.appendChild(msg);
        thread?.scrollTo(0, thread.scrollHeight);
        if (input) input.value = '';
        clearAttachment();
        auditEvent('transport_chat_message_sent', { job: '{{ $job["id"] }}' });
    });

    /* Attachment */
    window.handleChatAttachment = function (input) {
        const file = input.files[0];
        if (!file) return;
        if (file.size > 25 * 1024 * 1024) { window.CS4.toast('File exceeds 25 MB limit.', 'error'); return; }
        document.getElementById('attachment-preview').classList.remove('hidden');
        document.getElementById('attachment-name').textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
    };
    window.clearAttachment = function () {
        document.getElementById('attachment-preview').classList.add('hidden');
        document.getElementById('chat-attachment').value = '';
    };
})();
</script>

@endsection
