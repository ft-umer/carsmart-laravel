{{-- resources/views/crm/vendors.blade.php --}}
{{-- Phase 3 — C5: Vendors (Companies) — Browse & Detail --}}
@extends('layouts.app')
@section('title', 'Vendors — Carsmart')

@section('content')
<div class="kt-container-fixed">

@include('partials._retention_banner')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h1 class="text-xl font-semibold">Vendors (Companies)</h1>
        <p class="text-sm text-muted-foreground mt-0.5">Manage companies who buy, bid, and list; track compliance and participation.</p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('modal-add-vendor')" class="kt-btn kt-btn-primary kt-btn-sm">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Add vendor
        </button>
    </div>
</div>

{{-- Filters --}}
<div class="card border border-border rounded-xl p-4 mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="form-label text-xs">Search</label>
            <input type="text" placeholder="Company, name, email, phone…" class="kt-input w-full kt-input-sm">
        </div>
        <div>
            <label class="form-label text-xs">KYB status</label>
            <select class="kt-select kt-select-sm">
                <option>Any</option><option>Required</option><option>Pending</option><option>Verified</option><option>Failed</option>
            </select>
        </div>
        <div>
            <label class="form-label text-xs">Card on file</label>
            <select class="kt-select kt-select-sm"><option>Any</option><option>Yes</option><option>No</option></select>
        </div>
        <div>
            <label class="form-label text-xs">Tags</label>
            <select class="kt-select kt-select-sm"><option>Any</option></select>
        </div>
        <div>
            <label class="form-label text-xs">Owner</label>
            <select class="kt-select kt-select-sm"><option>Any</option><option>Me</option></select>
        </div>
        <button class="kt-btn kt-btn-primary kt-btn-sm">Apply</button>
        <button class="kt-btn kt-btn-ghost kt-btn-sm text-muted-foreground">Reset</button>
    </div>
</div>

{{-- Table --}}
@php
$vendors = [
    ['id'=>'VEN-001','name'=>'Fast Cars Ltd','kyb'=>'Verified','card'=>true,'listings'=>12,'bids'=>48,'purchases'=>5,'wallet'=>'Clear','last_activity'=>'1 day ago','owner'=>'JR'],
    ['id'=>'VEN-002','name'=>'Prime Auto Group','kyb'=>'Pending','card'=>false,'listings'=>3,'bids'=>7,'purchases'=>0,'wallet'=>'Hold','last_activity'=>'3 days ago','owner'=>'AM'],
    ['id'=>'VEN-003','name'=>'City Dealers Ltd','kyb'=>'Required','card'=>false,'listings'=>0,'bids'=>0,'purchases'=>0,'wallet'=>'—','last_activity'=>'1 week ago','owner'=>'SR'],
];
@endphp

