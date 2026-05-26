{{-- Stub view for Phase 1+ pages — shows "coming soon" with nav context --}}
@extends('layouts.app')

@section('title', $title ?? 'Coming soon')

@section('content')
<div class="kt-container-fixed py-12 flex flex-col items-center justify-center min-h-[60vh] text-center gap-5">
    <div class="flex items-center justify-center size-20 rounded-2xl bg-muted">
        <i class="ki-filled ki-abstract-26 text-4xl text-muted-foreground/50"></i>
    </div>
    <div>
        <h2 class="text-xl font-semibold text-mono mb-2">{{ $title ?? 'Coming soon' }}</h2>
        <p class="text-sm text-muted-foreground max-w-[340px]">
            This section is planned for a future phase. Use the navigation to return to an available area.
        </p>
    </div>
    <a href="{{ route('dashboard') }}" class="kt-btn kt-btn-primary">
        <i class="ki-filled ki-home-2 text-sm me-1.5"></i>
        Back to dashboard
    </a>
</div>
@endsection
