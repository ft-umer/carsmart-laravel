{{-- resources/views/layouts/app.blade.php
     Global Shell — B1 Header · B2 Left Navigation · B3 Right Side Panel · B4 Footer
--}}
<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="en">
<head>
    <base href="{{ asset('') }}">
    <title>Carsmart — @yield('title', 'Dashboard')</title>
    <meta charset="utf-8"/>
    <meta name="robots" content="follow, index"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet"/>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/app/apple-touch-icon.png') }}"/>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/app/favicon-32x32.png') }}"/>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/media/app/favicon-16x16.png') }}"/>
    <link rel="shortcut icon" href="{{ asset('assets/media/app/favicon.ico') }}"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="antialiased flex h-full text-base text-foreground bg-background demo1 kt-sidebar-fixed kt-header-fixed">

    {{-- Theme initialiser --}}
    <script>
        const defaultThemeMode = 'light';
        let themeMode;
        if (document.documentElement) {
            if (localStorage.getItem('kt-theme')) {
                themeMode = localStorage.getItem('kt-theme');
            } else if (document.documentElement.hasAttribute('data-kt-theme-mode')) {
                themeMode = document.documentElement.getAttribute('data-kt-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }
            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.classList.add(themeMode);
        }
    </script>

    <div class="flex grow">

        {{-- ══════════════════════════════════════════════════
             B2. Left Navigation — fixed, collapsible dark sidebar
        ══════════════════════════════════════════════════ --}}
        <x-sidebar />

        {{-- ══════════════════════════════════════════════════
             Wrapper — header + main content
        ══════════════════════════════════════════════════ --}}
        <div class="kt-wrapper flex grow flex-col">

            {{-- ══════════════════════════════════════════════════
                 B1. Header — always visible
                 · App switcher · Global search · Notifications
                 · My tasks · Help · Profile menu
            ══════════════════════════════════════════════════ --}}
            <header class="kt-header fixed top-0 z-10 start-0 end-0 flex items-stretch shrink-0 bg-background"
                    data-kt-sticky="true"
                    data-kt-sticky-class="border-b border-border"
                    data-kt-sticky-name="header"
                    id="header">

                <div class="kt-container-fixed flex justify-between items-stretch lg:gap-4" id="headerContainer">

                    {{-- Mobile logo + sidebar toggle --}}
                    <div class="flex gap-2.5 lg:hidden items-center -ms-1">
                        <a class="shrink-0" href="{{ route('dashboard') }}">
                            <img class="max-h-[25px] w-full" src="{{ asset('assets/media/app/mini-logo.svg') }}"/>
                        </a>
                        <button class="kt-btn kt-btn-icon kt-btn-ghost" data-kt-drawer-toggle="#sidebar">
                            <i class="ki-filled ki-menu"></i>
                        </button>
                    </div>

                    {{-- Desktop: App Switcher --}}
                    <div class="hidden lg:flex items-center gap-2 me-auto ms-4">
                        @php $currentApp = session('app_target', 'admin'); @endphp
                        <div class="kt-menu" data-kt-menu="true">
                            <div class="kt-menu-item"
                                 data-kt-menu-item-offset="0,10px"
                                 data-kt-menu-item-placement="bottom-start"
                                 data-kt-menu-item-toggle="dropdown"
                                 data-kt-menu-item-trigger="click|lg:hover">
                                <button class="kt-menu-toggle kt-btn kt-btn-ghost flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-lg">
                                    <i class="ki-filled ki-setting-3 text-base"></i>
                                    <span>{{ $currentApp === 'crm' ? 'Customer Relationship Management' : 'Admin' }}</span>
                                    <i class="ki-filled ki-down text-xs"></i>
                                </button>
                                <div class="kt-menu-dropdown kt-menu-default w-[220px]" data-kt-menu-dismiss="true">
                                    <div class="kt-menu-item">
                                        <a class="kt-menu-link {{ $currentApp === 'admin' ? 'kt-menu-item-active' : '' }}"
                                           href="{{ route('switch-app', 'admin') }}">
                                            <span class="kt-menu-icon"><i class="ki-filled ki-home-2"></i></span>
                                            <span class="kt-menu-title">Admin</span>
                                        </a>
                                    </div>
                                    <div class="kt-menu-item">
                                        <a class="kt-menu-link {{ $currentApp === 'crm' ? 'kt-menu-item-active' : '' }}"
                                           href="{{ route('switch-app', 'crm') }}">
                                            <span class="kt-menu-icon"><i class="ki-filled ki-people"></i></span>
                                            <span class="kt-menu-title">Customer Relationship Management</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right-side topbar actions --}}
                    <div class="flex items-center gap-2.5">

                        {{-- Global Search (B1) --}}
                        <button class="group kt-btn kt-btn-ghost kt-btn-icon size-9 rounded-full hover:bg-primary/10 hover:[&_i]:text-primary"
                                data-kt-modal-toggle="#search_modal"
                                title="Search (f)"
                                aria-label="Global search">
                            <i class="ki-filled ki-magnifier text-lg group-hover:text-primary"></i>
                        </button>

                        {{-- Notifications (B1) --}}
                        <button class="kt-btn kt-btn-ghost kt-btn-icon size-9 rounded-full hover:bg-primary/10 hover:[&_i]:text-primary relative"
                                data-kt-drawer-toggle="#notifications_drawer"
                                title="Notifications"
                                aria-label="Open notifications">
                            <i class="ki-filled ki-notification-status text-lg"></i>
                            <span class="absolute top-1.5 end-1.5 size-2 rounded-full bg-danger"></span>
                        </button>

                        {{-- My Tasks (B1) --}}
                        <button class="kt-btn kt-btn-ghost kt-btn-icon size-9 rounded-full hover:bg-primary/10 hover:[&_i]:text-primary"
                                data-kt-drawer-toggle="#tasks_drawer"
                                title="My tasks"
                                aria-label="Open task inbox">
                            <i class="ki-filled ki-clipboard text-lg"></i>
                        </button>

                        {{-- Help (B1) --}}
                        <button class="kt-btn kt-btn-ghost kt-btn-icon size-9 rounded-full hover:bg-primary/10 hover:[&_i]:text-primary"
                                data-kt-modal-toggle="#help_modal"
                                title="Help (?)"
                                aria-label="Open help centre">
                            <i class="ki-filled ki-information-2 text-lg"></i>
                        </button>

                        {{-- Profile Menu (B1) --}}
                        <div class="kt-menu" data-kt-menu="true">
                            <div class="kt-menu-item"
                                 data-kt-menu-item-offset="0,10px"
                                 data-kt-menu-item-placement="bottom-end"
                                 data-kt-menu-item-toggle="dropdown"
                                 data-kt-menu-item-trigger="click|lg:hover">
                                <div class="kt-menu-toggle cursor-pointer">
                                    <div class="kt-avatar size-9 rounded-full">
                                        <div class="kt-avatar-image rounded-full bg-primary/10 flex items-center justify-center text-primary font-semibold text-sm">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="kt-avatar-indicator kt-avatar-status kt-avatar-status-online bottom-0 end-0"></div>
                                    </div>
                                </div>
                                <div class="kt-menu-dropdown kt-menu-default w-[200px]" data-kt-menu-dismiss="true">
                                    <div class="px-4 py-3 border-b border-border">
                                        <div class="text-sm font-semibold text-mono">{{ auth()->user()->name ?? 'User' }}</div>
                                        <div class="text-xs text-muted-foreground mt-0.5">{{ auth()->user()->email ?? '' }}</div>
                                    </div>
                                    <div class="kt-menu-item">
                                        <a class="kt-menu-link" href="#">
                                            <span class="kt-menu-icon"><i class="ki-filled ki-profile-circle"></i></span>
                                            <span class="kt-menu-title">User settings</span>
                                        </a>
                                    </div>
                                    <div class="border-t border-border mt-1 pt-1">
                                        <div class="kt-menu-item">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="kt-menu-link w-full text-start">
                                                    <span class="kt-menu-icon"><i class="ki-filled ki-exit-right"></i></span>
                                                    <span class="kt-menu-title">Sign out</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end topbar --}}
                </div>
            </header>
            {{-- ── End Header ── --}}

            {{-- ══════════════════════════════════════════════════
                 Main content
            ══════════════════════════════════════════════════ --}}
            <main class="grow pt-[--kt-header-height]" id="main_content">
                {{ $slot ?? '' }}
                @yield('content')
            </main>

            {{-- ══════════════════════════════════════════════════
                 B4. Footer — pagination + sticky page actions
            ══════════════════════════════════════════════════ --}}
            @hasSection('page_actions')
            <div class="sticky bottom-0 z-10 bg-background border-t border-border px-6 py-3 flex items-center justify-end gap-3">
                @yield('page_actions')
            </div>
            @endif

        </div>{{-- end kt-wrapper --}}
    </div>{{-- end flex grow --}}

    {{-- ══════════════════════════════════════════════════
         B3. Right Side Panel — context panel slide-over
    ══════════════════════════════════════════════════ --}}
    <div class="hidden kt-drawer kt-drawer-end flex-col max-w-[90%] w-[520px] top-0 bottom-0 end-0 bg-background border-s border-border"
         data-kt-drawer="true"
         data-kt-drawer-container="body"
         id="context_panel">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-border shrink-0">
            <h3 class="text-sm font-semibold text-mono" id="context_panel_title">Quick view</h3>
            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost" data-kt-drawer-dismiss="true" aria-label="Close panel">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        {{-- Tabs: Summary · Edit · Notes · Communications --}}
        <div class="kt-tabs kt-tabs-line px-5" data-kt-tabs="true">
            <div class="flex items-center gap-5">
                <button class="kt-tab-toggle py-3 text-sm active" data-kt-tab-toggle="#ctx_tab_summary">Summary</button>
                <button class="kt-tab-toggle py-3 text-sm" data-kt-tab-toggle="#ctx_tab_edit">Edit</button>
                <button class="kt-tab-toggle py-3 text-sm" data-kt-tab-toggle="#ctx_tab_notes">Notes</button>
                <button class="kt-tab-toggle py-3 text-sm" data-kt-tab-toggle="#ctx_tab_comms">Communications</button>
            </div>
        </div>
        <div class="grow overflow-y-auto px-5 py-4" id="context_panel_body">
            <div id="ctx_tab_summary"></div>
            <div id="ctx_tab_edit" class="hidden"></div>
            <div id="ctx_tab_notes" class="hidden"></div>
            <div id="ctx_tab_comms" class="hidden"></div>
        </div>
        <div class="shrink-0 flex items-center gap-3 px-5 py-3.5 border-t border-border">
            <button class="kt-btn kt-btn-primary kt-btn-sm" id="ctx_panel_save">Save</button>
            <button class="kt-btn kt-btn-ghost kt-btn-sm" data-kt-drawer-dismiss="true">Close</button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         Notifications Drawer
    ══════════════════════════════════════════════════ --}}
    <div class="hidden kt-drawer kt-drawer-end card flex-col max-w-[90%] w-[450px] top-5 bottom-5 end-5 rounded-xl border border-border"
         data-kt-drawer="true"
         data-kt-drawer-container="body"
         id="notifications_drawer">
        <div class="flex items-center justify-between gap-2.5 text-sm text-mono font-semibold px-5 py-2.5 border-b border-b-border">
            Notifications
            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-drawer-dismiss="true" aria-label="Close notifications">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="kt-tabs kt-tabs-line justify-between px-5 mb-2" data-kt-tabs="true">
            <div class="flex items-center gap-5">
                <button class="kt-tab-toggle py-3 active" data-kt-tab-toggle="#notif_all">All</button>
                <button class="kt-tab-toggle py-3 relative" data-kt-tab-toggle="#notif_inbox">
                    Inbox
                    <span class="rounded-full bg-green-500 size-[5px] absolute top-2 end-0 transform translate-y-1/2 translate-x-full"></span>
                </button>
            </div>
        </div>
        <div class="grow kt-scrollable-y-auto p-5 text-sm text-muted-foreground" id="notif_all">
            No new notifications.
        </div>
        <div class="hidden grow kt-scrollable-y-auto p-5 text-sm text-muted-foreground" id="notif_inbox">
            Your inbox is empty.
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         Tasks Drawer
    ══════════════════════════════════════════════════ --}}
    <div class="hidden kt-drawer kt-drawer-end flex-col max-w-[90%] w-[420px] top-0 bottom-0 end-0 bg-background border-s border-border"
         data-kt-drawer="true"
         data-kt-drawer-container="body"
         id="tasks_drawer">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-border shrink-0">
            <h3 class="text-sm font-semibold text-mono">My tasks</h3>
            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost" data-kt-drawer-dismiss="true" aria-label="Close tasks">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="grow overflow-y-auto p-5 text-sm text-muted-foreground">
            Your task inbox is empty.
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         Global Search Modal
    ══════════════════════════════════════════════════ --}}
    <div class="hidden kt-modal" data-kt-modal="true" id="search_modal">
        <div class="kt-modal-content max-w-[600px] w-full mt-[10vh] mx-auto">
            <div class="kt-card">
                <div class="kt-card-content p-4">
                    <div class="kt-input flex items-center gap-2">
                        <i class="ki-filled ki-magnifier text-muted-foreground"></i>
                        <input type="search"
                               class="grow bg-transparent outline-none text-sm"
                               placeholder="Search lot number, listing, auction, registration, person, company, email, phone…"
                               id="global_search_input"
                               autofocus
                               autocomplete="off"/>
                        <kbd class="text-xs text-muted-foreground border border-border rounded px-1.5 py-0.5 hidden lg:block">Esc</kbd>
                    </div>
                    <div class="mt-3 text-xs text-muted-foreground px-1" id="search_hint">
                        Start typing to search…
                    </div>
                    <div id="search_results" class="hidden mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         Help Modal
    ══════════════════════════════════════════════════ --}}
    <div class="hidden kt-modal" data-kt-modal="true" id="help_modal">
        <div class="kt-modal-content max-w-[480px] w-full mt-[15vh] mx-auto">
            <div class="kt-card">
                <div class="kt-card-header flex items-center justify-between">
                    <h3 class="kt-card-title text-base">Help centre</h3>
                    <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost" data-kt-modal-dismiss="true" aria-label="Close help">
                        <i class="ki-filled ki-cross"></i>
                    </button>
                </div>
                <div class="kt-card-content p-5 text-sm text-secondary-foreground">
                    Browse documentation, keyboard shortcuts, and support resources.
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="{{ asset('assets/js/core.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>

    {{-- B5. Keyboard shortcuts (desktop) --}}
    <script>
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (e.key === 'f' && !e.ctrlKey && !e.metaKey) {
            e.preventDefault();
            document.getElementById('global_search_input')?.focus();
            KTModal.getInstance(document.getElementById('search_modal'))?.show();
        }
        if (e.key === '?') {
            KTModal.getInstance(document.getElementById('help_modal'))?.show();
        }
        if (e.key === 'g') {
            window._ktNavPrefix = true;
            return;
        }
        if (window._ktNavPrefix) {
            window._ktNavPrefix = false;
            if (e.key === 'd') window.location.href = '{{ route("dashboard") }}';
            if (e.key === 'l') window.location.href = '{{ route("listings.index") }}';
            if (e.key === 'a') window.location.href = '{{ route("auctions.index") }}';
        }
        if (e.key === '.' && !e.ctrlKey) {
            /* quick actions palette — stub */ 
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
