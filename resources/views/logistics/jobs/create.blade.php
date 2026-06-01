{{-- resources/views/logistics/jobs/create.blade.php --}}
{{-- Phase 4 — L2: Create Transport Job --}}
@extends('layouts.app')
@section('title', 'Create Transport Job — Logistics')

@section('content')
<div class="kt-container-fixed">

<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
    <a href="{{ route('logistics.jobs.index') }}" class="hover:text-foreground">Jobs</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-foreground font-medium">New job</span>
</nav>

<div class="max-w-2xl">

    <div class="flex items-center gap-3 mb-5">
        <h1 class="text-xl font-semibold text-foreground">Create transport job</h1>
        @if (request('deal'))
            <span class="kt-badge kt-badge-info kt-badge-sm">
                Deal: {{ request('deal') }}
            </span>
        @endif
    </div>

    <form method="POST" action="{{ route('logistics.jobs.store') }}" id="create-job-form"
          class="space-y-5">
        @csrf

        @if (request('deal'))
            <input type="hidden" name="deal_id" value="{{ request('deal') }}" />
        @endif

        {{-- Deal & Vehicle --}}
        <div class="card border border-border rounded-xl p-5 space-y-4">
            <h3 class="text-sm font-semibold flex items-center gap-2">
                <i data-lucide="link" class="w-4 h-4 opacity-60"></i>Deal &amp; vehicle
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">Deal ref</label>
                    <input name="deal_ref" class="kt-input w-full"
                           value="{{ request('deal') }}"
                           placeholder="DEL-3112" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">VRM</label>
                    <input name="vrm" class="kt-input w-full" placeholder="AB19 CDE" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium mb-1">Vehicle</label>
                    <input name="vehicle_title" class="kt-input w-full"
                           placeholder="BMW 330i 2019" />
                </div>
            </div>
        </div>

        {{-- Route --}}
        <div class="card border border-border rounded-xl p-5 space-y-4">
            <h3 class="text-sm font-semibold flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 opacity-60"></i>Route
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">
                        Pickup address <span class="text-destructive">*</span>
                    </label>
                    <textarea name="pickup_address" class="kt-input w-full" rows="2"
                              required placeholder="Full address or postcode"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">
                        Drop address <span class="text-destructive">*</span>
                    </label>
                    <textarea name="drop_address" class="kt-input w-full" rows="2"
                              required placeholder="Full address or postcode"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Pickup contact</label>
                    <input name="pickup_contact" class="kt-input w-full"
                           placeholder="Name / phone" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Drop contact</label>
                    <input name="drop_contact" class="kt-input w-full"
                           placeholder="Name / phone" />
                </div>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="card border border-border rounded-xl p-5 space-y-4">
            <h3 class="text-sm font-semibold flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 opacity-60"></i>Schedule
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">
                        Date <span class="text-destructive">*</span>
                    </label>
                    <input name="slot_date" type="date" class="kt-input w-full"
                           required min="{{ now()->format('Y-m-d') }}" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Window</label>
                    <select name="slot_window" class="kt-input w-full">
                        <option value="AM">AM (08:00–12:00)</option>
                        <option value="PM">PM (12:00–18:00)</option>
                        <option value="Any">Any time</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Provider</label>
                    <select name="provider" class="kt-input w-full">
                        <option value="">Select / assign later</option>
                        @foreach ($providers ?? [] as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Owner & notes --}}
        <div class="card border border-border rounded-xl p-5 space-y-4">
            <h3 class="text-sm font-semibold flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 opacity-60"></i>Assignment &amp; notes
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1">Assigned to</label>
                    <select name="owner" class="kt-input w-full">
                        <option value="">Unassigned</option>
                        @foreach ($owners ?? [] as $o)
                            <option>{{ $o }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Tracking ref</label>
                    <input name="tracking_ref" class="kt-input w-full"
                           placeholder="Provider tracking code" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium mb-1">Notes</label>
                    <textarea name="notes" class="kt-input w-full" rows="2"
                              placeholder="Special instructions, access codes…"></textarea>
                </div>
            </div>
        </div>

        <div class="flex gap-3 justify-end">
            <a href="{{ route('logistics.jobs.index') }}" class="kt-btn kt-btn-ghost">Cancel</a>
            <button type="submit" class="kt-btn kt-btn-mono">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i>Create job
            </button>
        </div>
    </form>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>
@include('partials._phase4_js')

</div>
@endsection
