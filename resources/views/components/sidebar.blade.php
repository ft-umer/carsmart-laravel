@php
    $navigation = [
        [
            'label' => 'Dashboard',
            'icon' => 'ki-element-11',
            'route' => 'dashboard',
            'children' => [
                ['label' => 'Overview', 'route' => 'dashboard'],
                ['label' => 'Analytics', 'route' => 'dashboard.analytics'],
                ['label' => 'Activity', 'route' => 'dashboard.activity'],
            ],
        ],
        [
            'label' => 'Leads',
            'icon' => 'ki-user-circle',
            'route' => 'leads.index',
            'children' => [
                ['label' => 'All Leads', 'route' => 'leads.index'],
                ['label' => 'New Leads', 'route' => 'leads.new'],
                ['label' => 'Qualified', 'route' => 'leads.qualified'],
                ['label' => 'Archived', 'route' => 'leads.archived'],
            ],
        ],
        [
            'label' => 'Listings',
            'icon' => 'ki-row-horizontal',
            'route' => 'listings.index',
            'children' => [
                ['label' => 'All Listings', 'route' => 'listings.index'],
                ['label' => 'Active Listings', 'route' => 'listings.index'],
                ['label' => 'Drafts', 'route' => 'listings.index'],
            ],
        ],
        [
            'label' => 'Auctions',
            'icon' => 'ki-price-tag',
            'route' => 'auctions.index',
            'children' => [
                ['label' => 'Live Auctions', 'route' => 'auctions.live'],
                ['label' => 'Upcoming', 'route' => 'auctions.upcoming'],
                ['label' => 'Closed', 'route' => 'auctions.closed'],
                ['label' => 'Bids', 'route' => 'auctions.bids'],
            ],
        ],
        [
            'label' => 'Editions',
            'icon' => 'ki-book-open',
            'route' => 'editions.index',
            'children' => [
                ['label' => 'All Editions', 'route' => 'editions.index'],
                ['label' => 'Create Edition', 'route' => 'editions.create'],
                ['label' => 'Schedules', 'route' => 'editions.schedules'],
            ],
        ],
        [
            'label' => 'Vendors',
            'icon' => 'ki-shop',
            'route' => 'vendors.index',
            'children' => [
                ['label' => 'Vendor Directory', 'route' => 'vendors.index'],
                ['label' => 'Applications', 'route' => 'vendors.applications'],
                ['label' => 'Performance', 'route' => 'vendors.performance'],
            ],
        ],
        [
            'label' => 'Customers',
            'icon' => 'ki-people',
            'route' => 'customers.index',
            'children' => [
                ['label' => 'Customer List', 'route' => 'customers.index'],
                ['label' => 'Segments', 'route' => 'customers.segments'],
                ['label' => 'Support Requests', 'route' => 'customers.support'],
                ['label' => 'Purchase History', 'route' => 'customers.history'],
            ],
        ],
        [
            'label' => 'Payments',
            'icon' => 'ki-dollar',
            'route' => 'payments.index',
            'children' => [
                ['label' => 'Transactions', 'route' => 'payments.transactions'],
                ['label' => 'Invoices', 'route' => 'payments.invoices'],
                ['label' => 'Refunds', 'route' => 'payments.refunds'],
                ['label' => 'Payouts', 'route' => 'payments.payouts'],
            ],
        ],
        [
            'label' => 'Logistics',
            'icon' => 'ki-delivery',
            'route' => 'logistics.index',
            'children' => [
                ['label' => 'Shipments', 'route' => 'logistics.shipments'],
                ['label' => 'Tracking', 'route' => 'logistics.tracking'],
                ['label' => 'Warehouses', 'route' => 'logistics.warehouses'],
                ['label' => 'Delivery Status', 'route' => 'logistics.status'],
            ],
        ],
        [
            'label' => 'Disputes',
            'icon' => 'ki-shield-cross',
            'route' => 'disputes.index',
            'children' => [
                ['label' => 'Open Cases', 'route' => 'disputes.open'],
                ['label' => 'Resolved', 'route' => 'disputes.resolved'],
                ['label' => 'Escalations', 'route' => 'disputes.escalations'],
            ],
        ],
        [
            'label' => 'Content Management',
            'icon' => 'ki-abstract-26',
            'route' => 'cms.index',
            'children' => [
                ['label' => 'Pages', 'route' => 'cms.pages'],
                ['label' => 'Blogs', 'route' => 'cms.blogs'],
                ['label' => 'Media Library', 'route' => 'cms.media'],
                ['label' => 'SEO', 'route' => 'cms.seo'],
            ],
        ],
        [
            'label' => 'Automations',
            'icon' => 'ki-abstract-13',
            'route' => 'automations.index',
            'children' => [
                ['label' => 'Workflows', 'route' => 'automations.workflows'],
                ['label' => 'Email Automation', 'route' => 'automations.email'],
                ['label' => 'Triggers', 'route' => 'automations.triggers'],
                ['label' => 'Scheduled Tasks', 'route' => 'automations.scheduled'],
            ],
        ],
        [
            'label' => 'Reports',
            'icon' => 'ki-chart-line',
            'route' => 'reports.index',
            'children' => [
                ['label' => 'Sales Reports', 'route' => 'reports.sales'],
                ['label' => 'User Reports', 'route' => 'reports.users'],
                ['label' => 'Auction Reports', 'route' => 'reports.auctions'],
                ['label' => 'Exports', 'route' => 'reports.exports'],
            ],
        ],
        [
            'label' => 'Settings',
            'icon' => 'ki-setting-2',
            'route' => 'settings.index',
            'children' => [
                ['label' => 'General', 'route' => 'settings.general'],
                ['label' => 'Users and Roles', 'route' => 'settings.users'],
                ['label' => 'Integrations', 'route' => 'settings.integrations'],
                ['label' => 'Security', 'route' => 'settings.security'],
            ],
        ],
        [
            'label' => 'Notifications',
            'icon' => 'ki-notification-status',
            'route' => 'notifications.index',
            'children' => [
                ['label' => 'Inbox', 'route' => 'notifications.inbox'],
                ['label' => 'Templates', 'route' => 'notifications.templates'],
                ['label' => 'Preferences', 'route' => 'notifications.preferences'],
            ],
        ],
        [
            'label' => 'Tasks',
            'icon' => 'ki-calendar-tick',
            'route' => 'tasks.index',
            'children' => [
                ['label' => 'My Tasks', 'route' => 'tasks.mine'],
                ['label' => 'Team Tasks', 'route' => 'tasks.team'],
                ['label' => 'Completed', 'route' => 'tasks.completed'],
            ],
        ],
    ];
@endphp

<div class="kt-sidebar dark bg-background border-e border-e-border fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]"
    data-kt-drawer="true" data-kt-drawer-class="kt-drawer kt-drawer-start top-0 bottom-0" id="sidebar">

    {{-- Sidebar header with logo and collapse toggle --}}
    <div class="kt-sidebar-header hidden lg:flex items-center relative justify-between px-3 lg:px-6 shrink-0"
        id="sidebar_header">
        <a href="{{ route('dashboard') }}">
            <img class="default-logo min-h-[22px] max-w-none"
                src="{{ asset('assets/media/app/default-logo-dark.svg') }}" />
            <img class="small-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/mini-logo.svg') }}" />
        </a>
        <div data-kt-toggle="body" data-kt-toggle-class="kt-sidebar-collapse" id="sidebar_toggle">
            <div class="hidden dark:block">
                <button
                    class="kt-btn kt-btn-outline kt-btn-icon size-[30px] bg-white border border-white hover:[&_i]:text-black/80 border border-black/10! absolute start-full top-2/4 -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4"
                    aria-label="Collapse sidebar">
                    <i
                        class="ki-filled ki-black-left-line kt-toggle-active:rotate-180 transition-all duration-300 rtl:rotate-180 rtl:kt-toggle-active:rotate-0"></i>
                </button>
            </div>
            <div class="dark:hidden light">
                <button
                    class="kt-btn kt-btn-outline kt-btn-icon size-[30px] rounded-lg absolute start-full top-2/4 -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4"
                    aria-label="Collapse sidebar">
                    <i
                        class="ki-filled ki-black-left-line kt-toggle-active:rotate-180 transition-all duration-300 rtl:rotate-180 rtl:kt-toggle-active:rotate-0"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Scrollable nav content --}}
    <div class="kt-sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
        <div class="kt-scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3" data-kt-scrollable="true"
            data-kt-scrollable-dependencies="#sidebar_header" data-kt-scrollable-height="auto"
            data-kt-scrollable-offset="0px" data-kt-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">

            <div class="kt-menu flex flex-col grow gap-0.5" data-kt-menu="true"
                data-kt-menu-accordion-expand-all="false" id="sidebar_menu">

                @foreach ($navigation as $menu)
                    @php
                        $isActive =
                            request()->routeIs($menu['route']) ||
                            request()->routeIs(str_replace('.index', '.*', $menu['route']));
                        $hasActiveChild = collect($menu['children'])->contains(
                            fn($c) => request()->routeIs($c['route'] . '*'),
                        );
                        $open = $isActive || $hasActiveChild;
                    @endphp

                    @if (count($menu['children']) > 0)
                        {{-- Accordion primary menu item --}}
                        <div class="kt-menu-item {{ $open ? 'kt-menu-item-open' : '' }}"
                            data-kt-menu-item-toggle="accordion" data-kt-menu-item-trigger="click">

                            <div
                                class="kt-menu-link flex items-center border border-transparent transition-all duration-200
        {{ $open ? 'bg-accent/60 rounded-xl' : 'hover:bg-accent/60 hover:rounded-xl' }}
        gap-[14px] px-[10px] py-[10px] cursor-pointer">

                                <span class="kt-menu-icon text-muted-foreground w-[20px]">
                                    <i class="ki-filled {{ $menu['icon'] }} text-base"></i>
                                </span>

                                <span class="kt-menu-title text-sm font-medium text-foreground grow">
                                    {{ $menu['label'] }}
                                </span>

                                <span class="kt-menu-arrow transition-transform duration-300">
                                    <i class="ki-filled ki-down text-xs"></i>
                                </span>
                            </div>

                            {{-- Secondary menu items --}}
                            <div class="kt-menu-accordion overflow-hidden">
                                <div class="flex flex-col gap-1 pt-1 ps-7">
                                    @foreach ($menu['children'] as $child)
                                        @php
                                            $childActive = request()->routeIs($child['route'] . '*');
                                        @endphp

                                        <div class="kt-menu-item">
                                            <a href="{{ route($child['route']) }}"
                                                class="kt-menu-link flex items-center rounded-lg transition-all duration-200
                    {{ $childActive
                        ? 'bg-primary/10 text-primary font-semibold'
                        : 'text-muted-foreground hover:bg-accent/40 hover:text-foreground' }}
                    px-3 py-2 text-[13px]">

                                                {{-- Small bullet --}}
                                                <span
                                                    class="w-[5px] h-[5px] rounded-full me-3
                        {{ $childActive ? 'bg-primary' : 'bg-muted-foreground/40' }}">
                                                </span>

                                                <span class="kt-menu-title">
                                                    {{ $child['label'] }}
                                                </span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="kt-menu-item {{ $isActive ? 'kt-menu-item-active' : '' }}">
                            <a href="{{ route($menu['route']) }}"
                                class="kt-menu-link border border-transparent items-center grow
                                      {{ $isActive ? 'bg-accent/60 rounded-lg' : 'hover:bg-accent/60 hover:rounded-lg' }}
                                      gap-[14px] ps-[10px] pe-[10px] py-[8px]">
                                <span class="kt-menu-icon items-start text-muted-foreground w-[20px]">
                                    <i class="ki-filled {{ $menu['icon'] }} text-base"></i>
                                </span>
                                <span
                                    class="kt-menu-title text-sm font-medium text-foreground">{{ $menu['label'] }}</span>
                            </a>
                        </div>
                    @endif
                @endforeach

            </div>{{-- end kt-menu --}}
        </div>
    </div>

</div>
