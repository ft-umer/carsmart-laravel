@extends('layouts.app')
@section('title','Audit Log Viewer')

@section('content')

<div class="kt-container-fixed">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">
            Home
        </a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-foreground font-medium">Audit Logs</span>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">

        <div>
            <h1 class="text-xl font-semibold text-foreground">Audit Log Viewer</h1>
            <p class="text-sm text-muted-foreground mt-0.5">
                Track system activity, changes, and security events
            </p>
        </div>

        <div class="flex gap-2">

            <button class="kt-btn kt-btn-outline kt-btn-sm">
                <i data-lucide="download" class="w-4 h-4 mr-1"></i>
                Export CSV
            </button>

            <button class="kt-btn kt-btn-mono kt-btn-sm">
                <i data-lucide="shield-x" class="w-4 h-4 mr-1"></i>
                Create Case
            </button>

        </div>

    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-2 mb-4">

        <select class="kt-input kt-input-sm w-[160px]">
            <option>All actors</option>
            <option>Admin</option>
            <option>System</option>
        </select>

        <select class="kt-input kt-input-sm w-[160px]">
            <option>All objects</option>
            <option>Listing</option>
            <option>Auction</option>
            <option>Person</option>
            <option>Wallet</option>
            <option>User</option>
        </select>

        <select class="kt-input kt-input-sm w-[160px]">
            <option>All results</option>
            <option>Success</option>
            <option>Failure</option>
        </select>

        <input type="date" class="kt-input kt-input-sm w-[160px]">

    </div>

    {{-- Table --}}
    <div class="card border border-border rounded-xl overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1000px] text-sm">

                <thead class="bg-muted/40 text-muted-foreground text-xs uppercase tracking-wide">
                    <tr>
                        <th class="p-3 text-left">Time</th>
                        <th class="p-3 text-left">Actor</th>
                        <th class="p-3 text-left">Object</th>
                        <th class="p-3 text-left">Action</th>
                        <th class="p-3 text-left">Summary</th>
                        <th class="p-3 text-left">Result</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @forelse($entries as $e)

                        <tr class="hover:bg-muted/30 transition-colors">

                            <td class="p-3 text-muted-foreground text-xs">
                                {{ $e['time'] }}
                            </td>

                            <td class="p-3 font-medium text-foreground">
                                {{ $e['actor'] }}
                            </td>

                            <td class="p-3">
                                <a href="#" class="text-primary font-medium">
                                    {{ $e['object'] }}
                                </a>
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-secondary kt-badge-xs">
                                    {{ $e['action'] }}
                                </span>
                            </td>

                            <td class="p-3 text-muted-foreground text-xs">
                                {{ $e['summary'] }}
                            </td>

                            <td class="p-3">
                                <span class="kt-badge kt-badge-{{ $e['result'] === 'Success' ? 'success' : 'danger' }} kt-badge-xs">
                                    {{ $e['result'] }}
                                </span>
                            </td>

                            <td class="p-3 text-right">

                                <button
                                    class="kt-btn kt-btn-outline kt-btn-xs"
                                    onclick="openAuditDiffModal()">
                                    View diff
                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="p-10 text-center text-muted-foreground">
                                No audit logs found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- Diff Modal --}}
<div id="audit-diff-modal"
     class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">

    <div class="bg-background rounded-xl shadow-xl w-full max-w-4xl p-6">

        <div class="flex items-center justify-between mb-4">

            <h2 class="text-lg font-semibold text-foreground">
                Audit Entry — Diff View
            </h2>

            <button class="modal-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Before --}}
            <div class="border border-red-200 bg-red-50 rounded-lg p-4">

                <h3 class="text-sm font-semibold text-red-600 mb-2">
                    Before
                </h3>

                <pre class="text-xs text-red-700 font-mono">
reserve_price: £0
state: draft
                </pre>

            </div>

            {{-- After --}}
            <div class="border border-green-200 bg-green-50 rounded-lg p-4">

                <h3 class="text-sm font-semibold text-green-600 mb-2">
                    After
                </h3>

                <pre class="text-xs text-green-700 font-mono">
reserve_price: £12,500
state: draft
                </pre>

            </div>

        </div>

        <div class="flex justify-end gap-2 mt-5">

            <button class="kt-btn kt-btn-mono">
                Create Case
            </button>

            <button class="modal-close kt-btn kt-btn-ghost">
                Close
            </button>

        </div>

    </div>

</div>

{{-- Scripts --}}
@push('scripts')
<script>
function openAuditDiffModal() {
    const modal = document.getElementById('audit-diff-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
        const modal = document.getElementById('audit-diff-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
});
</script>
@endpush

@endsection