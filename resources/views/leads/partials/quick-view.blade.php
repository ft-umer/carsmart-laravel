<div id="modal-quick-view" class="fixed inset-0 z-[10000] hidden">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" data-close-modal="modal-quick-view">
    </div>

    {{-- Dialog --}}
    <div class="absolute inset-0 flex items-center justify-center p-4">

        <div
            class="card w-full max-w-5xl h-[85vh]
                   border border-border rounded-xl
                   overflow-hidden flex flex-col">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-border flex justify-between">

                <div>
                    <div id="qv-lead-name"></div>
                    <div id="qv-lead-meta"></div>
                </div>

                <button type="button" data-close-modal="modal-quick-view" class="kt-btn kt-btn-ghost kt-btn-sm">
                    ✕
                </button>

            </div>

            {{-- ───────────────────────────────────────────────────────────────────── --}}
            {{-- QUICK VIEW PANEL                                                     --}}
            {{-- ───────────────────────────────────────────────────────────────────── --}}

            <div class="flex flex-col h-full">
                {{-- Header --}}
                <div class="px-4 py-3 border-b border-border flex items-start justify-between gap-3 bg-muted/20">

                    <div>

                        <div id="qv-lead-name" class="text-sm font-semibold text-foreground">

                            Select a lead

                        </div>

                        <div id="qv-lead-meta" class="text-xs text-muted-foreground mt-0.5">

                            Quick view will appear here

                        </div>

                    </div>

                    <a id="qv-open-link" href="#" class="kt-btn kt-btn-outline kt-btn-sm hidden">

                        Open

                    </a>

                </div>

                {{-- Tabs --}}
                <div class="border-b border-border px-4 pt-2">

                    <div id="qv-tab-list" class="flex gap-1">

                        <button data-qv-tab="overview" class="qv-tab-btn kt-btn kt-btn-ghost kt-btn-sm active-tab">

                            Overview

                        </button>

                        <button data-qv-tab="vehicles" class="qv-tab-btn kt-btn kt-btn-ghost kt-btn-sm">

                            Vehicles

                        </button>

                        <button data-qv-tab="tasks" class="qv-tab-btn kt-btn kt-btn-ghost kt-btn-sm">

                            Tasks

                        </button>

                        <button data-qv-tab="activity" class="qv-tab-btn kt-btn kt-btn-ghost kt-btn-sm">

                            Activity

                        </button>

                    </div>

                </div>

                {{-- Body --}}
                <div id="qv-body" class="flex-1 overflow-auto p-4 text-sm">
                    <div id="qv-tab-overview" class="qv-tab-content"></div>

                    <div id="qv-tab-vehicles" class="qv-tab-content hidden space-y-4">

                        <div>
                            <h4 id="qv-vehicle-title" class="font-medium">
                                No vehicle selected
                            </h4>

                            <p id="qv-vehicle-sub" class="text-xs text-muted-foreground">
                                —
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <button id="qv-btn-pull-val" class="kt-btn kt-btn-primary kt-btn-sm">
                                Pull Valuation
                            </button>

                            <button id="qv-btn-add-val" class="kt-btn kt-btn-outline kt-btn-sm">
                                Add Valuation
                            </button>

                            <button id="qv-btn-apply-pricing" class="kt-btn kt-btn-outline kt-btn-sm">
                                Apply Pricing
                            </button>
                        </div>

                        <div id="qv-val-fetch-status" class="hidden"></div>

                        <div id="qv-val-card" class="hidden card border border-border p-3">

                            <div id="qv-val-amount" class="text-lg font-semibold">
                                £0
                            </div>

                            <div id="qv-val-source" class="text-xs text-muted-foreground">
                            </div>

                            <div id="qv-val-delta" class="text-xs mt-1">
                            </div>

                        </div>

                        <table class="w-full text-xs">
                            <thead>
                                <tr>
                                    <th class="text-left p-2">Date</th>
                                    <th class="text-left p-2">Source</th>
                                    <th class="text-right p-2">Amount</th>
                                    <th class="text-left p-2">Notes</th>
                                    <th class="text-left p-2">Action</th>
                                </tr>
                            </thead>

                            <tbody id="qv-val-history-body"></tbody>
                        </table>

                    </div>

                    <div id="qv-tab-tasks" class="qv-tab-content hidden">

                        <div id="qv-tasks-list" class="space-y-2"></div>

                    </div>

                    <div id="qv-tab-activity" class="qv-tab-content hidden">

                        <div id="qv-activity-list" class="space-y-3"></div>

                    </div>
                  


                </div>
                  <div id="qv-footer" class="hidden border-t border-border p-4">
                        <button id="qv-btn-convert" class="kt-btn kt-btn-primary w-full">
                            Convert To Listing
                        </button>
                    </div>

            </div>
        </div>

    </div>
