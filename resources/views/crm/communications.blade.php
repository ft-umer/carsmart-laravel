{{-- resources/views/crm/communications.blade.php --}}
{{-- Phase 3 — C6: Communications — Compose & Threads --}}
@extends('layouts.app')
@section('title', 'Communications — Carsmart')

@section('content')
<div class="kt-container-fixed">

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h1 class="text-xl font-semibold">Communications</h1>
        <p class="text-sm text-muted-foreground mt-0.5">Send and track messages across email, SMS, and WhatsApp.</p>
    </div>
    <button onclick="openCompose()" class="kt-btn kt-btn-primary kt-btn-sm">
        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Compose
    </button>
</div>

{{-- Compose panel (slide-in or expanded) --}}
<div id="compose-panel" class="hidden card border border-border rounded-xl p-5 mb-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold">New message</h3>
        <button onclick="closeCompose()" class="kt-btn kt-btn-ghost kt-btn-sm">✕</button>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="space-y-4">
            <div>
                <label class="form-label">To *</label>
                <input type="text" class="kt-input w-full" placeholder="Search person or company…">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Channel *</label>
                    <select class="kt-select w-full" id="compose-channel" onchange="toggleSubject()">
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Template</label>
                    <select class="kt-select w-full">
                        <option value="">None</option>
                        <option>Welcome — Lead received</option>
                        <option>Valuation ready</option>
                        <option>Listing published</option>
                        <option>Auction invite</option>
                    </select>
                </div>
            </div>
            <div id="subject-row">
                <label class="form-label">Subject</label>
                <input type="text" id="compose-subject" class="kt-input w-full" placeholder="Email subject…">
            </div>
            <div>
                <label class="form-label">Message *</label>
                <textarea rows="5" class="kt-textarea w-full" placeholder="Type your message… Use {{first_name}}, {{listing_number}} etc."></textarea>
                <p class="text-xs text-muted-foreground mt-1">Available variables: <code>{{first_name}}</code> <code>{{listing_number}}</code> <code>{{auction_name}}</code></p>
            </div>
            <div>
                <label class="form-label">Attachments</label>
                <div class="border-2 border-dashed border-border rounded-lg p-3 text-center text-sm text-muted-foreground cursor-pointer hover:bg-muted/20">
                    Drop files here or click to upload (max 25 MB)
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="rounded-lg bg-muted/30 p-4">
                <h4 class="font-medium text-sm mb-3">Message preview</h4>
                <div class="rounded-lg bg-card border border-border p-3 text-sm min-h-[120px]">
                    <p class="text-muted-foreground">Preview will appear here as you type…</p>
                </div>
            </div>
            <div class="rounded-lg border border-border p-4 text-sm">
                <h4 class="font-medium mb-2">Consent & delivery</h4>
                <div class="space-y-1.5 text-muted-foreground">
                    <p><i data-lucide="check-circle" class="w-3.5 h-3.5 inline text-success mr-1"></i>Consent: Email ✔</p>
                    <p><i data-lucide="check-circle" class="w-3.5 h-3.5 inline text-success mr-1"></i>Not in quiet hours</p>
                    <p><i data-lucide="check-circle" class="w-3.5 h-3.5 inline text-success mr-1"></i>DNC: Off</p>
                </div>
            </div>
        </div>
    </div>
    <div class="flex justify-end gap-2 mt-5 pt-5 border-t border-border">
        <button onclick="closeCompose()" class="kt-btn kt-btn-outline">Cancel</button>
        <button class="kt-btn kt-btn-outline">
            <i data-lucide="clock" class="w-4 h-4 mr-1"></i> Schedule
        </button>
        <button onclick="openModal('modal-schedule-send')" class="kt-btn kt-btn-outline">
            <i data-lucide="calendar" class="w-4 h-4 mr-1"></i> Schedule send
        </button>
        <button class="kt-btn kt-btn-primary">
            <i data-lucide="send" class="w-4 h-4 mr-1"></i> Send
        </button>
    </div>
</div>

