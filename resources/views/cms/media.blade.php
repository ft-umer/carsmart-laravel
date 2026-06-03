{{-- resources/views/cms/media.blade.php --}}
{{-- Phase 5 — CMS Media Library --}}
@extends('layouts.app')
@section('title', 'Media Library — CMS')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('cms.index') }}" class="hover:text-foreground transition-colors">CMS</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Media Library</span>
    </nav>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Media Library</h1>
            <p class="text-sm text-muted-foreground mt-0.5">{{ $totalMedia ?? 0 }} files</p>
        </div>
        <div class="flex gap-2">
            <button id="btn-upload" class="kt-btn kt-btn-mono">
                <i data-lucide="upload" class="w-4 h-4 mr-1"></i> Upload
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('cms.media') }}" class="card border border-border rounded-lg p-3 mb-5">
        <div class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-muted-foreground mb-1">Search</label>
                <input name="search" value="{{ $search ?? '' }}" type="search"
                       class="kt-input w-full" placeholder="Filename or alt text…" />
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs text-muted-foreground mb-1">Type</label>
                <select name="type" class="kt-input w-full">
                    <option value="">All types</option>
                    <option value="image" @selected(($type ?? '') === 'image')>Image</option>
                    <option value="video" @selected(($type ?? '') === 'video')>Video</option>
                    <option value="document" @selected(($type ?? '') === 'document')>Document</option>
                </select>
            </div>
            <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
            <a href="{{ route('cms.media') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
        </div>
    </form>

    {{-- Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @forelse($media ?? [] as $m)
            <div class="group relative card border border-border rounded-xl overflow-hidden cursor-pointer hover:border-primary/40 transition-colors media-item"
                 data-id="{{ $m['id'] }}">
                @if($m['type'] === 'image')
                    <img src="{{ $m['url'] }}" alt="{{ $m['name'] }}"
                         class="w-full aspect-square object-cover bg-muted" />
                @elseif($m['type'] === 'video')
                    <div class="w-full aspect-square bg-muted flex items-center justify-center">
                        <i data-lucide="video" class="w-8 h-8 text-muted-foreground"></i>
                    </div>
                @else
                    <div class="w-full aspect-square bg-muted flex items-center justify-center">
                        <i data-lucide="file" class="w-8 h-8 text-muted-foreground"></i>
                    </div>
                @endif
                <div class="p-2">
                    <p class="text-xs font-medium text-foreground truncate">{{ $m['name'] }}</p>
                    <p class="text-xs text-muted-foreground">{{ $m['size'] }}</p>
                </div>
                <div class="absolute top-1 right-1 hidden group-hover:flex gap-1">
                    <button class="p-1 bg-background/90 rounded text-foreground hover:bg-background shadow-sm">
                        <i data-lucide="copy" class="w-3 h-3"></i>
                    </button>
                    <button class="p-1 bg-destructive/90 rounded text-white hover:bg-destructive shadow-sm">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center text-muted-foreground">
                <i data-lucide="image" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                <p>No media uploaded yet.</p>
                <button id="btn-upload-empty" class="mt-3 kt-btn kt-btn-outline kt-btn-sm">Upload first file</button>
            </div>
        @endforelse
    </div>

    @if(isset($media) && method_exists($media, 'links'))
        <div class="mt-5">{{ $media->links() }}</div>
    @endif
</div>

{{-- Upload drag-drop modal --}}
<div id="modal-upload" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Upload Media</h2>
            <button class="upload-modal-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div id="drop-zone" class="border-2 border-dashed border-border rounded-xl p-12 text-center cursor-pointer hover:border-primary/50 transition-colors">
            <i data-lucide="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-muted-foreground/50"></i>
            <p class="text-sm font-medium text-foreground">Drag & drop or click to browse</p>
            <p class="text-xs text-muted-foreground mt-1">JPG, PNG, GIF, MP4, PDF — up to 100 MB each</p>
        </div>
        <div id="file-list" class="mt-3 space-y-2"></div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="upload-modal-close kt-btn kt-btn-ghost">Cancel</button>
            <button id="btn-do-upload" class="kt-btn kt-btn-mono" disabled>Upload files</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openUpload() {
    document.getElementById('modal-upload').classList.remove('hidden');
    document.getElementById('modal-upload').classList.add('flex');
}
document.getElementById('btn-upload')?.addEventListener('click', openUpload);
document.getElementById('btn-upload-empty')?.addEventListener('click', openUpload);
document.querySelectorAll('.upload-modal-close').forEach(b => b.addEventListener('click', () => {
    document.getElementById('modal-upload').classList.add('hidden');
    document.getElementById('modal-upload').classList.remove('flex');
}));

// Drag and drop visual
const dz = document.getElementById('drop-zone');
dz?.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('border-primary'); });
dz?.addEventListener('dragleave', () => dz.classList.remove('border-primary'));
dz?.addEventListener('drop', e => {
    e.preventDefault();
    dz.classList.remove('border-primary');
    const files = [...e.dataTransfer.files];
    renderFileList(files);
});
dz?.addEventListener('click', () => {
    const inp = document.createElement('input');
    inp.type = 'file';
    inp.multiple = true;
    inp.accept = 'image/*,video/*,.pdf';
    inp.onchange = () => renderFileList([...inp.files]);
    inp.click();
});

function renderFileList(files) {
    const list = document.getElementById('file-list');
    list.innerHTML = '';
    files.forEach(f => {
        list.innerHTML += `<div class="flex items-center gap-2 p-2 rounded-lg bg-muted/40">
            <i data-lucide="file" class="w-4 h-4 text-muted-foreground shrink-0"></i>
            <span class="text-sm flex-1 truncate">${f.name}</span>
            <span class="text-xs text-muted-foreground">${(f.size/1024/1024).toFixed(1)} MB</span>
        </div>`;
    });
    document.getElementById('btn-do-upload').disabled = files.length === 0;
    lucide.createIcons();
}
</script>
@endpush

@endsection
