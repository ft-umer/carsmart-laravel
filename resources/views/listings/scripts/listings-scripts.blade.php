@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const listingModal = document.getElementById('listing-detail-modal');
            const quickModal = document.getElementById('quick-view-modal');

            // QUICK VIEW MODAL
            document.addEventListener('click', (e) => {

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

          
            const modalContent = document.getElementById('modal-content');
            const quickModal = document.getElementById('quick-view-modal');

            // =========================
            // QUICK VIEW (optional placeholder)
            // =========================
            document.addEventListener('click', (e) => {

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

      document.addEventListener('click', function (e) {

    const button = e.target.closest('[data-view]');
    if (!button) return;

    const view = button.dataset.view;

    const views = [
        'listings',
        'qa',
        'publication',
        'valuations',
        'exchange',
        'lifecycle'
    ];

    views.forEach(v => {
        document.getElementById(`view-${v}`)?.classList.add('hidden');
    });

    document.getElementById(`view-${view}`)?.classList.remove('hidden');

    document.querySelectorAll('[data-view]').forEach(btn => {
        btn.classList.remove('kt-btn-mono');
        btn.classList.add('kt-btn-ghost');
    });

    button.classList.remove('kt-btn-ghost');
    button.classList.add('kt-btn-mono');

    const isListings = view === 'listings';

    document.getElementById('listings-title')
        ?.classList.toggle('hidden', !isListings);

    document.getElementById('listings-actions')
        ?.classList.toggle('hidden', !isListings);

    document.getElementById('listings-filters')
        ?.classList.toggle('hidden', !isListings);
});

        });
      
    </script>
    
    <script>
document.addEventListener('click', function(e) {

    const tab = e.target.closest('.detail-tab');

    if (!tab) return;

    const container = tab.closest('.flex.flex-col');

    const panes = container.querySelectorAll('[data-tab-pane]');
    const tabs = container.querySelectorAll('.detail-tab');

    const target = tab.dataset.tab;

    panes.forEach(p => p.classList.add('hidden'));

    tabs.forEach(t => {
        t.classList.remove(
            'bg-background',
            'border',
            'border-b-0',
            'border-border'
        );

        t.classList.add('text-muted-foreground');
    });

    const activePane = container.querySelector(
        `[data-tab-pane="${target}"]`
    );

    if (activePane) {
        activePane.classList.remove('hidden');
    }

    tab.classList.add(
        'bg-background',
        'border',
        'border-b-0',
        'border-border'
    );

    tab.classList.remove('text-muted-foreground');
});
</script>
@endpush
