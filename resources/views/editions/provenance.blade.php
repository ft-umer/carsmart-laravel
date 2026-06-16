@extends('layouts.app')
@section('title','Editions — Provenance')

@section('content')

<div class="kt-container-fixed">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('editions.dashboard') }}" class="hover:text-foreground transition-colors">
            Editions
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">Provenance</span>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">

        <div>
            <h1 class="text-xl font-semibold text-foreground">Provenance</h1>
            <p class="text-sm text-muted-foreground mt-0.5">
                Manage ownership documents and verification history
            </p>
        </div>

        <button id="btn-add-doc" class="kt-btn kt-btn-mono">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
            Add Document
        </button>

    </div>

    {{-- Table --}}
    <div class="card border border-border rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-muted/40 text-muted-foreground text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Document Type</th>
                        <th class="p-3 text-left">Listing</th>
                        <th class="p-3 text-left">File</th>
                        <th class="p-3 text-left">Verified By</th>
                        <th class="p-3 text-left">Verified On</th>
                        <th class="p-3 text-left">Notes</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @forelse($docs as $doc)
                        <tr class="hover:bg-muted/30 transition-colors">

                            <td class="p-3 font-medium text-foreground">
                                {{ $doc['type'] }}
                            </td>

                            <td class="p-3 font-medium text-foreground">
                                {{ $doc['listing'] }}
                            </td>

                            <td class="p-3">

                                @if(!empty($doc['file']))
                                    <a href="#"
                                       class="text-primary text-sm flex items-center gap-1">
                                        <i data-lucide="file-down" class="w-4 h-4"></i>
                                        {{ $doc['file'] }}
                                    </a>
                                @else
                                    <span class="text-muted-foreground">—</span>
                                @endif

                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $doc['verified_by'] ?? '—' }}
                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $doc['verified_on'] ?? '—' }}
                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $doc['notes'] ?: '—' }}
                            </td>

                            <td class="p-3 text-right">
                                <button class="kt-btn kt-btn-outline kt-btn-xs">
                                    Upload
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-muted-foreground">
                                No provenance documents available
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- Modal --}}
<div id="modal-doc"
     class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">

    <div class="bg-background rounded-xl shadow-xl w-full max-w-lg p-6">

        <div class="flex items-center justify-between mb-4">

            <h2 class="text-lg font-semibold text-foreground">
                Add Provenance Document
            </h2>

            <button class="modal-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

        </div>

        <div class="space-y-4">

            <div>
                <label class="block text-xs text-muted-foreground mb-1">
                    Listing
                </label>
                <input type="text" class="kt-input w-full" placeholder="LST-XXXX">
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1">
                    Document Type
                </label>
                <select class="kt-input w-full">
                    <option>Original purchase invoice</option>
                    <option>Service history</option>
                    <option>Build sheet</option>
                    <option>Ownership certificate</option>
                    <option>Race / event history</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1">
                    File
                </label>
                <input type="file" class="kt-input w-full">
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1">
                    Notes
                </label>
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
                Save
            </button>

        </div>

    </div>

</div>

{{-- Scripts --}}
@push('scripts')
<script>
document.getElementById('btn-add-doc')?.addEventListener('click', () => {
    const modal = document.getElementById('modal-doc');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
});

document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
        const modal = document.getElementById('modal-doc');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
});
</script>
@endpush

@endsection