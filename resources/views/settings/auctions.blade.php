{{-- resources/views/settings/auctions.blade.php --}}
{{-- Phase 5 — S4: Settings → Auctions Reference --}}
@extends('layouts.app')
@section('title', 'Auctions Reference — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">
  <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Auctions Reference</span>
    </nav>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Auctions Reference</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Bid increment bands and default sniper extension minutes</p>
        </div>
    </div>

    {{-- Sniper default --}}
    <div class="card border border-border rounded-xl p-6 mb-4">
        <h2 class="text-sm font-semibold text-foreground mb-4">Default sniper extension</h2>
        <form method="POST" action="{{ route('settings.auctions.sniper') }}">
            @csrf @method('PATCH')
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">
                        Minutes to extend when bid placed in final window
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="sniper_minutes" class="kt-input w-28"
                               value="{{ $settings['sniper_minutes'] ?? 2 }}" min="1" max="30" />
                        <span class="text-sm text-muted-foreground">minutes</span>
                    </div>
                </div>
                <button type="submit" class="kt-btn kt-btn-mono self-end">Save</button>
            </div>
        </form>
    </div>

    {{-- Bid increment bands --}}
    <div class="card border border-border rounded-xl overflow-hidden mb-4">
        <div class="flex items-center justify-between p-4 border-b border-border">
            <div>
                <h2 class="text-sm font-semibold text-foreground">Bid increment bands</h2>
                <p class="text-xs text-muted-foreground mt-0.5">Non-overlapping, sorted ascending. Used read-only during auctions.</p>
            </div>
            <button class="kt-btn kt-btn-mono kt-btn-sm" id="btn-add-band">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Add band
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="bands-table">
                <thead class="bg-muted/40">
                    <tr>
                        @foreach(['From £','To £','Increment £','Actions'] as $col)
                            <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide {{ $col === 'Actions' ? 'w-24' : '' }}">
                                {{ $col }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="bands-tbody" class="divide-y divide-border bg-background">
                    @php
                        $bands = $bands ?? [
                            ['id' => 1, 'from' => 0,      'to' => 1000,   'increment' => 50],
                            ['id' => 2, 'from' => 1000,   'to' => 5000,   'increment' => 100],
                            ['id' => 3, 'from' => 5000,   'to' => 10000,  'increment' => 250],
                            ['id' => 4, 'from' => 10000,  'to' => 25000,  'increment' => 500],
                            ['id' => 5, 'from' => 25000,  'to' => 50000,  'increment' => 1000],
                            ['id' => 6, 'from' => 50000,  'to' => 100000, 'increment' => 2500],
                            ['id' => 7, 'from' => 100000, 'to' => 999999, 'increment' => 5000],
                        ];
                    @endphp
                    @foreach($bands as $band)
                        <tr class="hover:bg-muted/20 band-row" data-id="{{ $band['id'] }}">
                            <td class="p-3">
                                <input type="number" name="bands[{{ $band['id'] }}][from]"
                                       class="kt-input w-28 band-from"
                                       value="{{ $band['from'] }}" min="0" />
                            </td>
                            <td class="p-3">
                                <input type="number" name="bands[{{ $band['id'] }}][to]"
                                       class="kt-input w-28 band-to"
                                       value="{{ $band['to'] }}" min="1" />
                            </td>
                            <td class="p-3">
                                <input type="number" name="bands[{{ $band['id'] }}][increment]"
                                       class="kt-input w-28 band-increment"
                                       value="{{ $band['increment'] }}" min="1" />
                            </td>
                            <td class="p-3">
                                <button type="button" class="kt-btn kt-btn-ghost kt-btn-xs text-destructive remove-band"
                                        data-id="{{ $band['id'] }}">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="band-validation-error" class="hidden mx-4 mb-3 rounded-lg bg-destructive/10 border border-destructive/30 p-3 text-xs text-destructive">
            <i data-lucide="alert-circle" class="w-3.5 h-3.5 inline mr-1"></i>
            <span id="band-error-text"></span>
        </div>

        <div class="p-4 border-t border-border flex justify-end">
            <button type="button" class="kt-btn kt-btn-mono" id="btn-save-bands">Save increment bands</button>
        </div>
    </div>

    {{-- Info callout --}}
    <div class="rounded-lg bg-muted/50 border border-border p-4 text-sm text-muted-foreground">
        <i data-lucide="info" class="w-4 h-4 inline mr-1.5 text-primary"></i>
        Bands must not overlap and must cover the full price range. The system will use the matching band's increment when validating bids.
    </div>

</div>

@push('scripts')
<script>
let bandCount = {{ count($bands ?? []) + 1 }};

document.getElementById('btn-add-band')?.addEventListener('click', () => {
    const tbody = document.getElementById('bands-tbody');
    const id = 'new-' + bandCount++;
    tbody.insertAdjacentHTML('beforeend', `
        <tr class="hover:bg-muted/20 band-row" data-id="${id}">
            <td class="p-3"><input type="number" name="bands[${id}][from]" class="kt-input w-28 band-from" value="0" min="0" /></td>
            <td class="p-3"><input type="number" name="bands[${id}][to]"   class="kt-input w-28 band-to"   value="0" min="1" /></td>
            <td class="p-3"><input type="number" name="bands[${id}][increment]" class="kt-input w-28 band-increment" value="100" min="1" /></td>
            <td class="p-3"><button type="button" class="kt-btn kt-btn-ghost kt-btn-xs text-destructive remove-band" data-id="${id}">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button></td>
        </tr>`);
    lucide.createIcons();
    attachRemoveBand();
});

function attachRemoveBand() {
    document.querySelectorAll('.remove-band').forEach(btn => {
        btn.onclick = () => {
            const row = document.querySelector(`.band-row[data-id="${btn.dataset.id}"]`);
            if(document.querySelectorAll('.band-row').length <= 1) {
                alert('At least one band is required.');
                return;
            }
            row?.remove();
        };
    });
}
attachRemoveBand();

document.getElementById('btn-save-bands')?.addEventListener('click', () => {
    // Basic overlap validation
    const rows = [...document.querySelectorAll('.band-row')];
    const bands = rows.map(r => ({
        from: parseInt(r.querySelector('.band-from').value),
        to:   parseInt(r.querySelector('.band-to').value),
        inc:  parseInt(r.querySelector('.band-increment').value),
    })).sort((a, b) => a.from - b.from);

    const errEl = document.getElementById('band-validation-error');
    const errText = document.getElementById('band-error-text');

    for(let i = 0; i < bands.length; i++) {
        if(bands[i].from >= bands[i].to) {
            errEl.classList.remove('hidden');
            errText.textContent = `Band ${i+1}: "From" must be less than "To".`;
            return;
        }
        if(i > 0 && bands[i].from !== bands[i-1].to) {
            errEl.classList.remove('hidden');
            errText.textContent = `Gap or overlap between bands ${i} and ${i+1}.`;
            return;
        }
    }
    errEl.classList.add('hidden');

    // Submit via AJAX or form
    alert('Bands saved! (Connect to backend route settings.auctions.bands)');
});
</script>
@endpush

@endsection
