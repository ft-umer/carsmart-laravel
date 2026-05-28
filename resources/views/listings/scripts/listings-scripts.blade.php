@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const listingModal = document.getElementById('listing-detail-modal');
            const quickModal = document.getElementById('quick-view-modal');

            // OPEN DETAIL
            document.addEventListener('click', (e) => {

                const detailBtn = e.target.closest('.open-detail');
                if (detailBtn) {

                    listingModal.classList.remove('hidden');
                    listingModal.classList.add('flex');

                    return;
                }

                const quickBtn = e.target.closest('.quick-view');
                if (quickBtn) {

                    quickModal.classList.remove('hidden');
                    quickModal.classList.add('flex');

                    return;
                }
            });

            // CLOSE ANY MODAL
            document.addEventListener('click', (e) => {

                if (e.target.closest('.close-modal')) {

                    document.querySelectorAll('#listing-detail-modal, #quick-view-modal')
                        .forEach(modal => {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        });
                }
            });

        });

        document.addEventListener('DOMContentLoaded', () => {
            const wizardModal = document.getElementById('add-listing-wizard');

            function closeWizard() {
                wizardModal.classList.add('hidden');
                wizardModal.classList.remove('flex');
            }
            let currentStep = 1;
            const totalSteps = 5;

            const showStep = (step) => {

                for (let i = 1; i <= totalSteps; i++) {
                    document.getElementById(`wizard-step-${i}`)
                        ?.classList.add('hidden');
                }

                document.getElementById(`wizard-step-${step}`)
                    ?.classList.remove('hidden');

                // Back button hide on step 1
                document.getElementById('wizard-back')?.style &&
                    (document.getElementById('wizard-back').style.display =
                        step === 1 ? 'none' : 'inline-flex');

            };

            // INIT
            showStep(currentStep);

            // NEXT
            document.getElementById('wizard-next')?.addEventListener('click', () => {

                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }

                if (currentStep === totalSteps) {
                    document.getElementById('wizard-next').textContent = 'Create Listing';
                }
            });

            // BACK
            document.getElementById('wizard-back')?.addEventListener('click', () => {

                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });



            wizardModal?.addEventListener('click', (e) => {

                // only close if clicking OUTSIDE modal box
                if (e.target === wizardModal) {

                    closeWizard();
                    currentStep = 1;
                    showStep(currentStep);
                }
            });
            
            
        });
        
    </script>
    
    <script>
document.addEventListener('DOMContentLoaded', () => {

    const listingModal = document.getElementById('listing-detail-modal');
    const modalContent = document.getElementById('modal-content');
    const quickModal = document.getElementById('quick-view-modal');

    // =========================
    // OPEN DETAIL (FETCH HTML)
    // =========================
    document.addEventListener('click', async (e) => {

        const btn = e.target.closest('.open-detail');
        if (btn) {

            const id = btn.dataset.id;

            // optional loading state
            modalContent.innerHTML = `
                <div class="p-6 text-sm text-muted-foreground">
                    Loading listing...
                </div>
            `;

            listingModal.classList.remove('hidden');
            listingModal.classList.add('flex');

            const res = await fetch(`/listings/${id}`);
            const html = await res.text();

            modalContent.innerHTML = html;

            return;
        }

        // QUICK VIEW (optional placeholder)
        const quickBtn = e.target.closest('.quick-view');
        if (quickBtn) {

            quickModal.classList.remove('hidden');
            quickModal.classList.add('flex');

            return;
        }

        // =========================
        // CLOSE MODALS
        // =========================

        if (
            e.target.closest('.close-modal') ||
            e.target.classList.contains('close-overlay')
        ) {
            listingModal.classList.add('hidden');
            listingModal.classList.remove('flex');

            quickModal?.classList.add('hidden');
            quickModal?.classList.remove('flex');
        }
    });

});
</script>
@endpush
