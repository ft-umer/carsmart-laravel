{{-- resources/views/crm/consent.blade.php --}}
{{-- Phase 3 — C10: Consent & Privacy --}}
@extends('layouts.app')
@section('title', 'Consent & Privacy — Carsmart')

@section('content')
<div class="kt-container-fixed">

@include('partials._retention_banner')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h1 class="text-xl font-semibold">Consent & Privacy</h1>
        <p class="text-sm text-muted-foreground mt-0.5">Enforce channel-level consent, Do-Not-Contact, and data retention policies.</p>
    </div>
</div>

{{-- Tabs --}}
<div class="flex gap-1 mb-5 border-b border-border overflow-x-auto">
    @foreach (['overview' => 'Overview', 'policies' => 'Consent policies', 'dnc' => 'Do-Not-Contact list', 'retention' => 'Data retention', 'audit' => 'Audit log'] as $key => $label)
        <button onclick="switchTab('{{ $key }}')" id="tab-btn-{{ $key }}"
            class="tab-btn px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 -mb-px transition-colors
                   {{ $key === 'overview' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground' }}">
            {{ $label }}
        </button>
    @endforeach
</div>

{{-- OVERVIEW TAB --}}
<div id="tab-overview" class="tab-pane space-y-5">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ([['Email consent','1,248','text-success'],['SMS consent','892','text-success'],['WhatsApp consent','634','text-success'],['Do-Not-Contact','23','text-destructive']] as [$label,$count,$cls])
            <div class="card border border-border rounded-xl p-4 text-center">
                <p class="text-xs text-muted-foreground mb-1">{{ $label }}</p>
                <p class="text-2xl font-bold {{ $cls }}">{{ $count }}</p>
                <p class="text-xs text-muted-foreground mt-0.5">active records</p>
            </div>
        @endforeach
    </div>

    <div class="card border border-border rounded-xl p-5">
        <h3 class="font-semibold mb-4">Consent by channel</h3>
        <div class="space-y-4">
            @foreach ([['Email','1,248','1,856','67%'],['SMS','892','1,856','48%'],['WhatsApp','634','1,856','34%']] as [$ch,$yes,$total,$pct])
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium">{{ $ch }}</span>
                        <span class="text-muted-foreground">{{ $yes }} / {{ $total }} ({{ $pct }})</span>
                    </div>
                    <div class="h-2 bg-muted rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full" style="width:{{ $pct }}"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Sample consent panel (inline) --}}
    <div class="card border border-border rounded-xl p-5">
        <h3 class="font-semibold mb-1">Consent panel — example (Jane Doe · CST-001)</h3>
        <p class="text-xs text-muted-foreground mb-4">This panel appears inline on Person, Lead, and Vendor records.</p>
        <div class="max-w-md space-y-3">
            @foreach (['Email' => true, 'SMS' => false, 'WhatsApp' => true] as $ch => $val)
                <div class="flex items-center justify-between py-2 border-b border-border/50">
                    <span class="text-sm font-medium">{{ $ch }}</span>
                    <div class="flex items-center gap-3">
                        <span class="kt-badge {{ $val ? 'kt-badge-success' : 'kt-badge-outline' }} kt-badge-sm">
                            {{ $val ? 'Yes' : 'No' }}
                        </span>
                        <button class="kt-btn kt-btn-ghost kt-btn-xs text-primary">Change</button>
                    </div>
                </div>
            @endforeach
            <div class="flex items-center justify-between py-2">
                <span class="text-sm font-medium text-destructive">Do-Not-Contact</span>
                <div class="flex items-center gap-3">
                    <span class="kt-badge kt-badge-outline kt-badge-sm">Off</span>
                    <button class="kt-btn kt-btn-ghost kt-btn-xs text-destructive">Enable DNC</button>
                </div>
            </div>
        </div>
        <div class="mt-4 rounded-lg bg-muted/30 p-3 text-xs text-muted-foreground">
            <p class="font-medium text-foreground mb-1">Audit trail</p>
            <p>Email → Yes · changed by AM · 2 Oct 2025 14:32 · Reason: Customer opted in via website form</p>
        </div>
    </div>
</div>

{{-- POLICIES TAB --}}
<div id="tab-policies" class="tab-pane hidden space-y-5">
    <div class="card border border-border rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold">Consent policies</h3>
            <button class="kt-btn kt-btn-outline kt-btn-sm">Edit policies</button>
        </div>
        <div class="space-y-4 text-sm">
            @foreach ([
                ['Outbound email','Requires email consent = Yes','Enforced'],
                ['Outbound SMS','Requires SMS consent = Yes','Enforced'],
                ['Outbound WhatsApp','Requires WhatsApp consent = Yes','Enforced'],
                ['Broadcast templates','Requires approval before send','Enforced'],
                ['Do-Not-Contact','Blocks all channels immediately','Enforced'],
                ['Quiet hours','No outbound 21:00 – 08:00 (configurable)','Enforced'],
                ['Daily cap','Max 3 messages per recipient per day','Enforced'],
            ] as [$rule,$desc,$state])
                <div class="flex items-start justify-between py-2 border-b border-border/50 last:border-0">
                    <div>
                        <p class="font-medium">{{ $rule }}</p>
                        <p class="text-xs text-muted-foreground mt-0.5">{{ $desc }}</p>
                    </div>
                    <span class="kt-badge kt-badge-success kt-badge-sm flex-shrink-0">{{ $state }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card border border-border rounded-xl p-5">
        <h3 class="font-semibold mb-4">Right-to-be-forgotten (RTBF)</h3>
        <p class="text-sm text-muted-foreground mb-4">Configure what data is redacted when a RTBF request is processed. Admin only.</p>
        <div class="space-y-2 text-sm mb-4">
            @foreach (['Full name → [REDACTED]','Email address → [REDACTED]','Phone number → [REDACTED]','Address → [REDACTED]','IP address → removed','Files → purged'] as $rule)
                <div class="flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4 text-success"></i>
                    <span>{{ $rule }}</span>
                </div>
            @endforeach
        </div>
        <div class="flex gap-2">
            <button class="kt-btn kt-btn-outline kt-btn-sm">Edit redaction map</button>
            <button class="kt-btn kt-btn-ghost kt-btn-sm text-muted-foreground">Run test on sample</button>
        </div>
    </div>
</div>

{{-- DNC LIST TAB --}}
<div id="tab-dnc" class="tab-pane hidden">
    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-border flex items-center justify-between">
            <h3 class="font-semibold text-sm">Do-Not-Contact list (23 records)</h3>
            <input type="text" placeholder="Search…" class="kt-input kt-input-sm w-56">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-muted/30">
                    <tr class="border-b border-border text-xs text-muted-foreground">
                        <th class="text-left px-4 py-3">Person / Company</th>
                        <th class="text-left px-4 py-3">Type</th>
                        <th class="text-left px-4 py-3">Set by</th>
                        <th class="text-left px-4 py-3">Date</th>
                        <th class="text-left px-4 py-3">Reason</th>
                        <th class="text-left px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ([
                        ['Maria Santos','Customer','AM','3 Oct 2025','Requested removal from all comms'],
                        ['Quick Sales Ltd','Vendor','SR','28 Sep 2025','Legal dispute pending'],
                        ['Robert Green','Lead','JR','1 Oct 2025','Unsubscribed via email link'],
                    ] as $r)
                        <tr class="border-b border-border/50 hover:bg-muted/20">
                            <td class="px-4 py-3 font-medium">{{ $r[0] }}</td>
                            <td class="px-4 py-3 text-muted-foreground">{{ $r[1] }}</td>
                            <td class="px-4 py-3">
                                <span class="w-6 h-6 rounded-full bg-muted inline-flex items-center justify-center text-xs font-medium">{{ $r[2] }}</span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground text-xs">{{ $r[3] }}</td>
                            <td class="px-4 py-3 text-xs text-muted-foreground max-w-xs truncate">{{ $r[4] }}</td>
                            <td class="px-4 py-3">
                                <button onclick="openModal('modal-remove-dnc')" class="kt-btn kt-btn-ghost kt-btn-xs text-muted-foreground">Remove DNC</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- RETENTION TAB --}}
<div id="tab-retention" class="tab-pane hidden space-y-5">
    <div class="card border border-border rounded-xl p-5">
        <h3 class="font-semibold mb-4">Data retention settings</h3>
        <div class="space-y-4 text-sm">
            <div class="flex items-center justify-between py-2 border-b border-border/50">
                <div>
                    <p class="font-medium">Retention period</p>
                    <p class="text-xs text-muted-foreground">Records older than this are archived and hidden by default</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="number" value="12" class="kt-input w-16 text-center kt-input-sm">
                    <span class="text-muted-foreground">months</span>
                </div>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-border/50">
                <div>
                    <p class="font-medium">Include archived toggle (default)</p>
                    <p class="text-xs text-muted-foreground">Whether archived data is shown by default on index pages</p>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" class="kt-checkbox"> <span class="text-muted-foreground">Off by default</span>
                </label>
            </div>
            <div class="flex items-center justify-between py-2">
                <div>
                    <p class="font-medium">PII export masking</p>
                    <p class="text-xs text-muted-foreground">Non-privileged roles see masked email, phone, and address on exports</p>
                </div>
                <span class="kt-badge kt-badge-success kt-badge-sm">Enabled</span>
            </div>
        </div>
        <div class="flex justify-end mt-4 pt-4 border-t border-border">
            <button class="kt-btn kt-btn-primary kt-btn-sm">Save retention settings</button>
        </div>
    </div>

    <div class="card border border-border rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold">Archived records</h3>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" class="kt-checkbox" id="toggle-archived" onchange="toggleArchived(this)">
                Include archived
            </label>
        </div>
        <div id="archived-banner" class="hidden rounded-lg bg-warning/10 border border-warning/20 p-3 text-sm mb-3">
            <i data-lucide="archive" class="w-4 h-4 inline text-warning mr-1"></i>
            Showing archived records. These contain personal data subject to retention policy.
        </div>
        <p class="text-sm text-muted-foreground">Toggle "Include archived" to reveal records older than 12 months.</p>
    </div>
</div>

{{-- AUDIT LOG TAB --}}
<div id="tab-audit" class="tab-pane hidden">
    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-border flex flex-wrap gap-3 items-end">
            <div>
                <label class="form-label text-xs">Event type</label>
                <select class="kt-select kt-select-sm">
                    <option>All events</option>
                    <option>consent_updated</option>
                    <option>dnc_set</option>
                    <option>dnc_removed</option>
                    <option>rtbf_applied</option>
                    <option>export_performed</option>
                </select>
            </div>
            <div>
                <label class="form-label text-xs">Actor</label>
                <select class="kt-select kt-select-sm"><option>Anyone</option><option>Me</option></select>
            </div>
            <div>
                <label class="form-label text-xs">Date range</label>
                <input type="date" class="kt-input kt-input-sm">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-muted/30">
                    <tr class="border-b border-border text-xs text-muted-foreground">
                        <th class="text-left px-4 py-3">When</th>
                        <th class="text-left px-4 py-3">Actor</th>
                        <th class="text-left px-4 py-3">Event</th>
                        <th class="text-left px-4 py-3">Subject</th>
                        <th class="text-left px-4 py-3">Field</th>
                        <th class="text-left px-4 py-3">Before</th>
                        <th class="text-left px-4 py-3">After</th>
                        <th class="text-left px-4 py-3">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ([
                        ['2 Oct 2025 14:32','AM','consent_updated','Jane Doe (CST-001)','email','false','true','Customer opted in via website form'],
                        ['3 Oct 2025 10:15','AM','dnc_set','Maria Santos (CST-003)','dnc','false','true','Requested removal from all comms'],
                        ['1 Oct 2025 09:00','JR','consent_updated','Robert Green (LED-2088)','sms','true','false','Unsubscribed via link'],
                        ['28 Sep 2025 16:42','SR','dnc_set','Quick Sales Ltd (VEN-018)','dnc','false','true','Legal dispute pending'],
                    ] as $r)
                        <tr class="border-b border-border/50 hover:bg-muted/20 text-xs">
                            <td class="px-4 py-3 text-muted-foreground whitespace-nowrap">{{ $r[0] }}</td>
                            <td class="px-4 py-3">
                                <span class="w-6 h-6 rounded-full bg-muted inline-flex items-center justify-center text-xs font-medium">{{ $r[1] }}</span>
                            </td>
                            <td class="px-4 py-3"><code class="text-primary text-xs">{{ $r[2] }}</code></td>
                            <td class="px-4 py-3 font-medium">{{ $r[3] }}</td>
                            <td class="px-4 py-3 text-muted-foreground">{{ $r[4] }}</td>
                            <td class="px-4 py-3 text-muted-foreground">{{ $r[5] }}</td>
                            <td class="px-4 py-3 text-foreground">{{ $r[6] }}</td>
                            <td class="px-4 py-3 text-muted-foreground max-w-xs truncate">{{ $r[7] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-border flex justify-between text-sm text-muted-foreground">
            <span>4 events</span>
            <button class="kt-btn kt-btn-ghost kt-btn-xs">Export CSV</button>
        </div>
    </div>
</div>

{{-- Remove DNC modal --}}
<div id="modal-remove-dnc" class="fixed inset-0 z-[10000] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-remove-dnc')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-md border border-border rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="font-semibold">Remove from DNC list</h3>
                <button onclick="closeModal('modal-remove-dnc')" class="kt-btn kt-btn-ghost kt-btn-sm">✕</button>
            </div>
            <div class="p-5 text-sm space-y-4">
                <p class="text-muted-foreground">Removing from the DNC list will re-enable outbound contact subject to channel consent. A reason is required for the audit log.</p>
                <div>
                    <label class="form-label">Reason for removal *</label>
                    <textarea rows="3" class="kt-textarea w-full" placeholder="e.g. Customer contacted us to re-engage"></textarea>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-border flex justify-end gap-2">
                <button onclick="closeModal('modal-remove-dnc')" class="kt-btn kt-btn-outline">Cancel</button>
                <button class="kt-btn kt-btn-primary">Confirm removal</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(name){
    document.querySelectorAll('.tab-pane').forEach(p=>p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b=>{b.classList.remove('border-primary','text-primary');b.classList.add('border-transparent','text-muted-foreground');});
    document.getElementById('tab-'+name)?.classList.remove('hidden');
    const btn=document.getElementById('tab-btn-'+name);
    if(btn){btn.classList.add('border-primary','text-primary');btn.classList.remove('border-transparent','text-muted-foreground');}
}
function openModal(id){ document.getElementById(id)?.classList.remove('hidden'); }
function closeModal(id){ document.getElementById(id)?.classList.add('hidden'); }
function toggleArchived(cb){ document.getElementById('archived-banner').classList.toggle('hidden', !cb.checked); }
</script>
@endpush

@endsection
