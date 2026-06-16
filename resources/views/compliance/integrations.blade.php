@extends('layouts.app')
@section('title','Compliance — Integrations')
@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">
                Integrations & Key Management
            </h1>
            <p class="text-sm text-muted-foreground mt-1">
                Manage API keys, webhooks, and external connections
            </p>
        </div>
    </div>

    {{-- Integrations --}}
    @foreach($integrations as $integ)
    <div class="card border border-border rounded-xl mb-6">

        {{-- Card Header --}}
        <div class="p-5 border-b border-border flex items-center justify-between">

            <div class="flex items-center gap-3">

                <div class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center">
                    <i class="ki-outline {{ $integ['icon'] }} text-primary fs-2"></i>
                </div>

                <div>
                    <div class="font-semibold text-foreground">
                        {{ $integ['name'] }}
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Integration service
                    </div>
                </div>

                <span class="kt-badge kt-badge-sm kt-badge-success">
                    {{ $integ['status'] }}
                </span>

            </div>

            <div class="flex gap-2">
                <button class="kt-btn kt-btn-warning kt-btn-sm">
                    Rotate Key
                </button>
                <button class="kt-btn kt-btn-light kt-btn-sm">
                    Test Connection
                </button>
            </div>

        </div>

        {{-- Card Body --}}
        <div class="p-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- API Key --}}
                <div>
                    <label class="text-sm font-medium text-foreground mb-2 block">
                        Public / API Key
                    </label>

                    <div class="flex">
                        <input type="text"
                               class="kt-input w-full font-mono text-xs"
                               value="{{ $integ['public_key'] }}"
                               readonly>

                        <button class="kt-btn kt-btn-light"
                                onclick="navigator.clipboard.writeText('{{ $integ['public_key'] }}')">
                            <i class="ki-outline ki-copy"></i>
                        </button>
                    </div>
                </div>

                {{-- Webhook --}}
                <div>
                    <label class="text-sm font-medium text-foreground mb-2 block">
                        Webhook URL
                    </label>

                    <div class="flex">
                        <input type="text"
                               class="kt-input w-full font-mono text-xs"
                               value="{{ $integ['webhook'] }}"
                               readonly>

                        <button class="kt-btn kt-btn-light"
                                onclick="navigator.clipboard.writeText('{{ $integ['webhook'] }}')">
                            <i class="ki-outline ki-copy"></i>
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </div>
    @endforeach

</div>

@endsection