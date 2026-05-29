{{-- ─── Flash messages ──────────────────────────────────────────────────── --}}

@if (session('success'))

    <div
        id="flash-success"
        class="flex items-center gap-2 px-4 py-3 mb-4 rounded-lg
               bg-green-50 dark:bg-green-900/20
               border border-green-200 dark:border-green-800
               text-green-800 dark:text-green-300 text-sm">

        <i
            data-lucide="check-circle"
            class="w-4 h-4 shrink-0">
        </i>

        {{ session('success') }}

        <button
            type="button"
            onclick="this.parentElement.remove()"
            class="ml-auto text-green-600 hover:text-green-800">

            ✕
        </button>

    </div>

@endif