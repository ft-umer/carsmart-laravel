{{-- resources/views/settings/rbac.blade.php --}}
{{-- Phase 5 — S1: Settings → Users & Roles (RBAC) --}}
@extends('layouts.app')
@section('title', 'Users & Roles — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Users & Roles</span>
    </nav>

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Users & Roles</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Manage users, teams, and permission matrix</p>
        </div>
    </div>

    {{-- Tabs: Users | Roles --}}
    <div class="flex border-b border-border gap-1 mb-6">
        <button class="px-4 py-2.5 text-sm font-medium border-b-2 border-primary text-primary rbac-tab-btn" data-tab="users">Users</button>
        <button class="px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-muted-foreground hover:text-foreground rbac-tab-btn" data-tab="roles">Roles</button>
        <button class="px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-muted-foreground hover:text-foreground rbac-tab-btn" data-tab="matrix">Permissions matrix</button>
    </div>

    {{-- ── USERS tab ── --}}
    <div id="tab-users">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <div class="flex gap-2 flex-wrap">
                <input type="text" class="kt-input" placeholder="Search users…" id="user-search" />
                <select class="kt-input" id="user-role-filter">
                    <option value="">All roles</option>
                    @foreach(['Admin','Content','Automations','CRM','Compliance','Curator','Management'] as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                    @endforeach
                </select>
                <select class="kt-input" id="user-status-filter">
                    <option value="">All statuses</option>
                    <option value="Active">Active</option>
                    <option value="Disabled">Disabled</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
            <button class="kt-btn kt-btn-mono" id="btn-invite-user">
                <i data-lucide="user-plus" class="w-4 h-4 mr-1"></i> Invite user
            </button>
        </div>

        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-sm">
                    <thead class="bg-muted/40">
                        <tr>
                            @foreach(['Name','Email','Role(s)','Status','Last active','Actions'] as $col)
                                <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background">
                        @forelse($users ?? [] as $user)
                            <tr class="hover:bg-muted/30 transition-colors">
                                <td class="p-3">
                                    <div class="flex items-center gap-2">
                                        <span class="flex items-center justify-center size-8 rounded-full bg-primary/10 text-primary text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($user['name'], 0, 2)) }}
                                        </span>
                                        <span class="font-medium text-foreground">{{ $user['name'] }}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-muted-foreground">{{ $user['email'] }}</td>
                                <td class="p-3">
                                    <div class="flex gap-1 flex-wrap">
                                        @foreach($user['roles'] ?? [] as $role)
                                            <span class="kt-badge kt-badge-outline kt-badge-xs">{{ $role }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-3">
                                    <span class="kt-badge kt-badge-{{ match($user['status'] ?? 'Active') {
                                        'Active'   => 'success',
                                        'Disabled' => 'destructive',
                                        'Pending'  => 'warning',
                                        default    => 'secondary',
                                    } }} kt-badge-sm">{{ $user['status'] ?? 'Active' }}</span>
                                </td>
                                <td class="p-3 text-xs text-muted-foreground">
                                    {{ isset($user['last_active']) ? \Carbon\Carbon::parse($user['last_active'])->diffForHumans() : 'Never' }}
                                </td>
                                <td class="p-3">
                                    <div class="flex gap-1">
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs user-edit-btn" data-id="{{ $user['id'] }}">Open</button>
                                        @if($user['status'] === 'Active')
                                            <button class="kt-btn kt-btn-ghost kt-btn-xs text-warning user-disable-btn" data-id="{{ $user['id'] }}">Disable</button>
                                        @else
                                            <button class="kt-btn kt-btn-ghost kt-btn-xs text-success user-enable-btn" data-id="{{ $user['id'] }}">Enable</button>
                                        @endif
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs user-reset-btn" data-id="{{ $user['id'] }}">Reset pwd</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-10 text-center text-muted-foreground">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── ROLES tab ── --}}
    <div id="tab-roles" class="hidden">
        <div class="flex justify-between mb-4">
            <p class="text-sm text-muted-foreground">Define roles and their permission sets</p>
            <button class="kt-btn kt-btn-mono" id="btn-new-role">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> New role
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($roles ?? [] as $role)
                <div class="card border border-border rounded-xl p-4 hover:border-primary/40 transition-colors cursor-pointer role-card" data-id="{{ $role['id'] }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-foreground">{{ $role['name'] }}</span>
                        <span class="kt-badge kt-badge-outline kt-badge-xs">{{ $role['user_count'] ?? 0 }} users</span>
                    </div>
                    <p class="text-xs text-muted-foreground mb-3">{{ $role['description'] ?? 'No description.' }}</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach(array_slice($role['permissions'] ?? [], 0, 4) as $perm)
                            <span class="kt-badge kt-badge-secondary kt-badge-xs">{{ $perm }}</span>
                        @endforeach
                        @if(count($role['permissions'] ?? []) > 4)
                            <span class="kt-badge kt-badge-secondary kt-badge-xs">+{{ count($role['permissions']) - 4 }} more</span>
                        @endif
                    </div>
                </div>
            @empty
                @foreach(['Admin','Content','Automations','CRM','Compliance','Curator','Management'] as $roleName)
                    <div class="card border border-border rounded-xl p-4 hover:border-primary/40 transition-colors cursor-pointer">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-semibold text-foreground">{{ $roleName }}</span>
                            <span class="kt-badge kt-badge-outline kt-badge-xs">0 users</span>
                        </div>
                        <p class="text-xs text-muted-foreground">Platform role for {{ strtolower($roleName) }} operations.</p>
                    </div>
                @endforeach
            @endforelse
        </div>
    </div>

    {{-- ── PERMISSIONS MATRIX tab ── --}}
    <div id="tab-matrix" class="hidden">
        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/40 sticky top-0">
                        <tr>
                            <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide sticky left-0 bg-muted/40 min-w-[180px]">Module / Action</th>
                            @foreach(['Admin','Content','Automations','CRM','Compliance','Curator','Management'] as $r)
                                <th class="p-3 text-center text-xs font-semibold text-muted-foreground uppercase tracking-wide whitespace-nowrap">{{ $r }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background">
                        @php
                            $matrix = [
                                'CMS: View'            => [1,1,0,0,0,1,1],
                                'CMS: Edit'            => [1,1,0,0,0,1,0],
                                'CMS: Publish'         => [1,1,0,0,0,0,0],
                                'Automations: View'    => [1,0,1,0,1,0,1],
                                'Automations: Build'   => [1,0,1,0,0,0,0],
                                'Reports: View'        => [1,0,0,1,1,0,1],
                                'Reports: Export'      => [1,0,0,1,0,0,1],
                                'Settings: RBAC'       => [1,0,0,0,0,0,0],
                                'Settings: Providers'  => [1,0,0,0,0,0,0],
                                'KYC: Override'        => [1,0,0,0,1,0,0],
                                'Payout: Approve'      => [1,0,0,0,0,0,0],
                                'Tasks: View'          => [1,1,1,1,1,1,1],
                                'Tasks: Assign'        => [1,0,0,1,0,0,0],
                                'Notifications: Mgmt'  => [1,0,0,0,0,0,0],
                            ];
                        @endphp
                        @foreach($matrix as $action => $perms)
                            <tr class="hover:bg-muted/20">
                                <td class="p-3 font-medium text-foreground sticky left-0 bg-background">{{ $action }}</td>
                                @foreach($perms as $has)
                                    <td class="p-3 text-center">
                                        @if($has)
                                            <i data-lucide="check" class="w-4 h-4 text-success mx-auto"></i>
                                        @else
                                            <span class="block w-4 h-0.5 bg-border mx-auto rounded"></span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-xs text-muted-foreground mt-2">
            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
            Certain permissions (override KYC/KYB, payout approvals) are restricted to Super Admin only.
        </p>
    </div>
</div>

{{-- Invite User Modal --}}
<div id="modal-invite-user" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">Invite user</h2>
            <button class="invite-close text-muted-foreground hover:text-foreground"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Full name</label>
                <input type="text" class="kt-input w-full" placeholder="Jane Smith" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Email address <span class="text-destructive">*</span></label>
                <input type="email" class="kt-input w-full" placeholder="jane@example.com" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Roles <span class="text-destructive">*</span></label>
                <div class="grid grid-cols-2 gap-1.5">
                    @foreach(['Admin','Content','Automations','CRM','Compliance','Curator','Management'] as $r)
                        <label class="flex items-center gap-2 text-sm cursor-pointer p-1.5 rounded hover:bg-muted/40">
                            <input type="checkbox" class="kt-checkbox" /> {{ $r }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="invite-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-mono">Send invite</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Tab switching
document.querySelectorAll('.rbac-tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.rbac-tab-btn').forEach(b => {
            b.className = 'px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-muted-foreground hover:text-foreground rbac-tab-btn';
        });
        this.className = 'px-4 py-2.5 text-sm font-medium border-b-2 border-primary text-primary rbac-tab-btn';
        ['users','roles','matrix'].forEach(t => document.getElementById('tab-' + t).classList.add('hidden'));
        document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
    });
});

// Invite modal
document.getElementById('btn-invite-user')?.addEventListener('click', () => {
    document.getElementById('modal-invite-user').classList.remove('hidden');
    document.getElementById('modal-invite-user').classList.add('flex');
});
document.querySelectorAll('.invite-close').forEach(b => b.addEventListener('click', () => {
    document.getElementById('modal-invite-user').classList.add('hidden');
    document.getElementById('modal-invite-user').classList.remove('flex');
}));
</script>
@endpush

@endsection
