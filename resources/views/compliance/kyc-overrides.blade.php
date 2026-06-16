@extends('layouts.app')
@section('title','Compliance — KYC/KYB Overrides')
@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">
                KYC / KYB Override Log
            </h1>
            <p class="text-sm text-muted-foreground mt-1">
                Compliance → Verification overrides audit trail
            </p>
        </div>

        <button class="kt-btn kt-btn-mono kt-btn-sm">
            Export
        </button>
    </div>

    {{-- Info --}}
    <div class="card border border-border rounded-xl mb-6">
        <div class="p-4 flex items-center gap-3">
            <i class="ki-outline ki-shield-cross text-info fs-2"></i>
            <span class="text-sm text-muted-foreground font-medium">
                All KYC/KYB overrides are restricted to Super Admin. Each entry requires a reason and evidence attachment.
            </span>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border border-border rounded-xl">
        <div class="p-5 border-b border-border">
            <h3 class="font-semibold text-foreground">Audit Log</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1000px]">
                <thead class="bg-muted/40 text-xs uppercase text-muted-foreground">
                    <tr>
                        <th class="p-3 text-left">Time</th>
                        <th class="p-3 text-left">Subject</th>
                        <th class="p-3 text-left">Before</th>
                        <th class="p-3 text-left">After</th>
                        <th class="p-3 text-left">Actor</th>
                        <th class="p-3 text-left">Reason</th>
                        <th class="p-3 text-left">Attachment</th>
                        <th class="p-3 text-right">Actions</th>
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
                            <span class="kt-badge kt-badge-sm kt-badge-warning">
                                {{ $e['before'] }}
                            </span>
                        </td>

                        <td class="p-3">
                            <span class="kt-badge kt-badge-sm
                                {{ $e['after']==='Verified' ? 'kt-badge-success' : 'kt-badge-danger' }}">
                                {{ $e['after'] }}
                            </span>
                        </td>

                        <td class="p-3">{{ $e['actor'] }}</td>

                        <td class="p-3 text-muted-foreground text-sm">
                            {{ $e['reason'] }}
                        </td>

                        <td class="p-3">
                            <a href="#" class="text-primary flex items-center gap-1">
                                <i class="ki-outline ki-file-down"></i>
                                <span>{{ $e['attachment'] }}</span>
                            </a>
                        </td>

                        <td class="p-3 text-right">
                            <button class="kt-btn kt-btn-ghost kt-btn-sm">View</button>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection