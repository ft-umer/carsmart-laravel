{{-- resources/views/cms/edit.blade.php --}}
{{-- Phase 5 — CMS1: Page/Post Editor (with versioning) --}}
@extends('layouts.app')
@section('title', ($item['title'] ?? 'New Content') . ' — CMS Editor')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    {{-- ── Editor Toolbar ── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div class="flex items-center gap-2">
            <a href="{{ route('cms.index') }}" class="text-muted-foreground hover:text-foreground">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <h1 class="text-xl font-semibold text-foreground">
                {{ isset($item) ? 'Edit: ' . $item['title'] : 'New content' }}
            </h1>
            @if(isset($item))
                <span class="kt-badge kt-badge-{{ match($item['status'] ?? 'Draft') {
                    'Published' => 'success', 'Scheduled' => 'info', 'Draft' => 'secondary',
                    'Archived' => 'destructive', default => 'secondary'
                } }} kt-badge-sm">{{ $item['status'] ?? 'Draft' }}</span>
            @endif
        </div>
        <div class="flex gap-2 flex-wrap">
            <button type="button" id="btn-history" class="kt-btn kt-btn-ghost kt-btn-sm">
                <i data-lucide="history" class="w-4 h-4 mr-1"></i> History
            </button>
            <button type="button" id="btn-preview" class="kt-btn kt-btn-outline kt-btn-sm">
                <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Preview
            </button>
            <button type="button" id="btn-save-draft" class="kt-btn kt-btn-outline kt-btn-sm">
                Save draft
            </button>
            <button type="button" id="btn-submit-approval" class="kt-btn kt-btn-outline kt-btn-sm">
                Submit for approval
            </button>
            <button type="button" id="btn-schedule" class="kt-btn kt-btn-outline kt-btn-sm">
                <i data-lucide="calendar" class="w-4 h-4 mr-1"></i> Schedule
            </button>
            <button type="button" id="btn-publish" class="kt-btn kt-btn-mono kt-btn-sm">
                Publish
            </button>
        </div>
    </div>

    {{-- ── Header: Title / Slug / Owner ── --}}
    <div class="card border border-border rounded-xl p-5 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-[1fr,auto,auto] gap-4 items-end">
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Title <span class="text-destructive">*</span></label>
                <input id="cms-title" type="text" class="kt-input w-full text-lg font-semibold"
                       placeholder="Enter title…" value="{{ $item['title'] ?? '' }}" />
            </div>
            <div class="min-w-[200px]">
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Slug</label>
                <div class="flex items-center">
                    <span class="text-muted-foreground text-sm px-2">/</span>
                    <input id="cms-slug" type="text" class="kt-input w-full"
                           placeholder="auto-generated" value="{{ $item['slug'] ?? '' }}" />
                </div>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Owner</label>
                <select class="kt-input w-full">
                    @foreach($users ?? [] as $u)
                        <option value="{{ $u['id'] }}" @selected(($item['owner_id'] ?? null) == $u['id'])>{{ $u['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Main 2-col layout ── --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr,300px] gap-5">

        {{-- Body Editor --}}
        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="border-b border-border p-3 flex flex-wrap gap-1 bg-muted/20">
                @foreach([
                    ['bold','Bold'],['italic','Italic'],['underline','Underline'],
                    null,
                    ['heading','Heading'],['list','List'],['image','Image'],['link','Link'],
                    null,
                    ['code','Code'],['quote','Quote'],
                ] as $tool)
                    @if($tool === null)
                        <div class="w-px h-6 bg-border self-center mx-1"></div>
                    @else
                        <button type="button"
                                class="p-1.5 rounded hover:bg-accent text-muted-foreground hover:text-foreground transition-colors"
                                title="{{ $tool[1] }}">
                            <i data-lucide="{{ $tool[0] }}" class="w-4 h-4"></i>
                        </button>
                    @endif
                @endforeach
            </div>
            <div id="cms-editor"
                 class="min-h-[480px] p-5 focus:outline-none text-sm leading-relaxed text-foreground"
                 contenteditable="true"
                 placeholder="Start writing…">
                {!! $item['body'] ?? '' !!}
            </div>
        </div>

        {{-- Sidebar: Meta / SEO / Settings --}}
        <div class="flex flex-col gap-4">

            {{-- Meta --}}
            <div class="card border border-border rounded-xl p-4">
                <h3 class="text-sm font-semibold text-foreground mb-3">Meta</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Type</label>
                        <select class="kt-input w-full">
                            <option value="page" @selected(($item['type'] ?? '') === 'page')>Page</option>
                            <option value="post" @selected(($item['type'] ?? '') === 'post')>Post</option>
                            <option value="banner" @selected(($item['type'] ?? '') === 'banner')>Banner</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Categories</label>
                        <input type="text" class="kt-input w-full" placeholder="e.g. News, Updates"
                               value="{{ implode(', ', $item['categories'] ?? []) }}" />
                    </div>
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Tags</label>
                        <input type="text" class="kt-input w-full" placeholder="Comma separated"
                               value="{{ implode(', ', $item['tags'] ?? []) }}" />
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="card border border-border rounded-xl p-4">
                <h3 class="text-sm font-semibold text-foreground mb-3">SEO</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">SEO Title</label>
                        <input type="text" class="kt-input w-full" value="{{ $item['seo_title'] ?? '' }}" />
                    </div>
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Meta Description</label>
                        <textarea class="kt-input w-full" rows="3"
                                  placeholder="160 chars max">{{ $item['seo_description'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs text-muted-foreground mb-1">Social image</label>
                        <div class="border border-dashed border-border rounded-lg p-3 text-center text-xs text-muted-foreground cursor-pointer hover:border-primary/40">
                            <i data-lucide="image" class="w-5 h-5 mx-auto mb-1"></i>
                            Upload or select from media
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feature flags --}}
            <div class="card border border-border rounded-xl p-4">
                <h3 class="text-sm font-semibold text-foreground mb-3">Feature flags</h3>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="kt-checkbox"
                               {{ ($item['homepage_carousel'] ?? false) ? 'checked' : '' }} />
                        <span class="text-sm">Homepage carousel</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="kt-checkbox"
                               {{ ($item['editions_feature'] ?? false) ? 'checked' : '' }} />
                        <span class="text-sm">Editions feature</span>
                    </label>
                </div>
            </div>

        </div>{{-- /sidebar --}}
    </div>
</div>

{{-- Version History Drawer --}}
<div id="drawer-history" class="fixed inset-y-0 right-0 w-80 bg-background border-l border-border shadow-xl z-50 hidden transform translate-x-full transition-transform duration-300">
    <div class="flex items-center justify-between p-4 border-b border-border">
        <h2 class="font-semibold text-foreground">Version history</h2>
        <button id="drawer-history-close" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    <div class="p-4 overflow-y-auto h-full pb-20">
        @forelse($versions ?? [] as $v)
            <div class="flex items-start gap-3 py-3 border-b border-border last:border-0">
                <div class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 shrink-0"></div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-foreground">v{{ $v['version'] }}</div>
                    <div class="text-xs text-muted-foreground">{{ $v['author'] }} · {{ \Carbon\Carbon::parse($v['created_at'])->diffForHumans() }}</div>
                    <div class="text-xs text-muted-foreground mt-0.5">{{ $v['note'] ?? 'No notes' }}</div>
                </div>
                <div class="flex gap-1">
                    <button class="kt-btn kt-btn-ghost kt-btn-xs" title="View diff">Diff</button>
                    @can('admin')
                        <button class="kt-btn kt-btn-ghost kt-btn-xs text-warning" title="Rollback">Revert</button>
                    @endcan
                </div>
            </div>
        @empty
            <p class="text-sm text-muted-foreground">No versions yet.</p>
        @endforelse
    </div>
</div>

{{-- Schedule Modal --}}
<div id="modal-schedule" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Schedule publish</h2>
            <button class="modal-schedule-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-muted-foreground mb-1">Publish date & time</label>
                <input type="datetime-local" class="kt-input w-full" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1">Channels</label>
                <div class="flex gap-3">
                    @foreach(['Web','Social'] as $ch)
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                            <input type="checkbox" class="kt-checkbox" checked /> {{ $ch }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="modal-schedule-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-mono">Confirm schedule</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// History drawer
document.getElementById('btn-history')?.addEventListener('click', () => {
    const d = document.getElementById('drawer-history');
    d.classList.remove('hidden');
    setTimeout(() => d.classList.remove('translate-x-full'), 10);
});
document.getElementById('drawer-history-close')?.addEventListener('click', () => {
    const d = document.getElementById('drawer-history');
    d.classList.add('translate-x-full');
    setTimeout(() => d.classList.add('hidden'), 300);
});
// Schedule modal
document.getElementById('btn-schedule')?.addEventListener('click', () => {
    document.getElementById('modal-schedule').classList.remove('hidden');
    document.getElementById('modal-schedule').classList.add('flex');
});
document.querySelectorAll('.modal-schedule-close').forEach(b => {
    b.addEventListener('click', () => {
        document.getElementById('modal-schedule').classList.add('hidden');
        document.getElementById('modal-schedule').classList.remove('flex');
    });
});
// Auto-generate slug from title
document.getElementById('cms-title')?.addEventListener('input', function() {
    const slugField = document.getElementById('cms-slug');
    if (!slugField.dataset.manual) {
        slugField.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }
});
document.getElementById('cms-slug')?.addEventListener('input', function() {
    this.dataset.manual = '1';
});
</script>
@endpush

@endsection
