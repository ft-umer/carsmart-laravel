{{-- resources/views/crm/imports.blade.php --}}
{{-- Phase 3 — C9: Imports & Deduplication --}}
@extends('layouts.app')
@section('title', 'Imports & Deduplication — Carsmart')

@section('content')
<div class="kt-container-fixed">

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <div>
        <h1 class="text-xl font-semibold">Imports & Deduplication</h1>
        <p class="text-sm text-muted-foreground mt-0.5">Bulk import people, vendors, and leads; keep records clean.</p>
    </div>
</div>

{{-- Tabs --}}
<div class="flex gap-1 mb-5 border-b border-border">
    <button onclick="switchTab('import')" id="tab-btn-import"
        class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px border-primary text-primary">Import wizard</button>
    <button onclick="switchTab('dedup')" id="tab-btn-dedup"
        class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px border-transparent text-muted-foreground hover:text-foreground">Deduplication</button>
    <button onclick="switchTab('history')" id="tab-btn-history"
        class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px border-transparent text-muted-foreground hover:text-foreground">Import history</button>
</div>

{{-- IMPORT WIZARD --}}
<div id="tab-import" class="tab-pane">
    {{-- Stepper --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto">
        @php $steps = ['1 Upload','2 Map columns','3 Validate','4 Preview','5 Import']; @endphp
        @foreach ($steps as $i => $step)
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1.5 {{ $i === 0 ? 'text-primary font-semibold' : 'text-muted-foreground' }} whitespace-nowrap text-sm">
                    <div class="w-6 h-6 rounded-full {{ $i === 0 ? 'bg-primary text-primary-foreground' : 'bg-muted' }} flex items-center justify-center text-xs font-bold">{{ $i + 1 }}</div>
                    {{ explode(' ', $step, 2)[1] }}
                </div>
                @if (!$loop->last)
                    <div class="w-8 h-px bg-border"></div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Main step --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Step 1: Upload --}}
            <div id="step-1" class="card border border-border rounded-xl p-5">
                <h3 class="font-semibold mb-4">Step 1 — Upload file</h3>
                <div>
                    <label class="form-label">Import type</label>
                    <select class="kt-select w-full mb-4">
                        <option>People (Customers)</option>
                        <option>Vendors (Companies)</option>
                        <option>Leads</option>
                    </select>
                </div>
                <div class="border-2 border-dashed border-border rounded-xl p-10 text-center cursor-pointer hover:bg-muted/20 transition-colors"
                    ondragover="event.preventDefault()" ondrop="handleDrop(event)">
                    <i data-lucide="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-muted-foreground opacity-60"></i>
                    <p class="font-medium mb-1">Drop CSV file here or click to browse</p>
                    <p class="text-sm text-muted-foreground">CSV files only · UTF-8 recommended</p>
                    <input type="file" id="import-file" accept=".csv" class="hidden" onchange="handleFile(event)">
                    <button onclick="document.getElementById('import-file').click()" class="kt-btn kt-btn-outline kt-btn-sm mt-4">Choose file</button>
                </div>
                <div class="flex justify-between items-center mt-3">
                    <a href="#" class="kt-btn kt-btn-ghost kt-btn-sm text-primary">
                        <i data-lucide="download" class="w-3.5 h-3.5 mr-1"></i> Download sample CSV
                    </a>
                    <div id="file-info" class="hidden text-sm text-success flex items-center gap-1">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span id="file-name"></span>
                    </div>
                </div>
            </div>

            {{-- Step 2: Map columns (shown after upload) --}}
            <div id="step-2" class="card border border-border rounded-xl p-5 hidden">
                <h3 class="font-semibold mb-4">Step 2 — Map columns</h3>
                <div class="space-y-3">
                    @foreach (['First name','Last name','Email','Phone','Source','Tags','Notes'] as $field)
                        <div class="flex items-center gap-3 text-sm">
                            <span class="w-32 font-medium">{{ $field }}</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 text-muted-foreground flex-shrink-0"></i>
                            <select class="kt-select flex-1 kt-select-sm">
                                <option>— Skip —</option>
                                <option {{ strtolower(str_replace(' ','_',$field)) === strtolower(str_replace(' ','_',$field)) ? 'selected' : '' }}>
                                    Column: {{ strtolower(str_replace(' ','_',$field)) }}
                                </option>
                                <option>Column: name</option>
                                <option>Column: email_address</option>
                                <option>Column: tel</option>
                            </select>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-2 mt-4 pt-4 border-t border-border">
                    <button class="kt-btn kt-btn-outline kt-btn-sm">
                        <i data-lucide="save" class="w-3.5 h-3.5 mr-1"></i> Save mapping as preset
                    </button>
                    <button onclick="goToStep(3)" class="kt-btn kt-btn-primary kt-btn-sm ml-auto">Validate →</button>
                </div>
            </div>

            {{-- Step 3: Validate --}}
            <div id="step-3" class="card border border-border rounded-xl p-5 hidden">
                <h3 class="font-semibold mb-4">Step 3 — Validate</h3>
                <div class="rounded-lg bg-success/10 border border-success/20 p-3 mb-4 text-sm">
                    <p class="font-medium text-success">92 rows ready · 3 rows have errors</p>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="rounded-lg bg-destructive/10 border border-destructive/20 p-3">
                        <p class="font-medium text-destructive">Row 14 — Missing required field: email</p>
                    </div>
                    <div class="rounded-lg bg-destructive/10 border border-destructive/20 p-3">
                        <p class="font-medium text-destructive">Row 37 — Invalid phone format: 07700abc123</p>
                    </div>
                    <div class="rounded-lg bg-warning/10 border border-warning/20 p-3">
                        <p class="font-medium text-warning-foreground">Row 55 — Possible duplicate: email matches CST-088 (Jane Doe)</p>
                    </div>
                </div>
                <div class="flex gap-2 mt-4 pt-4 border-t border-border">
                    <button class="kt-btn kt-btn-outline kt-btn-sm">Download error rows</button>
                    <button onclick="goToStep(4)" class="kt-btn kt-btn-primary kt-btn-sm ml-auto">Preview import (92 rows) →</button>
                </div>
            </div>

            {{-- Step 4: Preview --}}
            <div id="step-4" class="card border border-border rounded-xl p-5 hidden">
                <h3 class="font-semibold mb-4">Step 4 — Preview (first 5 rows)</h3>
                <div class="overflow-x-auto text-sm">
                    <table class="w-full">
                        <thead><tr class="border-b border-border text-xs text-muted-foreground">
                            <th class="text-left pb-2 pr-4">Name</th>
                            <th class="text-left pb-2 pr-4">Email</th>
                            <th class="text-left pb-2">Phone</th>
                        </tr></thead>
                        <tbody>
                            @foreach ([['Alice Brown','alice@example.com','+44 7700 100001'],['Bob Smith','bob@example.com','+44 7700 100002'],['Claire Jones','claire@example.com','+44 7700 100003']] as $row)
                                <tr class="border-b border-border/50"><td class="py-2 pr-4">{{ $row[0] }}</td><td class="py-2 pr-4 text-muted-foreground">{{ $row[1] }}</td><td class="py-2 text-muted-foreground">{{ $row[2] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-muted-foreground mt-2">… and 89 more rows</p>
                <div class="flex gap-2 mt-4 pt-4 border-t border-border">
                    <button onclick="goToStep(5)" class="kt-btn kt-btn-primary ml-auto">Run import (92 records) →</button>
                </div>
            </div>

            {{-- Step 5: Complete --}}
            <div id="step-5" class="card border border-border rounded-xl p-5 hidden text-center">
                <i data-lucide="check-circle" class="w-12 h-12 mx-auto mb-3 text-success"></i>
                <h3 class="font-semibold text-lg mb-1">Import complete</h3>
                <p class="text-muted-foreground text-sm">92 records imported · 3 skipped</p>
                <div class="flex gap-2 justify-center mt-4">
                    <a href="{{ route('customers.index') }}" class="kt-btn kt-btn-outline kt-btn-sm">View customers</a>
                    <button onclick="resetWizard()" class="kt-btn kt-btn-primary kt-btn-sm">New import</button>
                </div>
            </div>
        </div>

        {{-- Sidebar tips --}}
        <div class="space-y-4">
            <div class="card border border-border rounded-xl p-4 text-sm">
                <h4 class="font-semibold mb-3">Import rules</h4>
                <ul class="space-y-2 text-muted-foreground">
                    <li class="flex gap-2"><i data-lucide="info" class="w-4 h-4 flex-shrink-0 text-primary mt-0.5"></i>Email or phone required for People</li>
                    <li class="flex gap-2"><i data-lucide="info" class="w-4 h-4 flex-shrink-0 text-primary mt-0.5"></i>Duplicate check: email › phone › name+address</li>
                    <li class="flex gap-2"><i data-lucide="info" class="w-4 h-4 flex-shrink-0 text-primary mt-0.5"></i>PII is encrypted at rest</li>
                    <li class="flex gap-2"><i data-lucide="info" class="w-4 h-4 flex-shrink-0 text-primary mt-0.5"></i>Max 10,000 rows per import</li>
                    <li class="flex gap-2"><i data-lucide="info" class="w-4 h-4 flex-shrink-0 text-primary mt-0.5"></i>Vendor import requires Admin role</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- DEDUPLICATION --}}
