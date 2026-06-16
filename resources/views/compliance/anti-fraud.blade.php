@extends('layouts.app')
@section('title','Compliance — Anti-Fraud & Rate Limits')
@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">
                Anti-Fraud & Rate Limits
            </h1>
            <p class="text-sm text-muted-foreground mt-1">
                Compliance → Rules & exception management
            </p>
        </div>
    </div>

    {{-- Rate Limit Rules --}}
    <div class="card border border-border rounded-xl mb-6">

        <div class="p-5 border-b border-border flex items-center justify-between">
            <h3 class="font-semibold text-foreground">Rate Limit Rules</h3>

            <button class="kt-btn kt-btn-mono kt-btn-sm">
                <i class="ki-outline ki-plus"></i> Add Rule
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1000px]">
                <thead class="bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="p-3 text-left">Area</th>
                        <th class="p-3 text-left">Metric</th>
                        <th class="p-3 text-left">Threshold</th>
                        <th class="p-3 text-left">Action</th>
                        <th class="p-3 text-right">Edit</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($rules as $r)
                    <tr class="border-t border-border hover:bg-muted/20">
                        <td class="p-3 font-semibold">{{ $r['area'] }}</td>
                        <td class="p-3">{{ $r['metric'] }}</td>
                        <td class="p-3">{{ $r['threshold'] }}</td>
                        <td class="p-3">
                            <span class="kt-badge kt-badge-sm
                                {{ $r['action']==='Block' ? 'kt-badge-danger' : ($r['action']==='Flag' ? 'kt-badge-warning' : 'kt-badge-info') }}">
                                {{ $r['action'] }}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <button class="kt-btn kt-btn-ghost kt-btn-sm">Edit</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Exceptions --}}
    <div class="card border border-border rounded-xl">

        <div class="p-5 border-b border-border flex items-center justify-between">
            <h3 class="font-semibold text-foreground">Exceptions</h3>

            <button class="kt-btn kt-btn-warning kt-btn-sm">
                <i class="ki-outline ki-plus"></i> Add Exception
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1000px]">
                <thead class="bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="p-3 text-left">Subject</th>
                        <th class="p-3 text-left">Rule</th>
                        <th class="p-3 text-left">Window</th>
                        <th class="p-3 text-left">Reason</th>
                        <th class="p-3 text-left">Granted By</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($exceptions as $e)
                    <tr class="border-t border-border hover:bg-muted/20">
                        <td class="p-3 font-semibold">{{ $e['subject'] }}</td>
                        <td class="p-3">{{ $e['rule'] }}</td>
                        <td class="p-3">{{ $e['window'] }}</td>
                        <td class="p-3 text-muted-foreground">{{ $e['reason'] }}</td>
                        <td class="p-3">{{ $e['actor'] }}</td>
                        <td class="p-3 text-right">
                            <button class="kt-btn kt-btn-danger kt-btn-sm">
                                Revoke
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection