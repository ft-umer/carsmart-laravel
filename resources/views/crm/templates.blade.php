{{-- resources/views/crm/templates.blade.php --}}
{{-- Phase 3 — C8: Templates (Email / SMS / WhatsApp) --}}
@extends('layouts.app')
@section('title', 'Message Templates — Carsmart')

@section('content')
<div class="kt-container-fixed">

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h1 class="text-xl font-semibold">Message Templates</h1>
        <p class="text-sm text-muted-foreground mt-0.5">Reusable message templates with variables and approvals.</p>
    </div>
    <button onclick="openEditor()" class="kt-btn kt-btn-primary kt-btn-sm">
        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> New template
    </button>
</div>

{{-- Filters --}}
<div class="card border border-border rounded-xl p-4 mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <input type="text" placeholder="Search templates…" class="kt-input w-full kt-input-sm">
        </div>
        <div>
            <select class="kt-select kt-select-sm">
                <option>All channels</option><option>Email</option><option>SMS</option><option>WhatsApp</option>
            </select>
        </div>
        <div>
            <select class="kt-select kt-select-sm">
                <option>All statuses</option><option>Draft</option><option>Pending approval</option><option>Approved</option><option>Archived</option>
            </select>
        </div>
    </div>
</div>

{{-- Table --}}
@php
$templates = [
    ['id'=>1,'name'=>'Welcome — Lead received','channel'=>'Email','folder'=>'Leads','status'=>'Approved','owner'=>'SR','updated'=>'2 days ago'],
    ['id'=>2,'name'=>'Valuation ready','channel'=>'Email','folder'=>'Leads','status'=>'Approved','owner'=>'SR','updated'=>'1 week ago'],
    ['id'=>3,'name'=>'Auction invite','channel'=>'Email','folder'=>'Auctions','status'=>'Approved','owner'=>'AM','updated'=>'2 weeks ago'],
    ['id'=>4,'name'=>'Outbid notification','channel'=>'WhatsApp','folder'=>'Auctions','status'=>'Pending approval','owner'=>'AM','updated'=>'3 days ago'],
    ['id'=>5,'name'=>'Handover confirmation','channel'=>'SMS','folder'=>'Logistics','status'=>'Approved','owner'=>'JR','updated'=>'5 days ago'],
    ['id'=>6,'name'=>'Bulk promo broadcast','channel'=>'Email','folder'=>'Marketing','status'=>'Draft','owner'=>'AM','updated'=>'1 day ago'],
];
@endphp