<div id="tab-dedup" class="tab-pane hidden">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-semibold">Potential duplicates</h3>
            <p class="text-sm text-muted-foreground mt-0.5">Matched by email › phone › name+address</p>
        </div>
        <button class="kt-btn kt-btn-outline kt-btn-sm">Bulk merge suggestions</button>
    </div>

    @php
    $dupes = [
        ['a'=>['id'=>'CST-001','name'=>'Jane Doe','email'=>'jane.doe@example.com','phone'=>'+44 7700 900001'],'b'=>['id'=>'CST-099','name'=>'J. Doe','email'=>'jane.doe@example.com','phone'=>'+44 7700 900001'],'reason'=>'email + phone match'],
        ['a'=>['id'=>'CST-045','name'=>'David Hughes','email'=>'david.h@example.com','phone'=>''],'b'=>['id'=>'LED-2055','name'=>'D. Hughes','email'=>'david.h@example.com','phone'=>''],'reason'=>'email match'],
    ];
    @endphp

    <div class="space-y-4">
        @foreach ($dupes as $d)
            <div class="card border border-border rounded-xl p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 grid grid-cols-2 gap-4 text-sm">
                        <div class="rounded-lg bg-muted/40 p-3">
                            <p class="font-semibold mb-1">{{ $d['a']['name'] }}</p>
                            <p class="text-xs text-muted-foreground">{{ $d['a']['id'] }}</p>
                            <p class="text-xs">{{ $d['a']['email'] }}</p>
                            @if ($d['a']['phone'])<p class="text-xs">{{ $d['a']['phone'] }}</p>@endif
                        </div>
                        <div class="rounded-lg bg-muted/40 p-3">
                            <p class="font-semibold mb-1">{{ $d['b']['name'] }}</p>
                            <p class="text-xs text-muted-foreground">{{ $d['b']['id'] }}</p>
                            <p class="text-xs">{{ $d['b']['email'] }}</p>
                            @if ($d['b']['phone'])<p class="text-xs">{{ $d['b']['phone'] }}</p>@endif
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="kt-badge kt-badge-warning kt-badge-sm">{{ $d['reason'] }}</span>
                        <button onclick="openModal('modal-merge-detail')" class="kt-btn kt-btn-primary kt-btn-sm">Merge</button>
                        <button class="kt-btn kt-btn-ghost kt-btn-sm text-muted-foreground">Dismiss</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- IMPORT HISTORY --}}
