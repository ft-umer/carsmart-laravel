{{-- resources/views/editions/features.blade.php --}}
@extends('layouts.app')
@section('title','Editions — Features Schedule')

@section('content')

<div class="kt-container-fixed">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <a href="{{ route('editions.dashboard') }}" class="hover:text-foreground transition-colors">Editions</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">Features Schedule</span>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Features Schedule</h1>
            <p class="text-sm text-muted-foreground mt-0.5">
                Manage scheduled edition features and publishing slots
            </p>
        </div>

        <button id="btn-add-feature" class="kt-btn kt-btn-mono">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
            Add Feature
        </button>
    </div>

    {{-- Table Card --}}
    <div class="card border border-border rounded-xl overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-sm">

                <thead class="bg-muted/40 text-muted-foreground text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Slot</th>
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Listing</th>
                        <th class="p-3 text-left">Channels</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @php
                        $sc = [
                            'Scheduled' => 'info',
                            'Draft' => 'secondary',
                            'Published' => 'success'
                        ];
                    @endphp

                    @forelse($slots as $slot)
                        <tr class="hover:bg-muted/30 transition-colors">

                            <td class="p-3">
                                <span class="kt-badge kt-badge-outline kt-badge-xs">
                                    {{ $slot['slot'] }}
                                </span>
                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $slot['date'] }}
                            </td>

                            <td class="p-3 font-medium text-foreground">
                                {{ $slot['listing'] }}
                            </td>

                            <td class="p-3 text-muted-foreground">
                              {{ is_array($slot['channels']) ? implode(', ', $slot['channels']) : $slot['channels'] }}
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $sc[$slot['status']] ?? 'secondary' }} kt-badge-xs">
                                    {{ $slot['status'] }}
                                </span>
                            </td>

                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-1">

                                    <button class="kt-btn kt-btn-ghost kt-btn-xs">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </button>

                                    <button class="kt-btn kt-btn-mono kt-btn-xs">
                                        Publish
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-muted-foreground">
                                No feature slots available
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>

</div>

{{-- Modal --}}
<div id="modal-feature" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">

    <div class="bg-background rounded-xl shadow-xl w-full max-w-lg p-6">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">Add Feature Slot</h2>

            <button class="feature-modal-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="space-y-4">

            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">
                    Listing ID
                </label>
                <input type="text" class="kt-input w-full" placeholder="LST-XXXX">
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">
                    Feature Date & Time
                </label>
                <input type="datetime-local" class="kt-input w-full">
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">
                    Channels
                </label>

                <div class="flex gap-4">
                    <label class="flex items-center gap-1.5 text-sm">
                        <input type="checkbox" class="kt-checkbox" checked>
                        Web
                    </label>

                    <label class="flex items-center gap-1.5 text-sm">
                        <input type="checkbox" class="kt-checkbox">
                        Social
                    </label>
                </div>
            </div>

        </div>

        <div class="flex justify-end gap-2 mt-5">

            <button class="feature-modal-close kt-btn kt-btn-ghost">
                Cancel
            </button>

            <button class="kt-btn kt-btn-outline">
                Save
            </button>

            <button class="kt-btn kt-btn-mono">
                Publish
            </button>

        </div>

    </div>

</div>

{{-- Scripts --}}
@push('scripts')
<script>
// open modal
document.getElementById('btn-add-feature')?.addEventListener('click', () => {
    document.getElementById('modal-feature').classList.remove('hidden');
    document.getElementById('modal-feature').classList.add('flex');
});

// close modal
document.querySelectorAll('.feature-modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
        const modal = document.getElementById('modal-feature');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
});
</script>
@endpush

@endsection