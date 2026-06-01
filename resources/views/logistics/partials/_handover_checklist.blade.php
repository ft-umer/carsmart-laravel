{{-- resources/views/logistics/partials/_handover_checklist.blade.php --}}
{{-- Phase 4 — L3: Collection & Handover Checklist --}}

<div class="space-y-5" id="handover-checklist">

    <div class="card border border-border rounded-xl p-5">
        <h3 class="text-sm font-semibold mb-4 flex items-center gap-2">
            <i data-lucide="clipboard-check" class="w-4 h-4 opacity-60"></i>
            Collection &amp; Handover Checklist
        </h3>

        {{-- Identities --}}
        <div class="space-y-3 mb-5">
            <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Identities</h4>
            @foreach([
                ['id' => 'chk-buyer-id',  'label' => 'Buyer identity checked',  'checked' => $job['chk_buyer_id']  ?? false],
                ['id' => 'chk-seller-id', 'label' => 'Seller identity checked', 'checked' => $job['chk_seller_id'] ?? false],
            ] as $item)
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox"
                           id="{{ $item['id'] }}"
                           class="checklist-item form-checkbox rounded w-4 h-4"
                           {{ $item['checked'] ? 'checked' : '' }}
                           onchange="updateChecklist()" />
                    <span class="text-sm group-hover:text-foreground text-muted-foreground transition-colors
                                 {{ $item['checked'] ? 'line-through text-muted-foreground' : '' }}">
                        {{ $item['label'] }}
                    </span>
                </label>
            @endforeach
        </div>

        {{-- Documents --}}
        <div class="space-y-3 mb-5">
            <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Documents</h4>
            @foreach([
                ['id' => 'chk-v5c',  'label' => 'V5C present',  'checked' => $job['chk_v5c']  ?? false],
            ] as $item)
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" id="{{ $item['id'] }}"
                           class="checklist-item form-checkbox rounded w-4 h-4"
                           {{ $item['checked'] ? 'checked' : '' }}
                           onchange="updateChecklist()" />
                    <span class="text-sm {{ $item['checked'] ? 'line-through text-muted-foreground' : '' }}">
                        {{ $item['label'] }}
                    </span>
                </label>
            @endforeach
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" id="chk-keys"
                           class="checklist-item form-checkbox rounded w-4 h-4"
                           {{ ($job['chk_keys'] ?? false) ? 'checked' : '' }}
                           onchange="updateChecklist()" />
                    <span class="text-sm">Keys received</span>
                </label>
                <div class="flex items-center gap-2 ml-2">
                    <label class="text-xs text-muted-foreground">Count:</label>
                    <input id="key-count" type="number" min="0" max="10"
                           class="kt-input w-16 text-xs"
                           value="{{ $job['key_count'] ?? 2 }}" />
                </div>
            </div>
        </div>

        {{-- Photos --}}
        <div class="space-y-3 mb-5">
            <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Condition photos</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach(['Front','Rear','Side (driver)','Side (passenger)','Interior','Odometer','VIN plate'] as $photoLabel)
                    @php $photoKey = Str::slug($photoLabel); @endphp
                    <div class="relative rounded-xl border-2 border-dashed border-border bg-muted/10
                                hover:border-primary/50 transition-colors cursor-pointer
                                flex flex-col items-center justify-center p-4 text-center min-h-[80px]"
                         onclick="triggerPhotoUpload('{{ $photoKey }}')">
                        @if (!empty($job['photos'][$photoKey]))
                            <img src="{{ $job['photos'][$photoKey] }}" alt="{{ $photoLabel }}"
                                 class="w-full h-20 object-cover rounded-lg" />
                            <div class="absolute top-1.5 right-1.5">
                                <span class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center">
                                    <i data-lucide="check" class="w-3 h-3 text-white"></i>
                                </span>
                            </div>
                        @else
                            <i data-lucide="camera" class="w-5 h-5 text-muted-foreground mb-1 opacity-50"></i>
                            <span class="text-xs text-muted-foreground">{{ $photoLabel }}</span>
                        @endif
                        <input type="file" id="photo-{{ $photoKey }}" class="hidden"
                               accept="image/*" onchange="handlePhotoUpload(this, '{{ $photoLabel }}')" />
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Condition notes --}}
        <div class="mb-5">
            <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">
                Condition notes
            </label>
            <textarea id="condition-notes" class="kt-input w-full" rows="3"
                      placeholder="Note any damage, missing items, or discrepancies…">{{ $job['condition_notes'] ?? '' }}</textarea>
        </div>

        {{-- Signatures --}}
        <div class="space-y-4 mb-5">
            <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Signatures</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['buyer' => 'Buyer', 'seller' => 'Seller'] as $party => $label)
                    <div>
                        <div class="text-xs font-medium mb-1.5">{{ $label }} signature</div>
                        @if (!empty($job[$party . '_signature']))
                            <div class="rounded-xl border border-green-300 bg-green-50 dark:bg-green-900/20 p-3 text-xs text-green-700 dark:text-green-300 flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                                Signed by {{ $label }}
                            </div>
                        @else
                            <div class="relative">
                                <canvas id="sig-canvas-{{ $party }}"
                                        class="w-full h-28 rounded-xl border border-border bg-background cursor-crosshair"
                                        style="touch-action: none;"></canvas>
                                <button onclick="clearSignature('{{ $party }}')"
                                        class="absolute top-2 right-2 kt-btn kt-btn-ghost kt-btn-sm text-xs">
                                    Clear
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Progress + confirm --}}
        <div class="pt-4 border-t border-border">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-muted-foreground">Checklist progress</span>
                <span id="checklist-pct" class="text-xs font-semibold">0%</span>
            </div>
            <div class="h-2 rounded-full bg-muted overflow-hidden mb-4">
                <div id="checklist-bar" class="h-full bg-primary rounded-full transition-all" style="width: 0%"></div>
            </div>
            <button id="btn-confirm-handover"
                    class="kt-btn kt-btn-mono w-full"
                    onclick="confirmHandover()">
                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                Confirm handover
            </button>
            <p class="text-xs text-muted-foreground text-center mt-2">
                Handover confirmation unlocks payout approval.
            </p>
        </div>
    </div>