<div id="tab-history" class="tab-pane hidden">
    <div class="card border border-border rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-muted/30"><tr class="border-b border-border text-xs text-muted-foreground">
                <th class="text-left px-4 py-3">Date</th>
                <th class="text-left px-4 py-3">Type</th>
                <th class="text-left px-4 py-3">Rows imported</th>
                <th class="text-left px-4 py-3">Skipped</th>
                <th class="text-left px-4 py-3">By</th>
                <th class="text-left px-4 py-3">Actions</th>
            </tr></thead>
            <tbody>
                <tr class="border-b border-border/50"><td class="px-4 py-3">2 days ago</td><td class="px-4 py-3">People</td><td class="px-4 py-3">92</td><td class="px-4 py-3">3</td><td class="px-4 py-3">AM</td><td class="px-4 py-3"><button class="kt-btn kt-btn-ghost kt-btn-xs">Download log</button></td></tr>
                <tr class="border-b border-border/50"><td class="px-4 py-3">1 week ago</td><td class="px-4 py-3">Leads</td><td class="px-4 py-3">240</td><td class="px-4 py-3">8</td><td class="px-4 py-3">SR</td><td class="px-4 py-3"><button class="kt-btn kt-btn-ghost kt-btn-xs">Download log</button></td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Merge modal --}}
