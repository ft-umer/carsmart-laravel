{{-- C2. Customer Relationship Management → My work → Inbox
     Purpose: Single place for advisors to work all assigned items against service level targets.
     Who: Customer Relationship Management roles.
--}}
@extends('layouts.app')

@section('title', 'My work — Inbox')

@section('content')
<div class="kt-container-fixed py-6 flex flex-col gap-5">

    {{-- Page heading --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-mono">My work</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Manage your assigned items and meet service level targets.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="kt-btn kt-btn-outline kt-btn-sm" data-kt-modal-toggle="#compose_email_modal">
                <i class="ki-filled ki-sms text-sm me-1.5"></i>
                Send email
            </button>
            <button class="kt-btn kt-btn-primary kt-btn-sm" data-kt-modal-toggle="#compose_sms_modal">
                <i class="ki-filled ki-message-notify text-sm me-1.5"></i>
                Send Short Message Service
            </button>
        </div>
    </div>

    {{-- C2. Tabs: Inbox | Tasks | Service level targets --}}
    <div class="kt-tabs kt-tabs-line" data-kt-tabs="true">
        <div class="flex items-center gap-6 border-b border-border">
            <button class="kt-tab-toggle py-3 text-sm font-medium active" data-kt-tab-toggle="#crm_tab_inbox">
                Inbox
                @if(($inboxCount ?? 0) > 0)
                    <span class="kt-badge kt-badge-primary ms-2 text-xs">{{ $inboxCount ?? 0 }}</span>
                @endif
            </button>
            <button class="kt-tab-toggle py-3 text-sm font-medium" data-kt-tab-toggle="#crm_tab_tasks">
                Tasks
                @if(($tasksCount ?? 0) > 0)
                    <span class="kt-badge kt-badge-warning ms-2 text-xs">{{ $tasksCount ?? 0 }}</span>
                @endif
            </button>
            <button class="kt-tab-toggle py-3 text-sm font-medium" data-kt-tab-toggle="#crm_tab_slt">
                Service level targets
            </button>
        </div>
    </div>

    {{-- ── TAB: Inbox ── --}}
    <div id="crm_tab_inbox">

        {{-- Filters row --}}
        <div class="flex flex-wrap items-center gap-2">
            <div class="kt-menu" data-kt-menu="true">
                <div class="kt-menu-item" data-kt-menu-item-offset="0,10px" data-kt-menu-item-placement="bottom-start"
                     data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="click|lg:hover">
                    <button class="kt-menu-toggle kt-btn kt-btn-outline kt-btn-sm flex items-center gap-1.5">
                        <i class="ki-filled ki-filter text-sm"></i> Type <i class="ki-filled ki-down text-xs"></i>
                    </button>
                    <div class="kt-menu-dropdown kt-menu-default w-[160px]" data-kt-menu-dismiss="true">
                        @foreach(['All types','Lead','Listing','Customer','Vendor'] as $type)
                            <div class="kt-menu-item"><a class="kt-menu-link" href="#"><span class="kt-menu-title">{{ $type }}</span></a></div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="kt-menu" data-kt-menu="true">
                <div class="kt-menu-item" data-kt-menu-item-offset="0,10px" data-kt-menu-item-placement="bottom-start"
                     data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="click|lg:hover">
                    <button class="kt-menu-toggle kt-btn kt-btn-outline kt-btn-sm flex items-center gap-1.5">
                        Owner <i class="ki-filled ki-down text-xs"></i>
                    </button>
                    <div class="kt-menu-dropdown kt-menu-default w-[160px]" data-kt-menu-dismiss="true">
                        <div class="kt-menu-item"><a class="kt-menu-link" href="#"><span class="kt-menu-title">All owners</span></a></div>
                        <div class="kt-menu-item"><a class="kt-menu-link" href="#"><span class="kt-menu-title">Assigned to me</span></a></div>
                        <div class="kt-menu-item"><a class="kt-menu-link" href="#"><span class="kt-menu-title">Unassigned</span></a></div>
                    </div>
                </div>
            </div>
            <div class="kt-menu" data-kt-menu="true">
                <div class="kt-menu-item" data-kt-menu-item-offset="0,10px" data-kt-menu-item-placement="bottom-start"
                     data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="click|lg:hover">
                    <button class="kt-menu-toggle kt-btn kt-btn-outline kt-btn-sm flex items-center gap-1.5">
                        Service level due <i class="ki-filled ki-down text-xs"></i>
                    </button>
                    <div class="kt-menu-dropdown kt-menu-default w-[180px]" data-kt-menu-dismiss="true">
                        @foreach(['Any','Overdue','Due today','Due this week','On track'] as $slt)
                            <div class="kt-menu-item"><a class="kt-menu-link" href="#"><span class="kt-menu-title">{{ $slt }}</span></a></div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="ms-auto flex items-center gap-2">
                <button class="kt-btn kt-btn-ghost kt-btn-sm kt-btn-icon" title="Column view">
                    <i class="ki-filled ki-element-7 text-sm"></i>
                </button>
                <button class="kt-btn kt-btn-ghost kt-btn-sm kt-btn-icon" title="List view">
                    <i class="ki-filled ki-row-horizontal text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Inbox table --}}
        <div class="kt-card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="crm_inbox_table">
                    <thead>
                        <tr class="border-b border-border bg-muted/40">
                            <th class="w-10 px-4 py-3">
                                <input class="kt-checkbox kt-checkbox-sm" type="checkbox" id="inbox_select_all"/>
                            </th>
                            <th class="text-start px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Item</th>
                            <th class="text-start px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Type</th>
                            <th class="text-start px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Person or company</th>
                            <th class="text-start px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Due</th>
                            <th class="text-start px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Owner</th>
                            <th class="text-start px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Service level due</th>
                            <th class="text-end px-4 py-3 text-xs font-semibold text-muted-foreground uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inboxItems ?? [] as $item)
                            <tr class="border-b border-border last:border-0 hover:bg-accent/30 transition-colors">
                                <td class="px-4 py-3.5">
                                    <input class="kt-checkbox kt-checkbox-sm" type="checkbox"/>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-mono">{{ $item['title'] }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $item['reference'] }}</div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="kt-badge kt-badge-secondary text-xs">{{ $item['type'] }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="kt-avatar size-7">
                                            <div class="kt-avatar-image rounded-full bg-primary/10 flex items-center justify-center text-xs text-primary font-semibold">
                                                {{ strtoupper(substr($item['person'], 0, 1)) }}
                                            </div>
                                        </div>
                                        <span class="text-secondary-foreground">{{ $item['person'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-secondary-foreground">{{ $item['due'] }}</td>
                                <td class="px-4 py-3.5 text-secondary-foreground">{{ $item['owner'] }}</td>
                                <td class="px-4 py-3.5">
                                    @if ($item['slt_status'] === 'overdue')
                                        <span class="kt-badge kt-badge-danger text-xs">Overdue</span>
                                    @elseif ($item['slt_status'] === 'due_today')
                                        <span class="kt-badge kt-badge-warning text-xs">Due today</span>
                                    @else
                                        <span class="kt-badge kt-badge-success text-xs">On track</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <button class="kt-btn kt-btn-ghost kt-btn-sm kt-btn-icon" title="Open">
                                            <i class="ki-filled ki-exit-right text-sm"></i>
                                        </button>
                                        <button class="kt-btn kt-btn-ghost kt-btn-sm kt-btn-icon" title="Send email">
                                            <i class="ki-filled ki-sms text-sm"></i>
                                        </button>
                                        <button class="kt-btn kt-btn-ghost kt-btn-sm kt-btn-icon" title="Send Short Message Service">
                                            <i class="ki-filled ki-message-notify text-sm"></i>
                                        </button>
                                        <button class="kt-btn kt-btn-ghost kt-btn-sm kt-btn-icon" title="Add note">
                                            <i class="ki-filled ki-notepad text-sm"></i>
                                        </button>
                                        <button class="kt-btn kt-btn-ghost kt-btn-sm kt-btn-icon" title="Assign owner">
                                            <i class="ki-filled ki-people text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-14 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="ki-filled ki-check-circle text-4xl text-success"></i>
                                        <p class="text-sm font-medium text-mono">Your inbox is clear</p>
                                        <p class="text-xs text-muted-foreground">All items have been processed.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- B4. Pagination footer --}}
            <div class="flex items-center justify-between gap-3 px-5 py-3.5 border-t border-border">
                <span class="text-xs text-muted-foreground">
                    Showing {{ $inboxItems?->firstItem() ?? 0 }}–{{ $inboxItems?->lastItem() ?? 0 }} of {{ $inboxItems?->total() ?? 0 }} items
                </span>
                @if(isset($inboxItems))
                    {{ $inboxItems->links() }}
                @endif
            </div>

        </div>{{-- end inbox table --}}
    </div>{{-- end inbox tab --}}

    {{-- ── TAB: Tasks ── --}}
    <div id="crm_tab_tasks" class="hidden">
        <div class="kt-card">
            <div class="kt-card-content p-10 text-center">
                <i class="ki-filled ki-calendar-tick text-4xl text-muted-foreground/40 mb-3"></i>
                <p class="text-sm text-muted-foreground">Your task list will appear here.</p>
            </div>
        </div>
    </div>

    {{-- ── TAB: Service level targets ── --}}
    <div id="crm_tab_slt" class="hidden">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @php
            $sltCards = [
                ['label' => 'First response time',    'target' => '2h',  'current' => '1h 42m', 'status' => 'on_track',  'breaches' => 0],
                ['label' => 'Lead follow-up',          'target' => '24h', 'current' => '18h',    'status' => 'on_track',  'breaches' => 1],
                ['label' => 'Dispute acknowledgement', 'target' => '4h',  'current' => '5h 12m', 'status' => 'breached',  'breaches' => 2],
            ];
            @endphp
            @foreach ($sltCards as $card)
                <div class="kt-card">
                    <div class="kt-card-content p-5 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-mono">{{ $card['label'] }}</span>
                            @if ($card['status'] === 'breached')
                                <span class="kt-badge kt-badge-danger text-xs">{{ $card['breaches'] }} breach{{ $card['breaches'] !== 1 ? 'es' : '' }}</span>
                            @else
                                <span class="kt-badge kt-badge-success text-xs">On track</span>
                            @endif
                        </div>
                        <div class="flex items-end gap-2">
                            <span class="text-2xl font-bold text-mono">{{ $card['current'] }}</span>
                            <span class="text-sm text-muted-foreground mb-0.5">/ {{ $card['target'] }} target</span>
                        </div>
                        @if ($card['status'] === 'breached')
                            <p class="text-xs text-danger">Review workload distribution and follow-up cadence.</p>
                        @else
                            <p class="text-xs text-muted-foreground">Performing within target. Keep it up.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>{{-- end slt tab --}}

</div>
@endsection
