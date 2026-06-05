{{-- resources/views/disputes/partials/_evidence_tab.blade.php --}}
{{-- Phase 4 — S2: Dispute Case — Evidence tab --}}
<div class="space-y-4">

    {{-- Upload section --}}
    <div class="flex items-center justify-between mb-2">
        <h4 class="font-semibold text-sm">Evidence files</h4>
        <button onclick="document.getElementById('evidence-upload').click()"
            class="kt-btn kt-btn-outline kt-btn-sm">
            <i data-lucide="upload" class="w-3.5 h-3.5 mr-1"></i> Upload evidence
        </button>
        <input type="file" id="evidence-upload" class="hidden" multiple accept="image/*,.pdf,.doc,.docx">
    </div>

    {{-- Upload dropzone --}}
    <div class="border-2 border-dashed border-border rounded-xl p-6 text-center text-sm text-muted-foreground hover:bg-muted/20 transition-colors cursor-pointer"
        onclick="document.getElementById('evidence-upload').click()">
        <i data-lucide="file-plus" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
        <p>Drop photos, PDFs, or documents here</p>
        <p class="text-xs mt-1">Max 25 MB per file · Images, PDF, Word</p>
    </div>

    {{-- Existing evidence --}}
    @php
    $evidence = [
        ['id'=>1,'name'=>'condition-photos-front.jpg','type'=>'image','size'=>'2.4 MB','added_by'=>'AM','added_at'=>'3 Oct 2025 14:32','notes'=>'Front exterior — scratch visible on bumper'],
        ['id'=>2,'name'=>'condition-photos-rear.jpg','type'=>'image','size'=>'1.9 MB','added_by'=>'AM','added_at'=>'3 Oct 2025 14:33','notes'=>'Rear exterior'],
        ['id'=>3,'name'=>'inspection-report.pdf','type'=>'pdf','size'=>'340 KB','added_by'=>'SR','added_at'=>'4 Oct 2025 09:15','notes'=>'Full PDI report from collection agent'],
        ['id'=>4,'name'=>'seller-response.docx','type'=>'doc','size'=>'48 KB','added_by'=>'Seller','added_at'=>'5 Oct 2025 11:00','notes'=>'Seller statement disputing condition assessment'],
    ];
    @endphp

    <div class="space-y-3">
        @foreach ($evidence as $e)
            @php
            $icon = match($e['type']) {
                'image' => 'image',
                'pdf'   => 'file-text',
                default => 'file',
            };
            @endphp
            <div class="rounded-lg border border-border p-3 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-muted flex items-center justify-center flex-shrink-0">
                    <i data-lucide="{{ $icon }}" class="w-4 h-4 text-muted-foreground"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="font-medium text-sm truncate">{{ $e['name'] }}</p>
                        <span class="text-xs text-muted-foreground flex-shrink-0">{{ $e['size'] }}</span>
                    </div>
                    <p class="text-xs text-muted-foreground mt-0.5">{{ $e['notes'] }}</p>
                    <p class="text-xs text-muted-foreground mt-0.5">
                        Added by {{ $e['added_by'] }} · {{ $e['added_at'] }}
                    </p>
                </div>
                <div class="flex gap-1 flex-shrink-0">
                    <button class="kt-btn kt-btn-ghost kt-btn-xs" title="View">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                    </button>
                    <button class="kt-btn kt-btn-ghost kt-btn-xs" title="Download">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    </button>
                    <button class="kt-btn kt-btn-ghost kt-btn-xs text-destructive" title="Remove">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Evidence notes --}}
    <div class="card border border-border rounded-xl p-4">
        <label class="form-label text-sm">Investigator notes</label>
        <textarea rows="4" class="kt-textarea w-full text-sm mt-1"
            placeholder="Add investigation notes here…">Front bumper scratch confirmed on arrival photos. Seller disputes this was pre-existing.</textarea>
        <div class="flex justify-end mt-2">
            <button class="kt-btn kt-btn-primary kt-btn-sm">Save notes</button>
        </div>
    </div>

</div>
