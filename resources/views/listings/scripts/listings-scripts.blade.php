@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', () => {

    const wizard =
        document.getElementById('add-listing-wizard');

    const detail =
        document.getElementById('listing-detail-modal');

    document
        .getElementById('btn-create-listing')
        .addEventListener('click', () => {

            wizard.classList.remove('hidden');
            wizard.classList.add('flex');

        });

    document
        .querySelectorAll('.close-modal')
        .forEach(btn => {

            btn.addEventListener('click', () => {

                document
                    .querySelectorAll('.fixed.inset-0.z-50')
                    .forEach(m => {

                        m.classList.add('hidden');
                        m.classList.remove('flex');

                    });

            });

        });

    document
        .querySelectorAll('.open-detail')
        .forEach(btn => {

            btn.addEventListener('click', () => {

                detail.classList.remove('hidden');
                detail.classList.add('flex');

            });

        });

});

</script>

@endpush