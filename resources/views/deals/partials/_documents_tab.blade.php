{{-- resources/views/deals/partials/_documents_tab.blade.php --}}
<div class="card border border-border rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between">
        <h3 class="text-sm font-semibold">Documents</h3>
        <button id="btn-upload-doc" class="kt-btn kt-btn-mono kt-btn-sm">
            <i data-lucide="upload" class="w-3.5 h-3.5 mr-1"></i>Upload
        </button>
    </div>
    <div class="p-4">
        @if (!empty($deal['documents']))
            <div class="space-y-2">
                @foreach ($deal['documents'] as $doc)
                    <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-border
                                bg-muted/10 hover:bg-muted/30 transition-colors">
                        <i data-lucide="{{ str_contains(strtolower($doc['name'] ?? ''), 'photo') ? 'image' : 'file-text' }}"
                           class="w-4 h-4 text-muted-foreground shrink-0"></i>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium truncate">{{ $doc['name'] ?? 'Document' }}</div>
                            <div class="text-xs text-muted-foreground">{{ $doc['uploaded_at'] ?? '' }} · {{ $doc['uploaded_by'] ?? '' }}</div>
                        </div>
                        @if ($doc['required'] ?? false)
                            <span class="kt-badge kt-badge-warning kt-badge-sm shrink-0">Required</span>
                        @endif
                        <a href="{{ $doc['url'] ?? '#' }}"
                           class="kt-btn kt-btn-ghost kt-btn-sm shrink-0" target="_blank">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-sm text-muted-foreground">
                <i data-lucide="folder-open" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                No documents uploaded yet. V5C, MOT, and condition photos required before payout.
            </div>
        @endif

        {{-- Required documents checklist --}}
        <div class="mt-4 pt-4 border-t border-border">
            <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">Required for payout</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                @foreach (['V5C' => $deal['v5c_uploaded'] ?? false, 'MOT certificate' => $deal['mot_uploaded'] ?? false, 'Condition photos' => $deal['photos_uploaded'] ?? false, 'Buyer signature' => $deal['buyer_signed'] ?? false, 'Seller signature' => $deal['seller_signed'] ?? false] as $label => $done)
                    <div class="flex items-center gap-2 px-2 py-1.5 rounded border {{ $done ? 'border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-700' : 'border-border bg-muted/20' }}">
                        <i data-lucide="{{ $done ? 'check-circle' : 'circle' }}"
                           class="w-3.5 h-3.5 {{ $done ? 'text-green-500' : 'text-muted-foreground' }} shrink-0"></i>
                        <span class="{{ $done ? 'text-green-800 dark:text-green-300' : 'text-muted-foreground' }}">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
