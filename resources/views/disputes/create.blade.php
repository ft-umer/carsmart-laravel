{{-- resources/views/disputes/create.blade.php --}}
{{-- Phase 4 — Create dispute case (form) --}}
@extends('layouts.app')
@section('title', 'Open Dispute — Carsmart')

@section('content')
<div class="kt-container-fixed">

{{-- Breadcrumb --}}
<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
    <a href="{{ route('disputes.index') }}" class="hover:text-foreground">Disputes</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-foreground font-medium">New case</span>
</nav>

<div class="max-w-2xl mx-auto">

    <div class="card border border-border rounded-xl overflow-hidden">

        <div class="px-6 py-4 border-b border-border bg-muted/20">
            <h1 class="text-base font-semibold flex items-center gap-2">
                <i data-lucide="alert-octagon" class="w-4 h-4"></i> Open dispute case
            </h1>
            <p class="text-xs text-muted-foreground mt-1">
                Raise a dispute against a deal. This creates a case in the Disputes queue with
                SLA timers and notifies the relevant parties.
            </p>
        </div>

        @if ($errors->any())
            <div class="px-6 pt-4">
                <div class="rounded-lg border border-destructive/40 bg-red-50 dark:bg-red-900/20
                            text-destructive text-sm px-3 py-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-start gap-2">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5 mt-0.5 shrink-0"></i>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('disputes.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium mb-1">
                    Deal reference <span class="text-destructive">*</span>
                </label>
                <input type="text" name="deal_id" class="kt-input w-full font-mono"
                       value="{{ old('deal_id', $deal_ref ?? '') }}"
                       placeholder="e.g. DEL-3112" required />
                <p class="text-xs text-muted-foreground mt-1">
                    The deal this dispute relates to.
                </p>
            </div>

            <div>
                <label class="block text-xs font-medium mb-2">
                    Source <span class="text-destructive">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ([
                        ['value' => 'Seller objection', 'desc' => 'Seller disputes terms, price, or condition findings'],
                        ['value' => 'Post-handover',    'desc' => 'Issue raised after the vehicle has changed hands'],
                    ] as $opt)
                        <label class="flex items-start gap-3 px-3 py-2.5 rounded-lg border border-border
                                      cursor-pointer hover:bg-muted/30 transition-colors
                                      has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                            <input type="radio" name="source" value="{{ $opt['value'] }}"
                                   class="form-radio mt-0.5 shrink-0"
                                   @checked(old('source') === $opt['value']) required />
                            <div>
                                <div class="text-sm font-medium">{{ $opt['value'] }}</div>
                                <div class="text-xs text-muted-foreground">{{ $opt['desc'] }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">
                    Reason <span class="text-destructive">*</span>
                </label>
                <textarea name="reason" class="kt-input w-full" rows="4" maxlength="1000"
                          placeholder="Describe the issue in detail…" required>{{ old('reason') }}</textarea>
                <p class="text-xs text-muted-foreground mt-1">Up to 1000 characters.</p>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Owner</label>
                <select name="owner" class="kt-input w-full">
                    <option value="">Unassigned</option>
                    @foreach ($owners ?? [] as $o)
                        <option value="{{ $o }}" @selected(old('owner') === $o)>{{ $o }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-muted-foreground mt-1">
                    Can be assigned later from the case detail page.
                </p>
            </div>

            <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200
                        dark:border-amber-700 p-3 text-xs text-amber-800 dark:text-amber-300">
                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                Opening a case starts the acknowledgement SLA (24h) and decision SLA (5 business days).
            </div>

            <div class="flex gap-2 justify-end pt-2 border-t border-border">
                <a href="{{ url()->previous() }}" class="kt-btn kt-btn-ghost">Cancel</a>
                <button type="submit" class="kt-btn kt-btn-mono">
                    <i data-lucide="alert-octagon" class="w-4 h-4 mr-1"></i> Open case
                </button>
            </div>
        </form>

    </div>

</div>

</div>
@endsection