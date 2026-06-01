{{-- resources/views/partials/_quick_view_shell.blade.php --}}
{{--
  Generic sticky Quick-View panel.
  Include with: @include('partials._quick_view_shell', ['tabs' => [...], 'emptyLabel' => '...'])
  The host page's JS populates #qv-lead-name, #qv-lead-meta, #qv-open-link, #qv-body tabs.
--}}

@props([
    'panelId'    => 'quick-view-panel',
    'tabs'       => ['Overview', 'Details', 'Activity'],
    'emptyLabel' => 'Select a row to preview',
    'openRoute'  => '#',
])

<aside id="{{ $panelId }}"
       class="card border border-border rounded-xl overflow-hidden
              sticky top-[86px] h-[calc(100vh-120px)] flex flex-col">

    {{-- Header --}}
    <div class="px-4 py-3 border-b border-border flex items-start justify-between gap-3 bg-muted/20 shrink-0">
        <div class="min-w-0">
            <div id="qv-title" class="text-sm font-semibold text-foreground truncate">
                {{ $emptyLabel }}
            </div>
            <div id="qv-meta" class="text-xs text-muted-foreground mt-0.5"></div>
        </div>
        <a id="qv-open-link" href="#"
           class="kt-btn kt-btn-outline kt-btn-sm shrink-0 hidden">Open</a>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-border px-4 pt-2 shrink-0 overflow-x-auto">
        <div class="flex gap-1 min-w-max" id="qv-tab-list">
            @foreach ($tabs as $tab)
                <button data-qv-tab="{{ Str::slug($tab) }}"
                        class="qv-tab-btn kt-btn kt-btn-ghost kt-btn-sm
                               {{ $loop->first ? 'kt-btn-mono' : '' }}">
                    {{ $tab }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Body --}}
    <div class="flex-1 overflow-auto p-4 text-sm" id="qv-body">
        @foreach ($tabs as $tab)
            <div id="qv-tab-{{ Str::slug($tab) }}"
                 class="qv-tab-content {{ $loop->first ? '' : 'hidden' }} space-y-3">
                <p class="text-xs text-muted-foreground">{{ $emptyLabel }}</p>
            </div>
        @endforeach
    </div>

    {{-- Footer --}}
    <div id="qv-footer" class="border-t border-border px-4 py-3 bg-muted/10 shrink-0 hidden">
        <div id="qv-footer-actions" class="flex gap-2 flex-wrap"></div>
    </div>
</aside>