<div class="card border border-border rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/30">
                <tr class="border-b border-border text-muted-foreground text-xs">
                    <th class="text-left px-4 py-3">Template</th>
                    <th class="text-left px-4 py-3">Channel</th>
                    <th class="text-left px-4 py-3">Folder</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Owner</th>
                    <th class="text-left px-4 py-3">Last updated</th>
                    <th class="text-left px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($templates as $t)
                    @php
                    $sCls = match($t['status']){
                        'Approved' => 'kt-badge-success',
                        'Pending approval' => 'kt-badge-warning',
                        'Draft' => 'kt-badge-outline',
                        default => 'kt-badge-outline'
                    };
                    $chIcon = match($t['channel']){ 'Email' => 'mail', 'SMS' => 'message-square', 'WhatsApp' => 'message-circle', default=>'message-square' };
                    @endphp
                    <tr class="border-b border-border/50 hover:bg-muted/20">
                        <td class="px-4 py-3 font-medium">{{ $t['name'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <i data-lucide="{{ $chIcon }}" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                <span>{{ $t['channel'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">{{ $t['folder'] }}</td>
                        <td class="px-4 py-3"><span class="kt-badge {{ $sCls }} kt-badge-sm">{{ $t['status'] }}</span></td>
                        <td class="px-4 py-3">
                            <span class="w-6 h-6 rounded-full bg-muted inline-flex items-center justify-center text-xs font-medium">{{ $t['owner'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground text-xs">{{ $t['updated'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1">
                                <button onclick="openEditor({{ $t['id'] }})" class="kt-btn kt-btn-ghost kt-btn-xs">Edit</button>
                                @if ($t['status'] === 'Draft')
                                    <button class="kt-btn kt-btn-ghost kt-btn-xs text-primary">Submit</button>
                                @elseif ($t['status'] === 'Pending approval')
                                    <button class="kt-btn kt-btn-ghost kt-btn-xs text-success">Approve</button>
                                @endif
                                <button class="kt-btn kt-btn-ghost kt-btn-xs text-muted-foreground">Archive</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div>

{{-- Template Editor Modal --}}
<div id="modal-template-editor" class="fixed inset-0 z-[10000] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditor()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-3xl border border-border rounded-xl overflow-hidden" style="max-height:90vh; overflow-y:auto;">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between sticky top-0 bg-card z-10">
                <h3 class="font-semibold">Template editor</h3>
                <button onclick="closeEditor()" class="kt-btn kt-btn-ghost kt-btn-sm">✕</button>
            </div>
            <div class="p-5 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Template name *</label>
                        <input type="text" class="kt-input w-full" placeholder="e.g. Welcome — Lead received">
                    </div>
                    <div>
                        <label class="form-label">Channel *</label>
                        <select class="kt-select w-full" id="tmpl-channel" onchange="toggleSubjectField()">
                            <option>Email</option><option>SMS</option><option>WhatsApp</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">Folder</label>
                    <select class="kt-select w-full">
                        <option>Leads</option><option>Auctions</option><option>Logistics</option><option>Deals</option><option>Marketing</option>
                    </select>
                </div>
                <div id="subject-field">
                    <label class="form-label">Subject (Email)</label>
                    <input type="text" class="kt-input w-full" placeholder="e.g. Your BMW 330i valuation is ready">
                </div>
                <div>
                    <label class="form-label">Variables available</label>
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach (['{{first_name}}','{{last_name}}','{{listing_number}}','{{auction_name}}','{{valuation_amount}}','{{vrm}}','{{deal_ref}}'] as $var)
                            <button onclick="insertVar('{{ $var }}')" class="kt-badge kt-badge-outline cursor-pointer hover:bg-muted text-xs">{{ $var }}</button>
                        @endforeach
                    </div>
                    <label class="form-label">Body *</label>
                    <textarea id="tmpl-body" rows="8" class="kt-textarea w-full font-mono text-sm"
                        placeholder="Dear {{first_name}},&#10;&#10;Your vehicle valuation for {{vrm}} is ready…"></textarea>
                </div>
                <div>
                    <label class="form-label">Attachments (optional)</label>
                    <div class="border-2 border-dashed border-border rounded-lg p-4 text-sm text-muted-foreground text-center">
                        Drop files or click to attach
                    </div>
                </div>
                <div class="rounded-lg bg-amber-500/10 border border-amber-500/20 p-3 text-sm">
                    <p class="font-medium text-amber-600 dark:text-amber-400">Approval required</p>
                    <p class="text-muted-foreground text-xs mt-0.5">Broadcast templates must be submitted for approval before use.</p>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-border flex gap-2 justify-end">
                <button onclick="closeEditor()" class="kt-btn kt-btn-outline">Cancel</button>
                <button class="kt-btn kt-btn-outline">Save draft</button>
                <button class="kt-btn kt-btn-primary">Submit for approval</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openEditor(id){ document.getElementById('modal-template-editor').classList.remove('hidden'); }
function closeEditor(){ document.getElementById('modal-template-editor').classList.add('hidden'); }
function insertVar(v){
    const ta = document.getElementById('tmpl-body');
    const start = ta.selectionStart; const end = ta.selectionEnd;
    ta.value = ta.value.substring(0,start) + v + ta.value.substring(end);
    ta.selectionStart = ta.selectionEnd = start + v.length;
    ta.focus();
}
function toggleSubjectField(){
    const show = document.getElementById('tmpl-channel').value === 'Email';
    document.getElementById('subject-field').style.display = show ? 'block' : 'none';
}
</script>
@endpush
@endsection