</div>

<script>
function updateChecklist() {
    const items = document.querySelectorAll('.checklist-item');
    const done  = Array.from(items).filter(c => c.checked).length;
    const pct   = items.length ? Math.round((done / items.length) * 100) : 0;
    const bar   = document.getElementById('checklist-bar');
    const label = document.getElementById('checklist-pct');
    if (bar)   bar.style.width = pct + '%';
    if (label) label.textContent = pct + '%';
}

function triggerPhotoUpload(key) {
    document.getElementById('photo-' + key)?.click();
}

function handlePhotoUpload(input, label) {
    const file = input.files[0];
    if (!file) return;
    window.CS4.toast(label + ' photo uploaded.', 'success');
}

function clearSignature(party) {
    const canvas = document.getElementById('sig-canvas-' + party);
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function confirmHandover() {
    const items = document.querySelectorAll('.checklist-item');
    const done  = Array.from(items).filter(c => c.checked).length;
    if (done < items.length) {
        window.CS4.toast('Complete all checklist items before confirming handover.', 'warning');
        return;
    }
    window.CS4.auditEvent('handover_confirmed', { job: '{{ $job["id"] ?? "" }}' });
    window.CS4.toast('Handover confirmed. Payout approval is now available.', 'success');
    document.getElementById('btn-confirm-handover').disabled = true;
    document.getElementById('btn-confirm-handover').textContent = '✔ Handover confirmed';
}

/* Simple canvas drawing for signatures */
document.querySelectorAll('[id^="sig-canvas-"]').forEach(canvas => {
    const ctx = canvas.getContext('2d');
    let drawing = false;
    const rect  = () => canvas.getBoundingClientRect();
    const getXY = e => {
        const r = rect();
        const src = e.touches ? e.touches[0] : e;
        return [src.clientX - r.left, src.clientY - r.top];
    };
    canvas.addEventListener('mousedown',  e => { drawing = true; ctx.beginPath(); const [x,y] = getXY(e); ctx.moveTo(x,y); });
    canvas.addEventListener('mousemove',  e => { if (!drawing) return; const [x,y] = getXY(e); ctx.lineTo(x,y); ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue('--color-foreground') || '#000'; ctx.lineWidth = 1.5; ctx.stroke(); });
    canvas.addEventListener('mouseup',    () => drawing = false);
    canvas.addEventListener('mouseleave', () => drawing = false);
    canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; ctx.beginPath(); const [x,y] = getXY(e); ctx.moveTo(x,y); }, { passive: false });
    canvas.addEventListener('touchmove',  e => { e.preventDefault(); if (!drawing) return; const [x,y] = getXY(e); ctx.lineTo(x,y); ctx.lineWidth = 1.5; ctx.stroke(); }, { passive: false });
    canvas.addEventListener('touchend',   () => drawing = false);
});

updateChecklist();
</script>
