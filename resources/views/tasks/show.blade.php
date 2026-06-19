@extends('layouts.app')

@section('title', 'Task Detail')

@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">
                Task #{{ $task['id'] }} — {{ $task['title'] }}
            </h1>
            <p class="text-sm text-muted-foreground mt-1">
                {{ $task['type'] }} • {{ $task['source_module'] }}
            </p>
        </div>

        <a href="{{ route('tasks.index') }}" class="kt-btn kt-btn-ghost kt-btn-sm">
            Back
        </a>
    </div>

    {{-- Task Card --}}
    <div class="card border border-border rounded-xl p-5">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">

            <div>
                <div class="text-xs text-muted-foreground">Type</div>
                <div class="font-medium">{{ $task['type'] }}</div>
            </div>

            <div>
                <div class="text-xs text-muted-foreground">Priority</div>
                <div class="font-medium">{{ $task['priority'] }}</div>
            </div>

            <div>
                <div class="text-xs text-muted-foreground">Owner</div>
                <div class="font-medium">{{ $task['owner'] ?? 'Unassigned' }}</div>
            </div>

            <div>
                <div class="text-xs text-muted-foreground">Due</div>
                <div class="font-medium">
                    {{ \Carbon\Carbon::parse($task['due_at'])->format('d M Y H:i') }}
                </div>
            </div>

        </div>

        <hr class="my-5 border-border">

        <div>
            <div class="text-xs text-muted-foreground mb-1">Notes</div>
            <div class="text-sm text-foreground">
                {{ $task['notes'] ?? 'No notes available' }}
            </div>
        </div>

    </div>

</div>

@endsection