{{-- resources/views/deals/partials/_parties_tab.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Seller --}}
    <div class="card border border-border rounded-xl p-4 space-y-3">
        <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide flex items-center gap-2">
            <i data-lucide="user" class="w-3.5 h-3.5"></i> Seller
        </h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground text-xs">Name</span>
                <strong>{{ $deal['seller_name'] ?? '—' }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground text-xs">Customer ID</span>
                <span class="font-mono text-xs">{{ $deal['seller_id'] ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground text-xs">Email</span>
                @if ($deal['seller_email'] ?? null)
                    <a href="mailto:{{ $deal['seller_email'] }}" class="text-xs text-primary hover:underline">
                        {{ $deal['seller_email'] }}
                    </a>
                @else
                    <span class="text-xs text-muted-foreground">—</span>
                @endif
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground text-xs">Phone</span>
                <span class="text-xs">{{ $deal['seller_phone'] ?? '—' }}</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-1 pt-2 border-t border-border">
            @if ($deal['kyc_verified'] ?? false)
                <span class="kt-badge kt-badge-success kt-badge-sm">KYC Verified</span>
            @else
                <span class="kt-badge kt-badge-warning kt-badge-sm">KYC Pending</span>
            @endif
            @if ($deal['seller_consent_email'] ?? false)
                <span class="kt-badge kt-badge-outline kt-badge-sm">Email ✔</span>
            @endif
            @if ($deal['seller_consent_sms'] ?? false)
                <span class="kt-badge kt-badge-outline kt-badge-sm">SMS ✔</span>
            @endif
        </div>
    </div>

    {{-- Buyer / Vendor --}}
    <div class="card border border-border rounded-xl p-4 space-y-3">
        <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide flex items-center gap-2">
            <i data-lucide="building-2" class="w-3.5 h-3.5"></i> Buyer / Vendor
        </h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-muted-foreground text-xs">Name</span>
                <strong>{{ $deal['buyer_name'] ?? '—' }}</strong>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground text-xs">Company</span>
                <span class="text-xs">{{ $deal['buyer_company'] ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground text-xs">Vendor ID</span>
                <span class="font-mono text-xs">{{ $deal['vendor_id'] ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted-foreground text-xs">Email</span>
                @if ($deal['buyer_email'] ?? null)
                    <a href="mailto:{{ $deal['buyer_email'] }}" class="text-xs text-primary hover:underline">
                        {{ $deal['buyer_email'] }}
                    </a>
                @else
                    <span class="text-xs text-muted-foreground">—</span>
                @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-1 pt-2 border-t border-border">
            @if ($deal['card_on_file'] ?? false)
                <span class="kt-badge kt-badge-primary kt-badge-sm">Card on file ✔</span>
            @else
                <span class="kt-badge kt-badge-destructive kt-badge-sm">No card on file</span>
            @endif
            @if ($deal['buyer_consent_email'] ?? false)
                <span class="kt-badge kt-badge-outline kt-badge-sm">Email ✔</span>
            @endif
        </div>
    </div>
</div>
