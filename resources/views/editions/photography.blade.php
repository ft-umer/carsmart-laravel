@extends('layouts.app')
@section('title','Editions — Photography & Assets')

@section('content')

<div class="kt-container-fixed">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('editions.dashboard') }}" class="hover:text-foreground transition-colors">
            Editions
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">Photography & Assets</span>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">

        <div>
            <h1 class="text-xl font-semibold text-foreground">Photography & Assets</h1>
            <p class="text-sm text-muted-foreground mt-0.5">
                Manage shoots, providers, and asset delivery
            </p>
        </div>

        <button id="btn-book-shoot" class="kt-btn kt-btn-mono">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
            Book Shoot
        </button>

    </div>

    {{-- Table --}}
    <div class="card border border-border rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-muted/40 text-muted-foreground text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Job ID</th>
                        <th class="p-3 text-left">Listing</th>
                        <th class="p-3 text-left">Vehicle</th>
                        <th class="p-3 text-left">Provider</th>
                        <th class="p-3 text-left">Slot</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @php
                        $statusColors = [
                            'Delivered' => 'success',
                            'Scheduled' => 'warning',
                            'Cancelled' => 'danger'
                        ];
                    @endphp

                    @forelse($jobs as $job)
                        <tr class="hover:bg-muted/30 transition-colors">

                            <td class="p-3 font-medium text-foreground">
                                {{ $job['id'] }}
                            </td>

                            <td class="p-3 font-medium text-foreground">
                                {{ $job['listing'] }}
                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $job['vehicle'] }}
                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $job['provider'] }}
                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $job['slot'] }}
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $statusColors[$job['status']] ?? 'secondary' }} kt-badge-xs">
                                    {{ $job['status'] }}
                                </span>
                            </td>

                            <td class="p-3 text-right">
                                <button class="kt-btn kt-btn-outline kt-btn-xs">
                                    View assets
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-muted-foreground">
                                No photography jobs found
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- Modal --}}
<div id="modal-book-shoot"
     class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">

    <div class="bg-background rounded-xl shadow-xl w-full max-w-lg p-6">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">
                Book Photography Shoot
            </h2>

            <button class="modal-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="space-y-4">

            <div>
                <label class="block text-xs text-muted-foreground mb-1">Listing</label>
                <input type="text" class="kt-input w-full" placeholder="LST-XXXX">
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1">Provider</label>
                <select class="kt-input w-full">
                    <option>Studio9</option>
                    <option>MotionArt</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1">Slot</label>
                <input type="datetime-local" class="kt-input w-full">
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1">Notes</label>
                <textarea class="kt-input w-full" rows="3"></textarea>
            </div>

        </div>

        <div class="flex justify-end gap-2 mt-5">

            <button class="modal-close kt-btn kt-btn-ghost">
                Cancel
            </button>

            <button class="kt-btn kt-btn-outline">
                Save Draft
            </button>

            <button class="kt-btn kt-btn-mono">
                Book
            </button>

        </div>

    </div>

</div>

{{-- Scripts --}}
@push('scripts')
<script>
document.getElementById('btn-book-shoot')?.addEventListener('click', () => {
    const modal = document.getElementById('modal-book-shoot');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
});

document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
        const modal = document.getElementById('modal-book-shoot');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
});
</script>
@endpush

@endsection