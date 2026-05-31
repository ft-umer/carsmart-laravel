<div id="quick-view-modal"
     class="fixed inset-0 z-[10000] hidden items-center justify-center p-4">

    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/70 close-modal"></div>

    <!-- Modal -->
    <div class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto card bg-background">

        <!-- Header -->
        <div class="flex items-center justify-between border-b p-4">

            <div>
                <h2 class="text-lg font-semibold">
                    Quick View
                </h2>

                <div class="text-sm text-muted-foreground">
                    Listing Snapshot
                </div>
            </div>

            <button class="kt-btn kt-btn-sm kt-btn-ghost close-modal">
                ✕
            </button>

        </div>

        <!-- Body -->
        <div class="p-4">

            <div class="space-y-4">

                <div>
                    <h3 class="text-lg font-semibold">
                        BMW 330i M Sport 2019
                    </h3>

                    <div class="text-sm text-muted-foreground">
                        Listing LST-1023
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">

                    <div class="card p-3">
                        <div class="font-medium mb-2">
                            Vehicle
                        </div>

                        <div>VRM: AB19 CDE</div>
                        <div>VIN: WBAXX123456789</div>
                        <div>Mileage: 42,000</div>
                        <div>Fuel: Petrol</div>
                        <div>Transmission: Automatic</div>
                    </div>

                    <div class="card p-3">
                        <div class="font-medium mb-2">
                            Listing
                        </div>

                        <div>State: Draft</div>
                        <div>Owner: JR</div>
                        <div>QA: Needs Review</div>
                        <div>KYC: Pending</div>
                    </div>

                </div>

                <div class="grid md:grid-cols-4 gap-4">

                    <div class="card p-3">
                        <div class="text-xs text-muted-foreground">
                            Valuation
                        </div>

                        <div class="font-semibold">
                            £14,200
                        </div>
                    </div>

                    <div class="card p-3">
                        <div class="text-xs text-muted-foreground">
                            Guide
                        </div>

                        <div class="font-semibold">
                            £14,250
                        </div>
                    </div>

                    <div class="card p-3">
                        <div class="text-xs text-muted-foreground">
                            Reserve
                        </div>

                        <div class="font-semibold">
                            £14,000
                        </div>
                    </div>

                    <div class="card p-3">
                        <div class="text-xs text-muted-foreground">
                            BIN
                        </div>

                        <div class="font-semibold">
                            £15,495
                        </div>
                    </div>

                </div>

                <div class="card p-3">

                    <div class="font-medium mb-2">
                        Auction
                    </div>

                    <div>Code: AUC-1001</div>
                    <div>Status: Scheduled</div>

                </div>

            </div>

        </div>

        <!-- Footer -->
        <div class="border-t p-4 flex justify-end gap-2">

            <button class="kt-btn kt-btn-outline">
                Pull Valuation
            </button>

            <button class="kt-btn kt-btn-outline">
                Mark QA
            </button>

            <button class="kt-btn kt-btn-outline">
                Create Auction
            </button>

            <button class="kt-btn kt-btn-mono">
                Open Listing
            </button>

        </div>

    </div>

</div>