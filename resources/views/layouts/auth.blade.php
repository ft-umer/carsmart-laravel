<!DOCTYPE html>
<html class="h-full" data-kt-theme-mode="light" dir="ltr" lang="en">

<head>

    <base href="{{ asset('') }}">
    <title>Carsmart — @yield('title', 'Sign In')</title>
    <meta charset="utf-8" />
    <meta name="robots" content="follow, index" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />
    {{-- <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/app/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/app/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/media/app/favicon-16x16.png') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/media/app/favicon.ico') }}" /> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="antialiased flex h-full text-base text-foreground bg-background">

    {{-- Theme initialiser --}}
    <script>
        const defaultThemeMode = 'dark';

        let themeMode = localStorage.getItem('kt-theme') ||
            document.documentElement.getAttribute('data-kt-theme-mode') ||
            defaultThemeMode;

        if (themeMode === 'system') {
            themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        // 🔥 FORCE CLEAN STATE
        document.documentElement.classList.remove('light', 'dark');
        document.documentElement.classList.add(themeMode);

        // optional but recommended
        document.documentElement.setAttribute('data-theme', themeMode);
    </script>

    @yield('content')

    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <script>
    function updateLogos() {
    const isDark = document.documentElement.classList.contains('dark');

    const src = isDark
        ? "{{ asset('assets/media/app/default-logo-dark.svg') }}"
        : "{{ asset('assets/media/app/default-logo.svg') }}";

    const header = document.getElementById('logoHeader');
    const hero = document.getElementById('logoHero');

    if (header) header.src = src;
    if (hero) hero.src = src;
}

updateLogos();

new MutationObserver(updateLogos).observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class']
});
    </script>
    @stack('scripts')
</body>

</html>
