@extends('layouts.app')
@section('title','Compliance — DSAR')
@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">
                Data Subject Access Requests (DSAR)
            </h1>
            <p class="text-sm text-muted-foreground mt-1">
                Compliance → DSAR management queue
            </p>
        </div>

        <button class="kt-btn kt-btn-mono kt-btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#newDsarModal">
            New DSAR
        </button>
    </div>

    {{-- Queue --}}
    <div class="card border border-border rounded-xl mb-6">
        <div class="p-5 border-b border-border">
            <h3 class="font-semibold text-foreground">Queue</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-muted/40 text-muted-foreground text-xs uppercase">
                    <tr>
                        <th class="p-3 text-left">ID</th>
                        <th class="p-3 text-left">Subject</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Submitted</th>
                        <th class="p-3 text-left">SLA Due</th>
                        <th class="p-3 text-left">Owner</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($queue as $q)
                    <tr class="border-t border-border hover:bg-muted/20">
                        <td class="p-3 font-semibold">{{ $q['id'] }}</td>
                        <td class="p-3">{{ $q['subject'] }}</td>
                        <td class="p-3 text-muted-foreground">{{ $q['email'] }}</td>
                        <td class="p-3">{{ $q['submitted'] }}</td>
                        <td class="p-3">{{ $q['sla_due'] }}</td>
                        <td class="p-3">{{ $q['owner'] }}</td>
                        <td class="p-3">
                            <span class="kt-badge kt-badge-sm
                                {{ $q['status']==='In progress' ? 'kt-badge-warning' : 'kt-badge-primary' }}">
                                {{ $q['status'] }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <button class="kt-btn kt-btn-ghost kt-btn-sm">Export</button>
                            <button class="kt-btn kt-btn-outline kt-btn-sm">Complete</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bundles --}}
    <div class="card border border-border rounded-xl">
        <div class="p-5 border-b border-border">
            <h3 class="font-semibold text-foreground">Generated Bundles</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead class="bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="p-3 text-left">Bundle</th>
                        <th class="p-3 text-left">Subject</th>
                        <th class="p-3 text-left">Requested</th>
                        <th class="p-3 text-left">Generated</th>
                        <th class="p-3 text-left">Expires</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($generated as $g)
                    <tr class="border-t border-border hover:bg-muted/20">
                        <td class="p-3 font-semibold text-primary">
                            {{ $g['bundle'] }}
                        </td>
                        <td class="p-3">{{ $g['subject'] }}</td>
                        <td class="p-3">{{ $g['requested'] }}</td>
                        <td class="p-3">{{ $g['generated'] }}</td>
                        <td class="p-3">{{ $g['expires'] }}</td>
                        <td class="p-3 text-right">
                            <button class="kt-btn kt-btn-ghost kt-btn-sm">Download</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Modal --}}
<div id="newDsarModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-background rounded-xl border border-border w-full max-w-lg p-6">

        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-foreground">New DSAR Request</h2>

            <button class="text-muted-foreground hover:text-foreground"
                    data-bs-dismiss="modal">
                ✕
            </button>
        </div>

        <div class="space-y-4">
            <input class="kt-input w-full" placeholder="Subject Name">
            <input class="kt-input w-full" placeholder="Email">

            <select class="kt-input w-full">
                <option>Full data export</option>
                <option>Specific data query</option>
            </select>

            <textarea class="kt-input w-full" rows="3"
                      placeholder="Notes"></textarea>
        </div>

        <div class="flex justify-end gap-2 mt-5">
           <button type="button" class="kt-btn kt-btn-ghost" data-bs-dismiss="modal">
    Cancel
</button>
            <button class="kt-btn kt-btn-mono">Create</button>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('newDsarModal');
    const openBtn = document.querySelector('[data-bs-target="#newDsarModal"]');

    openBtn?.addEventListener('click', function () {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', function () {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    });

    modal?.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });

});
</script>
@endpush