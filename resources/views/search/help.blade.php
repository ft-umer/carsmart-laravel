@extends('layouts.app')
@section('title','Help Centre')
@section('content')

<div class="kt-container-fixed">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Help Centre</h1>
            <p class="text-sm text-muted-foreground mt-1">
                Search articles, guides, and release notes
            </p>
        </div>

        <a href="mailto:support@carsmart.co" class="kt-btn kt-btn-outline kt-btn-sm">
            Contact Support
        </a>
    </div>

    {{-- Search --}}
    <div class="card border border-border rounded-xl mb-6">
        <div class="p-6 text-center">
            <h2 class="text-lg font-semibold mb-2">How can we help?</h2>
            <p class="text-sm text-muted-foreground mb-5">
                Search articles, guides, and release notes
            </p>

            <div class="relative max-w-xl mx-auto">
                <i data-lucide="search"
                   class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"></i>

                <input type="text"
                       class="kt-input w-full pl-10"
                       placeholder="Search help articles…">
            </div>
        </div>
    </div>

    {{-- Categories --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">
        @foreach($categories as $cat)
            <div class="card border border-border rounded-xl p-5 hover:bg-muted/20 cursor-pointer transition">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                        <i data-lucide="{{ $cat['icon'] }}" class="w-5 h-5 text-primary"></i>
                    </div>

                    <div>
                        <div class="font-semibold text-foreground">
                            {{ $cat['title'] }}
                        </div>
                        <div class="text-xs text-muted-foreground">
                            {{ $cat['articles'] }} articles
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Release Notes --}}
    <div class="card border border-border rounded-xl">
        <div class="p-5 border-b border-border">
            <h3 class="font-semibold text-foreground">Release Notes</h3>
        </div>

        <div class="p-5 space-y-6">
            @foreach($releaseNotes as $r)
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="kt-badge kt-badge-primary kt-badge-sm">
                            {{ $r['version'] }}
                        </span>
                        <span class="text-xs text-muted-foreground">
                            {{ $r['date'] }}
                        </span>
                    </div>

                    <ul class="list-disc pl-5 text-sm text-muted-foreground space-y-1">
                        @foreach($r['notes'] as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection