{{--
    resources/views/auctions/partials/participants-modal.blade.php
    A7 — Participants & Sets (standalone modal)
--}}

<div id="participants-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-md" data-modal-backdrop></div>

    <div role="dialog"
         class="relative w-full max-w-2xl mx-auto card rounded-xl overflow-hidden border border-border
                shadow-2xl bg-background flex flex-col max-h-[88vh] opacity-0 scale-95 transition-all">

        <div class="p-4 border-b border-border flex items-center justify-between shrink-0">
            <h3 class="text-base font-semibold">Participants &amp; Sets</h3>
            <button data-modal-close class="kt-btn kt-btn-icon kt-btn-ghost">✕</button>
        </div>

        <div class="p-4 space-y-4 overflow-auto flex-1">

            {{-- Saved sets --}}
            <div class="flex items-center gap-3 flex-wrap">
                <select id="p-load-set" class="kt-input flex-1">
                    <option value="">Load saved set…</option>
                    <option>Prestige Set A</option>
                    <option>Trade Network B</option>
                </select>
                <button id="p-btn-create-set" class="kt-btn kt-btn-outline">Create new set</button>
                <button id="p-btn-save-set"   class="kt-btn kt-btn-ghost">Save current as set</button>
            </div>

            {{-- Invite --}}
            <div class="flex gap-2">
                <input id="p-invite-input" class="kt-input flex-1"
                       placeholder="Vendor name, email or ID" />
                <button id="p-btn-invite" class="kt-btn kt-btn-mono">Invite</button>
            </div>

            {{-- Eligibility gates reminder --}}
            <div class="text-xs text-muted-foreground bg-muted/30 rounded p-2 border border-border">
                Gate requirements: <strong>KYC/KYB Verified</strong> and <strong>Card on file</strong>.
                Vendors failing gates cannot bid even if invited.
            </div>

            {{-- Invitations table --}}
            <div class="overflow-auto border border-border rounded">
                <table class="w-full text-sm">
                    <thead class="bg-muted/40">
                        <tr>
                            <th class="p-2 text-left">Vendor</th>
                            <th class="p-2 text-left">KYB</th>
                            <th class="p-2 text-left">Card</th>
                            <th class="p-2 text-left">Status</th>
                            <th class="p-2 text-left">Last seen</th>
                            <th class="p-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="p-invitations-tbody" class="bg-background divide-y divide-border">
                        <tr>
                            <td colspan="6" class="p-4 text-center text-xs text-muted-foreground">
                                No participants yet
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-3 border-t border-border flex justify-end shrink-0">
            <button data-modal-close class="kt-btn kt-btn-ghost">Close</button>
        </div>
    </div>
</div>