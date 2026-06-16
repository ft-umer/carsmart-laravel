@extends('layouts.app')
@section('title','Editions — Submissions')

@section('content')

<div class="kt-container-fixed">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('editions.dashboard') }}" class="hover:text-foreground transition-colors">Editions</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">Submissions</span>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Submissions</h1>
            <p class="text-sm text-muted-foreground mt-0.5">
                Review and manage incoming edition submissions
            </p>
        </div>

        <button id="btn-new-submission" class="kt-btn kt-btn-mono">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i>
            New Submission
        </button>
    </div>

    {{-- Table --}}
    <div class="card border border-border rounded-xl overflow-hidden">

        {{-- Filters --}}
        <div class="p-4 border-b border-border flex flex-col md:flex-row gap-2 md:items-center md:justify-between">

            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-2.5 text-muted-foreground"></i>
                <input type="text"
                       class="kt-input pl-9 w-64"
                       placeholder="Search submissions…">
            </div>

            <div class="flex gap-2 flex-wrap">

                <select class="kt-input kt-input-sm w-40">
                    <option>All sources</option>
                    <option>Internal</option>
                    <option>Vendor</option>
                    <option>Partner</option>
                </select>

                <select class="kt-input kt-input-sm w-40">
                    <option>All statuses</option>
                    <option>New</option>
                    <option>In review</option>
                    <option>Converted</option>
                    <option>Rejected</option>
                </select>

            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] text-sm">

                <thead class="bg-muted/40 text-muted-foreground text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Source</th>
                        <th class="p-3 text-left">Vehicle</th>
                        <th class="p-3 text-left">Rarity Flags</th>
                        <th class="p-3 text-left">Curator</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @php
                        $colors = [
                            'New' => 'primary',
                            'In review' => 'warning',
                            'Converted' => 'success',
                            'Rejected' => 'danger'
                        ];
                    @endphp

                    @forelse($items as $item)
                        <tr class="hover:bg-muted/30 transition-colors">

                            <td class="p-3 font-medium text-foreground">
                                {{ $item['id'] }}
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-outline kt-badge-xs">
                                    {{ $item['source'] }}
                                </span>
                            </td>

                            <td class="p-3 font-medium text-foreground">
                                {{ $item['vehicle'] }}
                            </td>

                            <td class="p-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item['flags'] ?? [] as $flag)
                                        <span class="kt-badge kt-badge-warning kt-badge-xs">
                                            {{ $flag }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <td class="p-3 text-muted-foreground">
                                {{ $item['curator'] }}
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $colors[$item['status']] ?? 'secondary' }} kt-badge-xs">
                                    {{ $item['status'] }}
                                </span>
                            </td>

                            <td class="p-3 text-right">
                                <a href="{{ route('editions.curation') }}"
                                   class="kt-btn kt-btn-outline kt-btn-xs">
                                    Review
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-muted-foreground">
                                No submissions found
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- Modal --}}
<div id="modal-submission"
     class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">

    <div class="bg-background rounded-xl shadow-xl w-full max-w-xl p-6">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">
                New Submission
            </h2>

            <button class="modal-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="space-y-4">

            <div>
                <label class="block text-xs text-muted-foreground mb-1">Source</label>
                <select class="kt-input w-full">
                    <option>Internal</option>
                    <option>Vendor</option>
                    <option>Partner</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1">
                    Listing Reference
                </label>
                <input type="text" class="kt-input w-full" placeholder="LST-XXXX">
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1">
                    Vehicle
                </label>
                <input type="text" class="kt-input w-full" placeholder="Make, Model, Year">
            </div>

            <div>
                <label class="block text-xs text-muted-foreground mb-1">
                    Rarity Flags
                </label>

                <div class="flex flex-wrap gap-2">
                    @foreach(['Low mileage','Special spec','Limited run','Historic interest','1-of-1 build'] as $f)
                        <label class="flex items-center gap-1.5 text-sm">
                            <input type="checkbox" class="kt-checkbox">
                            {{ $f }}
                        </label>
                    @endforeach
                </div>
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
                Submit
            </button>

        </div>

    </div>

</div>

{{-- Scripts --}}
@push('scripts')
<script>
document.getElementById('btn-new-submission')?.addEventListener('click', () => {
    document.getElementById('modal-submission').classList.remove('hidden');
    document.getElementById('modal-submission').classList.add('flex');
});

document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
        const modal = document.getElementById('modal-submission');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
});
</script>
@endpush

@endsection