<div class="card border border-border rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/30">
                <tr class="border-b border-border text-muted-foreground text-xs">
                    <th class="text-left px-4 py-3"><input type="checkbox" class="kt-checkbox"></th>
                    <th class="text-left px-4 py-3">Vendor</th>
                    <th class="text-left px-4 py-3">KYB</th>
                    <th class="text-left px-4 py-3">Card on file</th>
                    <th class="text-left px-4 py-3">Listings</th>
                    <th class="text-left px-4 py-3">Bids</th>
                    <th class="text-left px-4 py-3">Purchases</th>
                    <th class="text-left px-4 py-3">Wallet</th>
                    <th class="text-left px-4 py-3">Last activity</th>
                    <th class="text-left px-4 py-3">Owner</th>
                    <th class="text-left px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vendors as $v)
                    @php
                    $kybCls = match ($v['kyb']) {
                        'Verified' => 'kt-badge-success',
                        'Pending'  => 'kt-badge-warning',
                        'Failed'   => 'kt-badge-destructive',
                        default    => 'kt-badge-outline',
                    };
                    @endphp
                    <tr class="border-b border-border/50 hover:bg-muted/20 cursor-pointer" onclick="openVendorDetail('{{ $v['id'] }}')">
                        <td class="px-4 py-3" onclick="event.stopPropagation()"><input type="checkbox" class="kt-checkbox"></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded bg-muted flex items-center justify-center text-xs font-bold">{{ substr($v['name'],0,1) }}</div>
                                <div>
                                    <p class="font-medium">{{ $v['name'] }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $v['id'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3"><span class="kt-badge {{ $kybCls }} kt-badge-sm">{{ $v['kyb'] }}</span></td>
                        <td class="px-4 py-3">
                            <span class="kt-badge {{ $v['card'] ? 'kt-badge-success' : 'kt-badge-outline' }} kt-badge-sm">
                                {{ $v['card'] ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $v['listings'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $v['bids'] }}</td>
                        <td class="px-4 py-3 text-center">{{ $v['purchases'] }}</td>
                        <td class="px-4 py-3">
                            <span class="kt-badge {{ $v['wallet'] === 'Clear' ? 'kt-badge-success' : ($v['wallet'] === 'Hold' ? 'kt-badge-warning' : 'kt-badge-outline') }} kt-badge-sm">{{ $v['wallet'] }}</span>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground text-xs">{{ $v['last_activity'] }}</td>
                        <td class="px-4 py-3">
                            <span class="w-6 h-6 rounded-full bg-muted inline-flex items-center justify-center text-xs font-medium">{{ $v['owner'] }}</span>
                        </td>
                        <td class="px-4 py-3" onclick="event.stopPropagation()">
                            <div class="flex gap-1">
                                <button onclick="openVendorDetail('{{ $v['id'] }}')" class="kt-btn kt-btn-ghost kt-btn-xs">Open</button>
                                <button class="kt-btn kt-btn-ghost kt-btn-xs">Message</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-border text-sm text-muted-foreground">3 vendors</div>
</div>

</div>

{{-- Vendor Detail Panel --}}
<div id="panel-vendor-detail" class="fixed inset-0 z-[9000] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeVendorDetail()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-2xl card border-l border-border overflow-y-auto">
        <div class="px-5 py-4 border-b border-border flex items-center justify-between sticky top-0 bg-card z-10">
            <div>
                <h2 class="font-semibold">Fast Cars Ltd</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="kt-badge kt-badge-success kt-badge-sm">KYB: Verified</span>
                    <span class="kt-badge kt-badge-success kt-badge-sm">Card on file: Yes</span>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="closeVendorDetail()" class="kt-btn kt-btn-ghost kt-btn-sm">✕</button>
            </div>
        </div>

        {{-- Vendor tabs --}}
        <div class="flex gap-1 border-b border-border px-4 overflow-x-auto">
            @foreach (['overview','company','people','compliance','participation','communications','files','activity','history'] as $vt)
                <button onclick="switchVendorTab('{{ $vt }}')" id="vtab-btn-{{ $vt }}"
                    class="vtab-btn px-3 py-2.5 text-xs font-medium whitespace-nowrap border-b-2 -mb-px
                           {{ $vt === 'overview' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground' }}">
                    {{ ucfirst($vt) }}
                </button>
            @endforeach
        </div>

        <div class="p-5">
            <div id="vtab-overview" class="vtab-pane">
                <div class="grid grid-cols-3 gap-3 mb-4">
                    <div class="rounded-lg bg-muted/40 p-3 text-center"><p class="text-xs text-muted-foreground mb-1">Listings</p><p class="text-xl font-bold">12</p></div>
                    <div class="rounded-lg bg-muted/40 p-3 text-center"><p class="text-xs text-muted-foreground mb-1">Bids</p><p class="text-xl font-bold">48</p></div>
                    <div class="rounded-lg bg-muted/40 p-3 text-center"><p class="text-xs text-muted-foreground mb-1">Purchases</p><p class="text-xl font-bold">5</p></div>
                </div>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-muted-foreground mb-0.5">Legal name</dt><dd>Fast Cars Limited</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Trading name</dt><dd>Fast Cars Ltd</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Company number</dt><dd>12345678</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">VAT number</dt><dd>GB123456789</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Wallet status</dt><dd><span class="kt-badge kt-badge-success kt-badge-sm">Clear</span></dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Owner</dt><dd>JR</dd></div>
                </dl>
            </div>

            <div id="vtab-company" class="vtab-pane hidden">
                <div class="flex justify-end mb-3"><button class="kt-btn kt-btn-outline kt-btn-sm">Edit</button></div>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-muted-foreground mb-0.5">Address</dt><dd>1 Business Park, Manchester M1 1AA</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Billing email</dt><dd>accounts@fastcars.co.uk</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Phone</dt><dd>+44 161 000 0000</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Website</dt><dd>fastcars.co.uk</dd></div>
                </dl>
            </div>

            <div id="vtab-people" class="vtab-pane hidden">
                <div class="flex justify-end mb-3"><button class="kt-btn kt-btn-outline kt-btn-sm">Add contact</button></div>
                <div class="space-y-2">
                    <div class="rounded-lg border border-border p-3 flex items-center justify-between text-sm">
                        <div><p class="font-medium">James Riley</p><p class="text-xs text-muted-foreground">Director · james@fastcars.co.uk</p></div>
                        <span class="kt-badge kt-badge-outline kt-badge-sm">Primary</span>
                    </div>
                </div>
            </div>

            <div id="vtab-compliance" class="vtab-pane hidden space-y-4">
                <div class="rounded-lg border border-border p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-sm">KYB Status</h4>
                        <span class="kt-badge kt-badge-success">Verified</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center"><span class="text-muted-foreground">Certificate of incorporation</span><span class="kt-badge kt-badge-success kt-badge-sm">✔ Received</span></div>
                        <div class="flex justify-between items-center"><span class="text-muted-foreground">Proof of address</span><span class="kt-badge kt-badge-success kt-badge-sm">✔ Received</span></div>
                        <div class="flex justify-between items-center"><span class="text-muted-foreground">ID — Director</span><span class="kt-badge kt-badge-success kt-badge-sm">✔ Received</span></div>
                    </div>
                </div>
                <div class="rounded-lg bg-muted/30 p-3 text-xs text-muted-foreground">
                    <p class="font-medium text-foreground text-sm mb-1">Override log</p>
                    No overrides. Super Admin only.
                </div>
            </div>

            <div id="vtab-participation" class="vtab-pane hidden">
                <div class="space-y-3 text-sm">
                    <div class="rounded-lg border border-border p-3">
                        <p class="font-medium">AUC-205 October Prime Sale</p>
                        <div class="flex gap-4 mt-2 text-xs text-muted-foreground">
                            <span>Bids: 12</span><span>Wins: 2</span><span>Avg price: £13,500</span>
                        </div>
                    </div>
                    <div class="rounded-lg border border-border p-3">
                        <p class="font-medium">AUC-206 Private Dealer Event</p>
                        <div class="flex gap-4 mt-2 text-xs text-muted-foreground">
                            <span>Bids: 36</span><span>Wins: 3</span><span>Avg price: £11,200</span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="vtab-communications" class="vtab-pane hidden">
                <div class="flex justify-end mb-3"><button class="kt-btn kt-btn-primary kt-btn-sm">Compose</button></div>
                <p class="text-sm text-muted-foreground">No messages yet.</p>
            </div>

            <div id="vtab-files" class="vtab-pane hidden">
                <div class="border-2 border-dashed border-border rounded-lg p-8 text-center text-sm text-muted-foreground">
                    <i data-lucide="file" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>No files uploaded.
                </div>
            </div>

            <div id="vtab-activity" class="vtab-pane hidden space-y-3">
                <div class="flex gap-3 text-sm"><div class="mt-1 w-2 h-2 rounded-full bg-primary shrink-0"></div><div><p>KYB verified</p><p class="text-xs text-muted-foreground">1 week ago · Compliance team</p></div></div>
                <div class="flex gap-3 text-sm"><div class="mt-1 w-2 h-2 rounded-full bg-muted shrink-0"></div><div><p>Card on file added</p><p class="text-xs text-muted-foreground">2 weeks ago · JR</p></div></div>
            </div>

            <div id="vtab-history" class="vtab-pane hidden">
                <table class="w-full text-sm"><thead><tr class="border-b border-border text-xs text-muted-foreground"><th class="text-left pb-2">When</th><th class="text-left pb-2">Actor</th><th class="text-left pb-2">Field</th><th class="text-left pb-2">Before</th><th class="text-left pb-2">After</th></tr></thead>
                <tbody><tr class="border-b border-border/50"><td class="py-2 text-xs text-muted-foreground">1w ago</td><td class="py-2">Compliance</td><td class="py-2">kyb_status</td><td class="py-2 text-muted-foreground">Pending</td><td class="py-2">Verified</td></tr></tbody></table>
            </div>
        </div>

        <div class="sticky bottom-0 bg-card border-t border-border px-5 py-3 flex gap-2 flex-wrap">
            <button class="kt-btn kt-btn-outline kt-btn-sm">Send message</button>
            <button class="kt-btn kt-btn-outline kt-btn-sm">Invite to auction</button>
            <button class="kt-btn kt-btn-outline kt-btn-sm">Request documents</button>
            <button class="kt-btn kt-btn-outline kt-btn-sm">Assign owner</button>
            <button class="kt-btn kt-btn-outline kt-btn-sm">Start KYB review</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(id){ document.getElementById(id)?.classList.remove('hidden'); }
function openVendorDetail(id){ document.getElementById('panel-vendor-detail').classList.remove('hidden'); }
function closeVendorDetail(){ document.getElementById('panel-vendor-detail').classList.add('hidden'); }
function switchVendorTab(name){
    document.querySelectorAll('.vtab-pane').forEach(p=>p.classList.add('hidden'));
    document.querySelectorAll('.vtab-btn').forEach(b=>{b.classList.remove('border-primary','text-primary');b.classList.add('border-transparent','text-muted-foreground');});
    document.getElementById('vtab-'+name)?.classList.remove('hidden');
    const btn=document.getElementById('vtab-btn-'+name);
    if(btn){btn.classList.add('border-primary','text-primary');btn.classList.remove('border-transparent','text-muted-foreground');}
}
</script>
@endpush
@endsection