<div id="modal-merge-detail" class="fixed inset-0 z-[10000] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-merge-detail')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-2xl border border-border rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="font-semibold">Merge records</h3>
                <button onclick="closeModal('modal-merge-detail')" class="kt-btn kt-btn-ghost kt-btn-sm">✕</button>
            </div>
            <div class="p-5">
                <p class="text-sm text-muted-foreground mb-4">Choose which record to keep as master, then select values field by field.</p>
                <div class="grid grid-cols-3 gap-3 text-sm">
                    <div class="text-xs font-medium text-muted-foreground">Field</div>
                    <div class="text-xs font-medium text-muted-foreground">CST-001 (Jane Doe)</div>
                    <div class="text-xs font-medium text-muted-foreground">CST-099 (J. Doe)</div>
                    @foreach (['Name','Email','Phone','Source','Tags'] as $f)
                        <div class="text-muted-foreground py-1 border-t border-border/50">{{ $f }}</div>
                        <div class="py-1 border-t border-border/50">
                            <label class="flex items-center gap-1.5"><input type="radio" name="merge_{{ strtolower($f) }}" value="a" checked class="kt-radio"> {{ $f === 'Name' ? 'Jane Doe' : ($f === 'Email' ? 'jane.doe@example.com' : ($f === 'Phone' ? '+44 7700 900001' : '—')) }}</label>
                        </div>
                        <div class="py-1 border-t border-border/50">
                            <label class="flex items-center gap-1.5"><input type="radio" name="merge_{{ strtolower($f) }}" value="b" class="kt-radio"> {{ $f === 'Name' ? 'J. Doe' : ($f === 'Email' ? 'jane.doe@example.com' : ($f === 'Phone' ? '+44 7700 900001' : '—')) }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="px-5 py-4 border-t border-border flex justify-end gap-2">
                <button onclick="closeModal('modal-merge-detail')" class="kt-btn kt-btn-outline">Cancel</button>
                <button class="kt-btn kt-btn-primary">Merge records</button>
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
function handleFile(e){
    const f = e.target.files[0];
    if(f){ document.getElementById('file-info').classList.remove('hidden'); document.getElementById('file-name').textContent=f.name; setTimeout(()=>goToStep(2),500); }
}
function handleDrop(e){ e.preventDefault(); const f=e.dataTransfer.files[0]; if(f && f.name.endsWith('.csv')){ document.getElementById('file-info').classList.remove('hidden'); document.getElementById('file-name').textContent=f.name; setTimeout(()=>goToStep(2),500); } }
function goToStep(n){
    for(let i=1;i<=5;i++) document.getElementById('step-'+i)?.classList.add('hidden');
    document.getElementById('step-'+n)?.classList.remove('hidden');
}
function resetWizard(){ for(let i=2;i<=5;i++) document.getElementById('step-'+i)?.classList.add('hidden'); document.getElementById('step-1')?.classList.remove('hidden'); document.getElementById('file-info').classList.add('hidden'); }
</script>
@endpush
@endsection