{{-- Thread list + detail --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: Thread list --}}
    <div class="lg:col-span-1 card border border-border rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-border">
            <input type="text" placeholder="Search threads…" class="kt-input w-full kt-input-sm">
        </div>
        <div class="divide-y divide-border overflow-y-auto max-h-[600px]">
            @php
            $threads = [
                ['id'=>1,'name'=>'John Smith','channel'=>'email','preview'=>'Thanks for getting back to me…','time'=>'2h ago','unread'=>2,'resolved'=>false],
                ['id'=>2,'name'=>'Fast Cars Ltd','channel'=>'whatsapp','preview'=>'We can pick up on Wednesday…','time'=>'Yesterday','unread'=>0,'resolved'=>false],
                ['id'=>3,'name'=>'Jane Doe','channel'=>'sms','preview'=>'Confirmed, see you then!','time'=>'2 days ago','unread'=>0,'resolved'=>true],
                ['id'=>4,'name'=>'David Hughes','channel'=>'email','preview'=>'Please resend the documents','time'=>'3 days ago','unread'=>1,'resolved'=>false],
            ];
            @endphp
            @foreach ($threads as $t)
                <div onclick="openThread({{ $t['id'] }})"
                    class="px-4 py-3 cursor-pointer hover:bg-muted/20 {{ $t['id'] === 1 ? 'bg-primary/5' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-7 h-7 rounded-full bg-primary/20 flex-shrink-0 flex items-center justify-center text-xs font-medium text-primary">{{ substr($t['name'],0,1) }}</div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="font-medium text-sm truncate">{{ $t['name'] }}</p>
                                    <span class="text-xs text-muted-foreground">·</span>
                                    <span class="text-xs text-muted-foreground uppercase">{{ $t['channel'] }}</span>
                                </div>
                                <p class="text-xs text-muted-foreground truncate">{{ $t['preview'] }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="text-xs text-muted-foreground">{{ $t['time'] }}</span>
                            @if ($t['unread'] > 0)
                                <span class="w-5 h-5 rounded-full bg-primary text-primary-foreground text-xs flex items-center justify-center">{{ $t['unread'] }}</span>
                            @endif
                            @if ($t['resolved'])
                                <span class="kt-badge kt-badge-success kt-badge-sm">Resolved</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Right: Selected thread --}}
    <div class="lg:col-span-2 card border border-border rounded-xl overflow-hidden flex flex-col" style="max-height: 680px;">
        {{-- Thread header --}}
        <div class="px-5 py-3 border-b border-border flex items-center justify-between">
            <div>
                <p class="font-semibold text-sm">John Smith</p>
                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                    <span class="kt-badge kt-badge-outline kt-badge-sm">Email</span>
                    <span>Lead LED-2041</span>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="kt-btn kt-btn-outline kt-btn-xs">Mark resolved</button>
                <a href="{{ route('leads.show', 'LED-2041') }}" class="kt-btn kt-btn-ghost kt-btn-xs">Open lead</a>
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <div class="flex gap-3">
                <div class="w-7 h-7 rounded-full bg-muted flex-shrink-0 flex items-center justify-center text-xs font-medium">J</div>
                <div class="max-w-[80%]">
                    <div class="rounded-lg bg-muted/40 px-3 py-2 text-sm">
                        <p>Hi, I'm interested in getting a valuation for my BMW 330i plate AB19 CDE. Could you help?</p>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">10 Oct 14:32 · Email · Read ✔</p>
                </div>
            </div>
            <div class="flex gap-3 flex-row-reverse">
                <div class="w-7 h-7 rounded-full bg-primary/20 flex-shrink-0 flex items-center justify-center text-xs font-medium text-primary">SR</div>
                <div class="max-w-[80%]">
                    <div class="rounded-lg bg-primary/10 border border-primary/20 px-3 py-2 text-sm">
                        <p>Hi John! Absolutely — we've started a valuation on your BMW. I'll have figures ready shortly.</p>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1 text-right">11 Oct 09:15 · Email · Delivered ✔✔</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="w-7 h-7 rounded-full bg-muted flex-shrink-0 flex items-center justify-center text-xs font-medium">J</div>
                <div class="max-w-[80%]">
                    <div class="rounded-lg bg-muted/40 px-3 py-2 text-sm">
                        <p>Thanks for getting back to me, looking forward to the figures!</p>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">11 Oct 10:04 · Email · Read ✔</p>
                </div>
            </div>
        </div>

        {{-- Reply composer --}}
        <div class="border-t border-border p-3">
            <div class="flex gap-2 mb-2">
                <select class="kt-select kt-select-sm text-xs" style="width:auto">
                    <option>Email</option><option>SMS</option><option>WhatsApp</option>
                </select>
                <select class="kt-select kt-select-sm text-xs" style="width:auto">
                    <option value="">Template…</option>
                    <option>Valuation ready</option>
                    <option>Follow up</option>
                </select>
            </div>
            <div class="flex gap-2 items-end">
                <textarea rows="2" placeholder="Type a reply…" class="kt-textarea flex-1 text-sm resize-none"></textarea>
                <div class="flex flex-col gap-1">
                    <button class="kt-btn kt-btn-ghost kt-btn-xs" title="Attachment">
                        <i data-lucide="paperclip" class="w-4 h-4"></i>
                    </button>
                    <button class="kt-btn kt-btn-primary kt-btn-sm">Send</button>
                </div>
            </div>
        </div>
    </div>

</div>

</div>

@push('scripts')
<script>
function openCompose(){ document.getElementById('compose-panel').classList.remove('hidden'); }
function closeCompose(){ document.getElementById('compose-panel').classList.add('hidden'); }
function openModal(id){ document.getElementById(id)?.classList.remove('hidden'); }
function openThread(id){ /* In production: load thread via AJAX */ }
function toggleSubject(){
    const ch = document.getElementById('compose-channel').value;
    const row = document.getElementById('subject-row');
    row.style.display = ch === 'email' ? 'block' : 'none';
}
</script>
@endpush
@endsection
