{{-- resources/views/settings/branding.blade.php --}}
{{-- Phase 5 — S8: Settings → Branding --}}
@extends('layouts.app')
@section('title', 'Branding — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">
 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Branding</span>
    </nav>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Branding</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Core brand tokens for Admin and CRM shells</p>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.branding.update') }}" enctype="multipart/form-data">
        @csrf @method('PATCH')

        {{-- Logo --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <h2 class="text-sm font-semibold text-foreground mb-4">Logo</h2>
            <div class="flex items-start gap-6">
                <div class="flex flex-col gap-3">
                    <div class="w-32 h-16 rounded-lg border border-border bg-muted/30 flex items-center justify-center overflow-hidden">
                        @if($branding['logo_url'] ?? false)
                            <img src="{{ $branding['logo_url'] }}" alt="Logo" class="max-w-full max-h-full object-contain" />
                        @else
                            <i data-lucide="image" class="w-8 h-8 text-muted-foreground/30"></i>
                        @endif
                    </div>
                    <div class="w-32 h-16 rounded-lg border border-border bg-gray-900 flex items-center justify-center overflow-hidden">
                        @if($branding['logo_dark_url'] ?? false)
                            <img src="{{ $branding['logo_dark_url'] }}" alt="Logo dark" class="max-w-full max-h-full object-contain" />
                        @else
                            <i data-lucide="image" class="w-8 h-8 text-white/20"></i>
                        @endif
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1 font-medium">Light mode logo</label>
                        <input type="file" name="logo" class="kt-input w-full" accept="image/png,image/svg+xml,image/webp" />
                        <p class="text-xs text-muted-foreground mt-0.5">PNG, SVG, or WebP — recommended 200×60px</p>
                    </div>
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1 font-medium">Dark mode logo (optional)</label>
                        <input type="file" name="logo_dark" class="kt-input w-full" accept="image/png,image/svg+xml,image/webp" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Colour palette --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <h2 class="text-sm font-semibold text-foreground mb-4">Colour palette</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach([
                    ['primary',     'Primary',      $branding['primary']     ?? '#2563eb'],
                    ['primary_fg',  'Primary fg',   $branding['primary_fg']  ?? '#ffffff'],
                    ['success',     'Success',      $branding['success']     ?? '#16a34a'],
                    ['warning',     'Warning',      $branding['warning']     ?? '#d97706'],
                    ['destructive', 'Destructive',  $branding['destructive'] ?? '#dc2626'],
                    ['info',        'Info',         $branding['info']        ?? '#0284c7'],
                ] as [$key, $label, $value])
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1 font-medium">{{ $label }}</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="colours[{{ $key }}]" class="w-10 h-10 rounded-lg border border-border cursor-pointer p-0.5"
                                   value="{{ $value }}" id="colour-{{ $key }}" />
                            <input type="text" class="kt-input flex-1 font-mono text-xs colour-hex"
                                   data-for="{{ $key }}" value="{{ $value }}" maxlength="7" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Typography --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <h2 class="text-sm font-semibold text-foreground mb-4">Typography</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Body font</label>
                    <select name="font_body" class="kt-input w-full">
                        @foreach(['Inter','DM Sans','Plus Jakarta Sans','Outfit','Poppins','Geist'] as $font)
                            <option value="{{ $font }}" @selected(($branding['font_body'] ?? 'Inter') === $font)>{{ $font }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Mono / code font</label>
                    <select name="font_mono" class="kt-input w-full">
                        @foreach(['JetBrains Mono','Fira Code','Source Code Pro','Geist Mono','Roboto Mono'] as $font)
                            <option value="{{ $font }}" @selected(($branding['font_mono'] ?? 'JetBrains Mono') === $font)>{{ $font }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Base font size (px)</label>
                    <input type="number" name="font_size_base" class="kt-input w-full"
                           value="{{ $branding['font_size_base'] ?? 14 }}" min="12" max="18" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Line height</label>
                    <input type="number" name="line_height" class="kt-input w-full"
                           value="{{ $branding['line_height'] ?? 1.5 }}" min="1" max="2.5" step="0.1" />
                </div>
            </div>
        </div>

        {{-- Radius & shadows --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <h2 class="text-sm font-semibold text-foreground mb-4">Radius & shadows</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Button border radius</label>
                    <div class="flex items-center gap-2">
                        <input type="range" name="btn_radius" class="flex-1" min="0" max="24" step="1"
                               value="{{ $branding['btn_radius'] ?? 8 }}" id="btn-radius-range"
                               oninput="document.getElementById('btn-radius-val').textContent = this.value + 'px'" />
                        <span class="text-sm text-muted-foreground w-10 text-right" id="btn-radius-val">
                            {{ $branding['btn_radius'] ?? 8 }}px
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Card border radius</label>
                    <div class="flex items-center gap-2">
                        <input type="range" name="card_radius" class="flex-1" min="0" max="24" step="1"
                               value="{{ $branding['card_radius'] ?? 12 }}" id="card-radius-range"
                               oninput="document.getElementById('card-radius-val').textContent = this.value + 'px'" />
                        <span class="text-sm text-muted-foreground w-10 text-right" id="card-radius-val">
                            {{ $branding['card_radius'] ?? 12 }}px
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Shadow style</label>
                    <select name="shadow_style" class="kt-input w-full">
                        <option value="none"   @selected(($branding['shadow_style'] ?? 'sm') === 'none')>None</option>
                        <option value="sm"     @selected(($branding['shadow_style'] ?? 'sm') === 'sm')>Subtle (sm)</option>
                        <option value="md"     @selected(($branding['shadow_style'] ?? 'sm') === 'md')>Medium</option>
                        <option value="lg"     @selected(($branding['shadow_style'] ?? 'sm') === 'lg')>Pronounced (lg)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Preview button --}}
        <div class="flex items-center justify-between mb-4">
            <button type="button" class="kt-btn kt-btn-outline" id="btn-preview-brand">
                <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Preview theme
            </button>
            <button type="submit" class="kt-btn kt-btn-mono">Save branding</button>
        </div>
    </form>

</div>

@push('scripts')
<script>
// Sync colour picker ↔ hex input
document.querySelectorAll('.colour-hex').forEach(hex => {
    const picker = document.getElementById('colour-' + hex.dataset.for);
    if(!picker) return;
    hex.addEventListener('input', () => { if(/^#[0-9a-f]{6}$/i.test(hex.value)) picker.value = hex.value; });
    picker.addEventListener('input', () => { hex.value = picker.value; });
});
</script>
@endpush

@endsection
