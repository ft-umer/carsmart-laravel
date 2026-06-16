@extends('layouts.app')
@section('title','Compliance — Right to Erasure')
@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">
                Right to Erasure
            </h1>
            <p class="text-sm text-muted-foreground mt-1">
                Compliance → Erasure requests management
            </p>
        </div>

        <button id="open-erasure-modal"
                class="kt-btn kt-btn-mono kt-btn-sm">
            New Request
        </button>
    </div>

    {{-- Warning --}}
    <div class="card border border-border rounded-xl mb-6">
        <div class="p-4 text-warning text-sm">
            ⚠ Erasure actions are irreversible. All requests require approval before execution.
        </div>
    </div>

    {{-- Table --}}
    <div class="card border border-border rounded-xl">
        <div class="p-5 border-b border-border">
            <h3 class="font-semibold text-foreground">Queue</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1000px]">

                <thead class="bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Subject</th>
                        <th class="p-3 text-left">Reason</th>
                        <th class="p-3 text-left">Submitted</th>
                        <th class="p-3 text-left">Owner</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($queue as $q)
                    <tr class="border-t border-border hover:bg-muted/20">

                        <td class="p-3 font-semibold">
                            {{ $q['id'] }}
                        </td>

                        <td class="p-3">
                            {{ $q['subject'] }}
                        </td>

                        <td class="p-3">
                            {{ $q['reason'] }}
                        </td>

                        <td class="p-3 text-muted-foreground">
                            {{ $q['submitted'] }}
                        </td>

                        <td class="p-3">
                            {{ $q['owner'] }}
                        </td>

                        <td class="p-3">
                            <span class="kt-badge kt-badge-sm
                                {{ $q['status']==='Review' ? 'kt-badge-warning' : 'kt-badge-primary' }}">
                                {{ $q['status'] }}
                            </span>
                        </td>

                        <td class="p-3 text-right">
                            <button class="kt-btn kt-btn-ghost kt-btn-sm">
                                Preview
                            </button>
                            <button class="kt-btn kt-btn-danger kt-btn-sm">
                                Execute
                            </button>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>

{{-- REDACTION MODAL --}}
<div id="redactionPreviewModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-background rounded-xl border border-border w-full max-w-4xl p-6">

        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-foreground">Redaction Preview</h2>

            <button class="text-muted-foreground hover:text-foreground"
                    data-bs-dismiss="modal">✕</button>
        </div>

        <p class="text-sm text-muted-foreground mb-4">
            The following fields will be redacted before execution.
        </p>

        <table class="w-full text-sm border border-border rounded-lg overflow-hidden">

            <thead class="bg-muted/40 text-xs uppercase text-muted-foreground">
                <tr>
                    <th class="p-3 text-left">Object</th>
                    <th class="p-3 text-left">Field</th>
                    <th class="p-3 text-left">Current</th>
                    <th class="p-3 text-left">After</th>
                </tr>
            </thead>

            <tbody>
                <tr class="border-t border-border">
                    <td class="p-3">Person</td>
                    <td class="p-3">email</td>
                    <td class="p-3">jane@example.com</td>
                    <td class="p-3 text-danger">[REDACTED]</td>
                </tr>
                <tr class="border-t border-border">
                    <td class="p-3">Person</td>
                    <td class="p-3">phone</td>
                    <td class="p-3">07700 900123</td>
                    <td class="p-3 text-danger">[REDACTED]</td>
                </tr>
            </tbody>

        </table>

        <div class="flex justify-end gap-2 mt-5">
            <button class="kt-btn kt-btn-ghost" data-bs-dismiss="modal">
                Close
            </button>
            <button class="kt-btn kt-btn-danger">
                Confirm Erasure
            </button>
        </div>

    </div>
</div>

{{-- NEW REQUEST MODAL --}}
<div id="newErasureModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-background rounded-xl border border-border w-full max-w-lg p-6">

        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-foreground">New Erasure Request</h2>

            <button class="text-muted-foreground hover:text-foreground"
                    data-bs-dismiss="modal">✕</button>
        </div>

        <div class="space-y-4">
            <input class="kt-input w-full" placeholder="Subject Name or ID">

            <select class="kt-input w-full">
                <option>Customer request</option>
                <option>Account closure</option>
                <option>Legal obligation</option>
            </select>

            <textarea class="kt-input w-full" rows="3"
                      placeholder="Notes"></textarea>
        </div>

        <div class="flex justify-end gap-2 mt-5">
            <button class="kt-btn kt-btn-ghost" data-bs-dismiss="modal">
                Cancel
            </button>
            <button class="kt-btn kt-btn-mono">
                Submit
            </button>
        </div>

    </div>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const erasureModal = document.getElementById('newErasureModal');
    const redactionModal = document.getElementById('redactionPreviewModal');
    const openBtn = document.getElementById('open-erasure-modal');

    // OPEN new request modal
    openBtn?.addEventListener('click', () => {
        erasureModal.classList.remove('hidden');
        erasureModal.classList.add('flex');
    });

    // CLOSE any modal
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', () => {
            erasureModal.classList.add('hidden');
            erasureModal.classList.remove('flex');

            redactionModal.classList.add('hidden');
            redactionModal.classList.remove('flex');
        });
    });

    // click outside close
    [erasureModal, redactionModal].forEach(modal => {
        modal?.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    });

});
</script>
@endpush