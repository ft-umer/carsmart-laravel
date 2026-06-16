@extends('layouts.app')
@section('title','Compliance — Security & Sessions')
@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">
                Security & Sessions
            </h1>
            <p class="text-sm text-muted-foreground mt-1">
                Active sessions and authentication audit trail
            </p>
        </div>

        <button class="kt-btn kt-btn-danger kt-btn-sm">
            Force sign out all
        </button>
    </div>

    {{-- Active Sessions --}}
    <div class="card border border-border rounded-xl mb-6">
        <div class="p-5 border-b border-border">
            <h3 class="font-semibold text-foreground">Active Sessions</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1000px]">
                <thead class="bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="p-3 text-left">User</th>
                        <th class="p-3 text-left">Role</th>
                        <th class="p-3 text-left">IP</th>
                        <th class="p-3 text-left">Device</th>
                        <th class="p-3 text-left">Started</th>
                        <th class="p-3 text-left">Last Active</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($active as $s)
                    <tr class="border-t border-border hover:bg-muted/20">

                        <td class="p-3 font-semibold">
                            {{ $s['user'] }}
                        </td>

                        <td class="p-3">
                            <span class="kt-badge kt-badge-sm kt-badge-primary">
                                {{ $s['role'] }}
                            </span>
                        </td>

                        <td class="p-3 font-mono text-xs text-muted-foreground">
                            {{ $s['ip'] }}
                        </td>

                        <td class="p-3">
                            {{ $s['agent'] }}
                        </td>

                        <td class="p-3 text-muted-foreground text-sm">
                            {{ $s['started'] }}
                        </td>

                        <td class="p-3">
                            <span class="text-success font-semibold">
                                {{ $s['last_active'] }}
                            </span>
                        </td>

                        <td class="p-3 text-right">
                            <button class="kt-btn kt-btn-danger kt-btn-sm">
                                Sign out
                            </button>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sign-in History --}}
    <div class="card border border-border rounded-xl">
        <div class="p-5 border-b border-border flex items-center justify-between">
            <h3 class="font-semibold text-foreground">Sign-in History</h3>

            <button class="kt-btn kt-btn-light kt-btn-sm">
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1000px]">
                <thead class="bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="p-3 text-left">Time</th>
                        <th class="p-3 text-left">User</th>
                        <th class="p-3 text-left">Result</th>
                        <th class="p-3 text-left">IP</th>
                        <th class="p-3 text-left">Device</th>
                        <th class="p-3 text-left">Location</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($history as $h)
                    <tr class="border-t border-border hover:bg-muted/20">

                        <td class="p-3 text-muted-foreground text-xs">
                            {{ $h['time'] }}
                        </td>

                        <td class="p-3 font-semibold">
                            {{ $h['user'] }}
                        </td>

                        <td class="p-3">
                            <span class="kt-badge kt-badge-sm
                                {{ $h['result']==='Success' ? 'kt-badge-success' : 'kt-badge-danger' }}">
                                {{ $h['result'] }}
                            </span>
                        </td>

                        <td class="p-3 font-mono text-xs text-muted-foreground">
                            {{ $h['ip'] }}
                        </td>

                        <td class="p-3">
                            {{ $h['agent'] }}
                        </td>

                        <td class="p-3 text-muted-foreground">
                            {{ $h['location'] }}
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection