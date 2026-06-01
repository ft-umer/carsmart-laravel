{{-- resources/views/payments/partials/_wallet_funding.blade.php --}}
<div class="space-y-4">

    {{-- Card on file --}}
    <div class="card border border-border rounded-xl p-4 space-y-3">
        <h3 class="text-sm font-semibold flex items-center gap-2">
            <i data-lucide="credit-card" class="w-4 h-4 opacity-60"></i> Card on file
        </h3>
        @if ($wallet['payment_method'] ?? null)
            @php $pm = $wallet['payment_method']; @endphp
            <div class="flex items-center justify-between gap-3 p-3 rounded-lg border border-border bg-muted/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-7 rounded bg-muted flex items-center justify-center text-xs font-bold text-muted-foreground">
                        {{ strtoupper($pm['brand'] ?? 'CARD') }}
                    </div>
                    <div>
                        <div class="text-sm font-medium">•••• {{ $pm['last4'] ?? '****' }}</div>
                        <div class="text-xs text-muted-foreground">Expires {{ $pm['expiry'] ?? '—' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="kt-badge kt-badge-success kt-badge-sm">{{ $pm['status'] ?? 'Verified' }}</span>
                    <button data-action="replace-card"
                            class="kt-btn kt-btn-outline kt-btn-sm">Replace</button>
                    <button data-action="remove-card"
                            class="kt-btn kt-btn-ghost kt-btn-sm text-destructive">Remove</button>
                </div>
            </div>
            <div class="text-xs text-muted-foreground">
                Added by {{ $pm['added_by'] ?? '—' }} · {{ $pm['added_at'] ?? '' }}
            </div>
        @else
            <div class="rounded-lg border border-dashed border-border p-6 text-center text-sm text-muted-foreground">
                <i data-lucide="credit-card" class="w-6 h-6 mx-auto mb-2 opacity-30"></i>
                No card on file. Send a setup link to the vendor.
            </div>
            <button id="btn-send-setup-link" class="kt-btn kt-btn-mono w-full">
                <i data-lucide="send" class="w-4 h-4 mr-1"></i>Send card setup link
            </button>
        @endif
    </div>

    {{-- Mandate info --}}
    <div class="card border border-border rounded-xl p-4 space-y-2">
        <h3 class="text-sm font-semibold">Mandate (MIT)</h3>
        <p class="text-xs text-muted-foreground">
            Cards are stored under a merchant-initiated transaction mandate. The vendor agreed
            to the mandate text during card setup. Full card data is never stored — only
            brand, last four digits, and expiry are visible.
        </p>
        @if ($wallet['mandate_accepted_at'] ?? null)
            <div class="text-xs text-muted-foreground">
                Mandate accepted: <strong class="text-foreground">{{ $wallet['mandate_accepted_at'] }}</strong>
                by {{ $wallet['mandate_accepted_by'] ?? '—' }}
            </div>
        @endif
    </div>
</div>
