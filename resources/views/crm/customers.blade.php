{{-- resources/views/crm/customers.blade.php --}}
{{-- Phase 3 — C4: Customers (People) — Browse & Detail --}}
@extends('layouts.app')
@section('title', 'Customers — Carsmart')

@section('content')
<div class="kt-container-fixed">

{{-- Retention banner --}}
@include('partials._retention_banner')

{{-- Page header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h1 class="text-xl font-semibold">Customers (People)</h1>
        <p class="text-sm text-muted-foreground mt-0.5">Manage person records, consent, and communications.</p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('modal-send-message')" class="kt-btn kt-btn-outline kt-btn-sm">
            <i data-lucide="send" class="w-4 h-4 mr-1"></i> Send message
        </button>
        <button onclick="openModal('modal-import')" class="kt-btn kt-btn-outline kt-btn-sm">
            <i data-lucide="upload" class="w-4 h-4 mr-1"></i> Import
        </button>
        <button onclick="openModal('modal-add-person')" class="kt-btn kt-btn-primary kt-btn-sm">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Add person
        </button>
    </div>
</div>

{{-- Filters --}}
<div class="card border border-border rounded-xl p-4 mb-5">
    <div class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[220px]">
            <label class="form-label text-xs">Search</label>
            <input type="text" placeholder="Name, email, phone…" class="kt-input w-full kt-input-sm">
        </div>
        <div>
            <label class="form-label text-xs">Consent</label>
            <select class="kt-select kt-select-sm"><option>Any</option><option>Email ✔</option><option>SMS ✔</option><option>WhatsApp ✔</option><option>None</option></select>
        </div>
        <div>
            <label class="form-label text-xs">Tags</label>
            <select class="kt-select kt-select-sm"><option>Any</option></select>
        </div>
        <div>
            <label class="form-label text-xs">Source</label>
            <select class="kt-select kt-select-sm"><option>Any</option><option>Website</option><option>Phone</option><option>Import</option></select>
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
<div class="card border border-border rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/30">
                <tr class="border-b border-border text-muted-foreground text-xs">
                    <th class="text-left px-4 py-3"><input type="checkbox" class="kt-checkbox"></th>
                    <th class="text-left px-4 py-3">Person</th>
                    <th class="text-left px-4 py-3">Phone</th>
                    <th class="text-left px-4 py-3">Email</th>
                    <th class="text-left px-4 py-3">Consent</th>
                    <th class="text-left px-4 py-3">Tags</th>
                    <th class="text-left px-4 py-3">Listings</th>
                    <th class="text-left px-4 py-3">Last activity</th>
                    <th class="text-left px-4 py-3">Owner</th>
                    <th class="text-left px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                $customers = [
                    ['id'=>'CST-001','name'=>'Jane Doe','phone'=>'+44 7700 900001','email'=>'jane.doe@example.com','consent'=>['email'=>true,'sms'=>false,'whatsapp'=>true],'tags'=>['VIP','Repeat'],'listings'=>2,'last_activity'=>'2 days ago','owner'=>'AM','dnc'=>false],
                    ['id'=>'CST-002','name'=>'David Hughes','phone'=>'+44 7700 900002','email'=>'david.h@example.com','consent'=>['email'=>true,'sms'=>true,'whatsapp'=>false],'tags'=>[],'listings'=>0,'last_activity'=>'1 week ago','owner'=>'SR','dnc'=>false],
                    ['id'=>'CST-003','name'=>'Maria Santos','phone'=>'+44 7700 900003','email'=>'maria.s@example.com','consent'=>['email'=>false,'sms'=>false,'whatsapp'=>false],'tags'=>['DNC'],'listings'=>1,'last_activity'=>'3 days ago','owner'=>'AM','dnc'=>true],
                ];
                @endphp
                @foreach ($customers as $c)
                    <tr class="border-b border-border/50 hover:bg-muted/20 cursor-pointer" onclick="openDetail('{{ $c['id'] }}')">
                        <td class="px-4 py-3" onclick="event.stopPropagation()"><input type="checkbox" class="kt-checkbox"></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center text-xs font-medium text-primary">
                                    {{ substr($c['name'], 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-foreground">{{ $c['name'] }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $c['id'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">{{ $c['phone'] }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ $c['email'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-1 flex-wrap">
                                @if ($c['dnc'])
                                    <span class="kt-badge kt-badge-destructive kt-badge-sm">DNC</span>
                                @else
                                    <span class="kt-badge {{ $c['consent']['email'] ? 'kt-badge-success' : 'kt-badge-outline' }} kt-badge-sm">E</span>
                                    <span class="kt-badge {{ $c['consent']['sms'] ? 'kt-badge-success' : 'kt-badge-outline' }} kt-badge-sm">S</span>
                                    <span class="kt-badge {{ $c['consent']['whatsapp'] ? 'kt-badge-success' : 'kt-badge-outline' }} kt-badge-sm">W</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @foreach ($c['tags'] as $tag)
                                <span class="kt-badge kt-badge-outline kt-badge-sm mr-1">{{ $tag }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-center">{{ $c['listings'] }}</td>
                        <td class="px-4 py-3 text-muted-foreground text-xs">{{ $c['last_activity'] }}</td>
                        <td class="px-4 py-3">
                            <span class="w-6 h-6 rounded-full bg-muted inline-flex items-center justify-center text-xs font-medium">{{ $c['owner'] }}</span>
                        </td>
                        <td class="px-4 py-3" onclick="event.stopPropagation()">
                            <div class="flex gap-1">
                                <button onclick="openDetail('{{ $c['id'] }}')" class="kt-btn kt-btn-ghost kt-btn-xs">Open</button>
                                <button class="kt-btn kt-btn-ghost kt-btn-xs">Message</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-border flex items-center justify-between text-sm text-muted-foreground">
        <span>3 people</span>
        <div class="flex gap-2">
            <button class="kt-btn kt-btn-ghost kt-btn-xs" disabled>← Prev</button>
            <button class="kt-btn kt-btn-ghost kt-btn-xs" disabled>Next →</button>
        </div>
    </div>
</div>

</div>

{{-- Person Detail Side Panel --}}
<div id="panel-person-detail" class="fixed inset-0 z-[9000] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDetail()"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-2xl card border-l border-border overflow-y-auto">
        <div class="px-5 py-4 border-b border-border flex items-center justify-between sticky top-0 bg-card z-10">
            <div>
                <h2 class="font-semibold" id="detail-name">Jane Doe</h2>
                <p class="text-xs text-muted-foreground">Owner: AM</p>
            </div>
            <div class="flex gap-2">
                <button class="kt-btn kt-btn-outline kt-btn-sm">Export data</button>
                <button onclick="closeDetail()" class="kt-btn kt-btn-ghost kt-btn-sm">✕</button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="flex gap-1 border-b border-border px-4 overflow-x-auto">
            @foreach (['overview','contact','vehicles','listings','communications','files','activity','history'] as $dt)
                <button onclick="switchDetailTab('{{ $dt }}')" id="dtab-btn-{{ $dt }}"
                    class="dtab-btn px-3 py-2.5 text-xs font-medium whitespace-nowrap border-b-2 -mb-px
                           {{ $dt === 'overview' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground' }}">
                    {{ ucfirst($dt) }}
                </button>
            @endforeach
        </div>

        <div class="p-5 space-y-4">

            {{-- Overview --}}
            <div id="dtab-overview" class="dtab-pane space-y-4">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg bg-muted/40 p-3"><p class="text-xs text-muted-foreground mb-1">Consent</p>
                        <div class="flex gap-1">
                            <span class="kt-badge kt-badge-success kt-badge-sm">Email ✔</span>
                            <span class="kt-badge kt-badge-outline kt-badge-sm">SMS ✖</span>
                            <span class="kt-badge kt-badge-success kt-badge-sm">WhatsApp ✔</span>
                        </div>
                    </div>
                    <div class="rounded-lg bg-muted/40 p-3"><p class="text-xs text-muted-foreground mb-1">Listings & Deals</p><p class="font-bold text-lg">2</p></div>
                </div>
                <div class="text-sm space-y-2">
                    <div class="flex justify-between py-1.5 border-b border-border/50"><span class="text-muted-foreground">Email</span><span>jane.doe@example.com</span></div>
                    <div class="flex justify-between py-1.5 border-b border-border/50"><span class="text-muted-foreground">Phone</span><span>+44 7700 900001</span></div>
                    <div class="flex justify-between py-1.5 border-b border-border/50"><span class="text-muted-foreground">Source</span><span>Website</span></div>
                    <div class="flex justify-between py-1.5 border-b border-border/50"><span class="text-muted-foreground">Last contact</span><span>2 days ago</span></div>
                </div>
            </div>

            {{-- Contact --}}
            <div id="dtab-contact" class="dtab-pane hidden space-y-4">
                <div class="flex justify-end"><button class="kt-btn kt-btn-outline kt-btn-sm">Edit</button></div>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-muted-foreground mb-0.5">Address</dt><dd>123 High Street, London SW1A 1AA</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Preferred channel</dt><dd>Email</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Best time to reach</dt><dd>Morning</dd></div>
                    <div><dt class="text-muted-foreground mb-0.5">Language</dt><dd>English</dd></div>
                </dl>
            </div>

            {{-- Vehicles --}}
            <div id="dtab-vehicles" class="dtab-pane hidden">
                <p class="text-sm text-muted-foreground">Owned vehicles and favourites. Valuation history is accessible via linked Listings.</p>
                <div class="mt-3 rounded-lg border border-border p-3 text-sm">
                    <p class="font-medium">BMW 330i — AB19 CDE</p>
                    <p class="text-xs text-muted-foreground">Sold via LST-1023 · Oct 2025</p>
                </div>
            </div>

            {{-- Listings --}}
            <div id="dtab-listings" class="dtab-pane hidden">
                <div class="space-y-2">
                    <div class="rounded-lg border border-border p-3 flex items-center justify-between text-sm">
                        <div><p class="font-medium">LST-1023 — BMW 330i 2019</p><p class="text-xs text-muted-foreground">Deal closed · Oct 2025</p></div>
                        <span class="kt-badge kt-badge-success">Closed</span>
                    </div>
                    <div class="rounded-lg border border-border p-3 flex items-center justify-between text-sm">
                        <div><p class="font-medium">LST-1088 — Audi A3 2021</p><p class="text-xs text-muted-foreground">Ready to publish</p></div>
                        <span class="kt-badge kt-badge-primary">Ready</span>
                    </div>
                </div>
            </div>

            {{-- Communications --}}
            <div id="dtab-communications" class="dtab-pane hidden">
                <div class="flex justify-end mb-3">
                    <button class="kt-btn kt-btn-primary kt-btn-sm">Compose</button>
                </div>
                <div class="space-y-3">
                    <div class="rounded-lg bg-muted/30 p-3 text-sm">
                        <div class="flex justify-between text-xs text-muted-foreground mb-1"><span>Email</span><span>2 days ago</span></div>
                        <p>Your listing has been published and is now live…</p>
                    </div>
                </div>
            </div>

            {{-- Files --}}
            <div id="dtab-files" class="dtab-pane hidden">
                <div class="border-2 border-dashed border-border rounded-lg p-8 text-center text-sm text-muted-foreground">
                    <i data-lucide="file" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>No files uploaded.
                </div>
            </div>

            {{-- Activity --}}
            <div id="dtab-activity" class="dtab-pane hidden space-y-3">
                @foreach ([['2d ago','Listing LST-1088 created'],['5d ago','Consent updated: WhatsApp → Yes'],['2w ago','Email sent: Listing confirmation']] as [$time,$desc])
                    <div class="flex gap-3 text-sm"><div class="mt-1 w-2 h-2 rounded-full bg-primary shrink-0"></div><div><p>{{ $desc }}</p><p class="text-xs text-muted-foreground">{{ $time }}</p></div></div>
                @endforeach
            </div>

            {{-- History --}}
            <div id="dtab-history" class="dtab-pane hidden">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-border text-xs text-muted-foreground"><th class="text-left pb-2">When</th><th class="text-left pb-2">Actor</th><th class="text-left pb-2">Field</th><th class="text-left pb-2">Before</th><th class="text-left pb-2">After</th></tr></thead>
                    <tbody>
                        <tr class="border-b border-border/50"><td class="py-2 text-xs text-muted-foreground">5d ago</td><td class="py-2">AM</td><td class="py-2">consent_whatsapp</td><td class="py-2 text-muted-foreground">false</td><td class="py-2">true</td></tr>
                    </tbody>
                </table>
            </div>

        </div>

        {{-- Action bar --}}
        <div class="sticky bottom-0 bg-card border-t border-border px-5 py-3 flex gap-2 flex-wrap">
            <button class="kt-btn kt-btn-outline kt-btn-sm">Send message</button>
            <button class="kt-btn kt-btn-outline kt-btn-sm">Add note</button>
            <button class="kt-btn kt-btn-outline kt-btn-sm">Assign</button>
            <button class="kt-btn kt-btn-outline kt-btn-sm">Merge</button>
            <button class="kt-btn kt-btn-destructive kt-btn-sm ml-auto">Mark DNC</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openDetail(id) { document.getElementById('panel-person-detail').classList.remove('hidden'); }
function closeDetail() { document.getElementById('panel-person-detail').classList.add('hidden'); }
function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
function switchDetailTab(name) {
    document.querySelectorAll('.dtab-pane').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.dtab-btn').forEach(b => {
        b.classList.remove('border-primary','text-primary');
        b.classList.add('border-transparent','text-muted-foreground');
    });
    document.getElementById('dtab-'+name)?.classList.remove('hidden');
    const btn = document.getElementById('dtab-btn-'+name);
    if(btn){ btn.classList.add('border-primary','text-primary'); btn.classList.remove('border-transparent','text-muted-foreground'); }
}
</script>
@endpush

@endsection
