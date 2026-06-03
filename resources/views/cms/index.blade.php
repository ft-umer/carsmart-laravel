{{-- resources/views/cms/index.blade.php --}}
{{-- Phase 5 — CMS0: CMS → Overview / Library --}}
@extends('layouts.app')
@section('title', 'Content Library — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-semibold text-foreground">Content Library</h1>
            <span class="text-sm text-muted-foreground">{{ $totalItems ?? 0 }} items</span>
        </div>
        <div class="flex gap-2 flex-wrap">
            <button id="btn-upload-media" class="kt-btn kt-btn-outline">
                <i data-lucide="upload" class="w-4 h-4 mr-1"></i> Upload media
            </button>
            <a href="{{ route('cms.pages.create') }}" class="kt-btn kt-btn-outline">
                <i data-lucide="file-plus" class="w-4 h-4 mr-1"></i> New page
            </a>
            <a href="{{ route('cms.posts.create') }}" class="kt-btn kt-btn-mono">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> New post
            </a>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="kt-tabs mb-5" data-kt-tabs="true">
        <div class="flex border-b border-border gap-1">
            @foreach([
                ['pages',    'Pages',            route('cms.index', ['tab' => 'pages'])],
                ['posts',    'Posts',             route('cms.index', ['tab' => 'posts'])],
                ['banners',  'Banners & Features',route('cms.banners')],
                ['media',    'Media Library',     route('cms.media')],
            ] as [$key, $label, $href])
                <a href="{{ $href }}"
                   class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                          {{ ($activeTab ?? 'pages') === $key
                              ? 'border-primary text-primary'
                              : 'border-transparent text-muted-foreground hover:text-foreground' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- ── Filters ── --}}
    <form method="GET" action="{{ route('cms.index') }}" class="card border border-border rounded-lg p-3 mb-5">
        <input type="hidden" name="tab" value="{{ $activeTab ?? 'pages' }}">
        <div class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-muted-foreground mb-1">Search</label>
                <input name="search" value="{{ $search ?? '' }}" type="search"
                       class="kt-input w-full" placeholder="Title or slug…" />
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs text-muted-foreground mb-1">Status</label>
                <select name="status" class="kt-input w-full">
                    <option value="">All statuses</option>
                    @foreach(['Draft','Scheduled','Published','Archived'] as $s)
                        <option value="{{ $s }}" @selected(($status ?? '') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs text-muted-foreground mb-1">Owner</label>
                <select name="owner" class="kt-input w-full">
                    <option value="">All owners</option>
                    @foreach($owners ?? [] as $o)
                        <option value="{{ $o }}" @selected(($owner ?? '') === $o)>{{ $o }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
            <a href="{{ route('cms.index') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
        </div>
    </form>

    {{-- ── Table + Quick View panel ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr,380px] gap-5">

        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-muted/40 sticky top-0 z-10">
                        <tr>
                            @foreach(['#','Title','Type','Status','Scheduled','Last updated','Owner','Actions'] as $col)
                                <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide
                                           {{ $col === 'Actions' ? 'w-40' : '' }}">
                                    {{ $col }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background">
                        @forelse($items ?? [] as $item)
                            <tr class="hover:bg-muted/30 transition-colors cursor-pointer cms-row"
                                data-id="{{ $item['id'] }}">
                                <td class="p-3 text-muted-foreground text-xs">{{ $item['id'] }}</td>
                                <td class="p-3 font-medium text-foreground">
                                    {{ $item['title'] }}
                                    <div class="text-xs text-muted-foreground">/{{ $item['slug'] }}</div>
                                </td>
                                <td class="p-3">
                                    <span class="kt-badge kt-badge-outline kt-badge-sm">{{ $item['type'] }}</span>
                                </td>
                                <td class="p-3">
                                    @php
                                        $statusColour = match($item['status']) {
                                            'Published' => 'success',
                                            'Scheduled' => 'info',
                                            'Draft'     => 'secondary',
                                            'Archived'  => 'destructive',
                                            default     => 'secondary',
                                        };
                                    @endphp
                                    <span class="kt-badge kt-badge-{{ $statusColour }} kt-badge-sm">{{ $item['status'] }}</span>
                                </td>
                                <td class="p-3 text-xs text-muted-foreground">
                                    {{ $item['scheduled_at'] ? \Carbon\Carbon::parse($item['scheduled_at'])->format('d M Y H:i') : '—' }}
                                </td>
                                <td class="p-3 text-xs text-muted-foreground">
                                    {{ \Carbon\Carbon::parse($item['updated_at'])->diffForHumans() }}
                                </td>
                                <td class="p-3 text-sm">{{ $item['owner'] }}</td>
                                <td class="p-3">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('cms.edit', $item['id']) }}"
                                           class="kt-btn kt-btn-ghost kt-btn-xs" title="Open">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <a href="#" class="kt-btn kt-btn-ghost kt-btn-xs cms-preview-btn"
                                           data-id="{{ $item['id'] }}" title="Preview">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </a>
                                        @if($item['status'] !== 'Published')
                                            <button class="kt-btn kt-btn-ghost kt-btn-xs cms-publish-btn"
                                                    data-id="{{ $item['id'] }}" title="Publish">
                                                <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-10 text-center text-muted-foreground">
                                    No content found. <a href="{{ route('cms.posts.create') }}" class="text-primary underline">Create your first post</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($items) && method_exists($items, 'links'))
                <div class="p-4 border-t border-border">{{ $items->links() }}</div>
            @endif
        </div>

        {{-- ── Quick View ── --}}
        <div id="cms-qv" class="card border border-border rounded-xl p-5 hidden xl:block">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-foreground text-sm">Quick view</h3>
                <button id="cms-qv-close" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <p class="text-sm text-muted-foreground">Select a row to preview details.</p>
        </div>

    </div>
</div>

{{-- Upload Media Modal --}}
<div id="modal-upload-media" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">Upload Media</h2>
            <button class="modal-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div id="drop-zone"
             class="border-2 border-dashed border-border rounded-xl p-10 text-center text-muted-foreground
                    hover:border-primary/50 transition-colors cursor-pointer">
            <i data-lucide="upload-cloud" class="w-10 h-10 mx-auto mb-3 text-muted-foreground/50"></i>
            <p class="text-sm font-medium">Drag and drop files here</p>
            <p class="text-xs mt-1">or <span class="text-primary underline cursor-pointer">browse files</span></p>
            <p class="text-xs text-muted-foreground/60 mt-2">Images, videos, PDFs up to 100 MB</p>
        </div>
        <div id="upload-list" class="mt-3 space-y-2 hidden"></div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-mono">Upload</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btn-upload-media')?.addEventListener('click', () => {
    document.getElementById('modal-upload-media').classList.remove('hidden');
    document.getElementById('modal-upload-media').classList.add('flex');
});
document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('modal-upload-media').classList.add('hidden');
        document.getElementById('modal-upload-media').classList.remove('flex');
    });
});

// Quick view
const qv = document.getElementById('cms-qv');
document.querySelectorAll('.cms-row').forEach(row => {
    row.addEventListener('click', function(e) {
        if(e.target.closest('a,button')) return;
        const id = this.dataset.id;
        const title = this.querySelector('td:nth-child(2)')?.innerText;
        qv.classList.remove('hidden');
        qv.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-foreground text-sm">Item #${id}</h3>
                <button id="cms-qv-close" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="space-y-3">
                <div class="text-sm font-medium text-foreground">${title}</div>
                <div class="flex gap-2">
                    <a href="/cms/${id}/edit" class="kt-btn kt-btn-mono kt-btn-sm w-full justify-center">Open editor</a>
                </div>
                <div class="flex gap-2">
                    <a href="/cms/${id}/preview" target="_blank" class="kt-btn kt-btn-outline kt-btn-sm w-full justify-center">Preview</a>
                </div>
            </div>`;
        document.getElementById('cms-qv-close')?.addEventListener('click', () => {
            qv.innerHTML = '<div class="flex items-center justify-between mb-4"><h3 class="font-semibold text-foreground text-sm">Quick view</h3></div><p class="text-sm text-muted-foreground">Select a row to preview details.</p>';
        });
        lucide.createIcons();
    });
});
</script>
@endpush

@endsection
