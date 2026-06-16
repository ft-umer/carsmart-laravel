@extends('layouts.app')
@section('title','Compliance — Consent Logs')
@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">
                Consent Logs
            </h1>
            <p class="text-sm text-muted-foreground mt-1">
                Compliance → Consent tracking history
            </p>
        </div>

        <button class="kt-btn kt-btn-sm kt-btn-ghost">
            Export CSV
        </button>
    </div>

    {{-- Filters --}}
    <div class="card border border-border rounded-xl mb-6">
        <div class="p-4 flex flex-wrap gap-3">

            <input type="text"
                   class="kt-input w-60"
                   placeholder="Search subject…">

            <select class="kt-input w-40">
                <option>All channels</option>
                <option>Email</option>
                <option>SMS</option>
                <option>WhatsApp</option>
            </select>

            <input type="date" class="kt-input w-48">

        </div>
    </div>

    {{-- Table --}}
    <div class="card border border-border rounded-xl">
        <div class="p-5 border-b border-border">
            <h3 class="font-semibold text-foreground">Activity Log</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1000px]">

                <thead class="bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="p-3 text-left">Time</th>
                        <th class="p-3 text-left">Subject</th>
                        <th class="p-3 text-left">Channel</th>
                        <th class="p-3 text-left">Before</th>
                        <th class="p-3 text-left">After</th>
                        <th class="p-3 text-left">Actor</th>
                        <th class="p-3 text-left">Reason</th>
                        <th class="p-3 text-left">Source</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($entries as $e)
                    <tr class="border-t border-border hover:bg-muted/20">

                        <td class="p-3 text-muted-foreground text-xs">
                            {{ $e['time'] }}
                        </td>

                        <td class="p-3 font-semibold">
                            {{ $e['subject'] }}
                        </td>

                        <td class="p-3">
                            <span class="kt-badge kt-badge-sm">
                                {{ $e['channel'] }}
                            </span>
                        </td>

                        <td class="p-3">
                            <span class="kt-badge kt-badge-sm
                                {{ $e['before']==='Yes' ? 'kt-badge-success' : 'kt-badge-danger' }}">
                                {{ $e['before'] }}
                            </span>
                        </td>

                        <td class="p-3">
                            <span class="kt-badge kt-badge-sm
                                {{ $e['after']==='Yes' ? 'kt-badge-success' : 'kt-badge-danger' }}">
                                {{ $e['after'] }}
                            </span>
                        </td>

                        <td class="p-3">
                            {{ $e['actor'] }}
                        </td>

                        <td class="p-3 text-muted-foreground text-xs">
                            {{ $e['reason'] }}
                        </td>

                        <td class="p-3">
                            <span class="kt-badge kt-badge-light">
                                {{ $e['source'] }}
                            </span>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection