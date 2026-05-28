<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingCreateController;
use App\Http\Controllers\ListingDetailController;
use App\Http\Controllers\QAController;
use App\Http\Controllers\ValuationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Phase 0 — Authentication & Entry + Global Shell + Home Screens
|--------------------------------------------------------------------------
*/

// ── D. Route guards ──────────────────────────────────────────────────────
// All authenticated routes are wrapped in the 'auth' middleware.
// The dashboard route determines which home screen to show based on app_target.

Route::get('/sign-in', fn() => view('auth.sign-in'))->name('login');
Route::post('/sign-in', fn() => redirect()->route('dashboard'))->name('login.post');

Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::post('/forgot-password', fn() => back())->name('password.email');

Route::get('/reset-password/{token}', fn($token) => view('auth.reset-password', ['token' => $token]))
    ->name('password.reset');

Route::post('/reset-password', fn() => redirect()->route('login'))->name('password.update');


// A4. Two-factor (optional)
Route::get('/two-factor', fn() => view('auth.two-factor'))->name('two-factor');
Route::post('/two-factor/verify', fn() => back())->name('two-factor.verify');
Route::post('/two-factor/resend', fn() => back())->name('two-factor.resend');
Route::get('/two-factor/backup', fn() => back())->name('two-factor.backup');

// A5. Terms consent
Route::get('/terms-consent', fn() => view('auth.terms-consent'))->name('terms.consent');
Route::post('/terms-consent', fn() => redirect()->route('dashboard'))->name('terms.accept');

// Sign out
Route::post('/sign-out', fn() => redirect()->route('login'))->name('logout');
// ── B1. App switcher ────────────────────────────────────────────────────
Route::get('/switch-app/{app}', function ($app) {
    session(['app_target' => $app]);
    return redirect()->route($app === 'crm' ? 'crm.inbox' : 'dashboard');
})->name('switch-app');

// ── C1. Admin home ──────────────────────────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard.overview');
Route::get('/dashboard/analytics', fn() => view('dashboard'))->name('dashboard.analytics');
Route::get('/dashboard/activity', fn() => view('dashboard'))->name('dashboard.activity');

// ── C2. CRM home ────────────────────────────────────────────────────────
Route::get('/crm/inbox', fn() => view('crm.inbox'))->name('crm.inbox');

// ── Stub routes for navigation (Phase 1+ will implement these) ──────────
Route::get('/leads',                  fn() => view('stub', ['title' => 'All Leads']))->name('leads.index');
Route::get('/leads/new',              fn() => view('stub', ['title' => 'New Leads']))->name('leads.new');
Route::get('/leads/qualified',        fn() => view('stub', ['title' => 'Qualified Leads']))->name('leads.qualified');
Route::get('/leads/archived',         fn() => view('stub', ['title' => 'Archived Leads']))->name('leads.archived');

/*
|--------------------------------------------------------------------------
| Listings — Phase 1
|--------------------------------------------------------------------------
*/
// Listings routes
Route::prefix('listings')->group(function () {
    Route::get('/', [ListingController::class, 'index'])->name('listings.index');
    Route::get('/create', [ListingCreateController::class, 'create'])->name('listings.create');
    Route::post('/store', [ListingCreateController::class, 'store'])->name('listings.store');
    Route::get('/{id}', [ListingDetailController::class, 'show'])->name('listings.show');
});

Route::prefix('valuations')->group(function () {
    Route::get('/', [ValuationController::class, 'index'])->name('valuations.index');
    Route::post('/pull/{id}', [ValuationController::class, 'pull'])->name('valuations.pull');
    Route::post('/add/{id}', [ValuationController::class, 'add'])->name('valuations.add');
    Route::post('/apply/{id}', [ValuationController::class, 'apply'])->name('valuations.apply');
});

Route::get('/qa', [QAController::class, 'index'])->name('qa.index');

Route::get('/auctions',               fn() => view('stub', ['title' => 'Auctions']))->name('auctions.index');
Route::get('/auctions/live',          fn() => view('stub', ['title' => 'Live Auctions']))->name('auctions.live');
Route::get('/auctions/upcoming',      fn() => view('stub', ['title' => 'Upcoming Auctions']))->name('auctions.upcoming');
Route::get('/auctions/closed',        fn() => view('stub', ['title' => 'Closed Auctions']))->name('auctions.closed');
Route::get('/auctions/bids',          fn() => view('stub', ['title' => 'Bids']))->name('auctions.bids');

Route::get('/editions',               fn() => view('stub', ['title' => 'All Editions']))->name('editions.index');
Route::get('/editions/create',        fn() => view('stub', ['title' => 'Create Edition']))->name('editions.create');
Route::get('/editions/schedules',     fn() => view('stub', ['title' => 'Edition Schedules']))->name('editions.schedules');

Route::get('/vendors',                fn() => view('stub', ['title' => 'Vendor Directory']))->name('vendors.index');
Route::get('/vendors/applications',   fn() => view('stub', ['title' => 'Vendor Applications']))->name('vendors.applications');
Route::get('/vendors/performance',    fn() => view('stub', ['title' => 'Vendor Performance']))->name('vendors.performance');

Route::get('/customers',              fn() => view('stub', ['title' => 'Customer List']))->name('customers.index');
Route::get('/customers/segments',     fn() => view('stub', ['title' => 'Customer Segments']))->name('customers.segments');
Route::get('/customers/support',      fn() => view('stub', ['title' => 'Support Requests']))->name('customers.support');
Route::get('/customers/history',      fn() => view('stub', ['title' => 'Purchase History']))->name('customers.history');

Route::get('/payments',               fn() => view('stub', ['title' => 'Payments']))->name('payments.index');
Route::get('/payments/transactions',  fn() => view('stub', ['title' => 'Transactions']))->name('payments.transactions');
Route::get('/payments/invoices',      fn() => view('stub', ['title' => 'Invoices']))->name('payments.invoices');
Route::get('/payments/refunds',       fn() => view('stub', ['title' => 'Refunds']))->name('payments.refunds');
Route::get('/payments/payouts',       fn() => view('stub', ['title' => 'Payout Requests']))->name('payments.payouts');

Route::get('/logistics',              fn() => view('stub', ['title' => 'Logistics']))->name('logistics.index');
Route::get('/logistics/shipments',    fn() => view('stub', ['title' => 'Shipments']))->name('logistics.shipments');
Route::get('/logistics/tracking',     fn() => view('stub', ['title' => 'Tracking']))->name('logistics.tracking');
Route::get('/logistics/warehouses',   fn() => view('stub', ['title' => 'Warehouses']))->name('logistics.warehouses');
Route::get('/logistics/status',       fn() => view('stub', ['title' => 'Delivery Status']))->name('logistics.status');

Route::get('/disputes',               fn() => view('stub', ['title' => 'Disputes']))->name('disputes.index');
Route::get('/disputes/open',          fn() => view('stub', ['title' => 'Open Cases']))->name('disputes.open');
Route::get('/disputes/resolved',      fn() => view('stub', ['title' => 'Resolved Cases']))->name('disputes.resolved');
Route::get('/disputes/escalations',   fn() => view('stub', ['title' => 'Escalations']))->name('disputes.escalations');

Route::get('/content',                fn() => view('stub', ['title' => 'Content Management']))->name('cms.index');
Route::get('/content/pages',          fn() => view('stub', ['title' => 'Pages']))->name('cms.pages');
Route::get('/content/blogs',          fn() => view('stub', ['title' => 'Blogs']))->name('cms.blogs');
Route::get('/content/media',          fn() => view('stub', ['title' => 'Media Library']))->name('cms.media');
Route::get('/content/seo',            fn() => view('stub', ['title' => 'SEO']))->name('cms.seo');

Route::get('/automations',            fn() => view('stub', ['title' => 'Automations']))->name('automations.index');
Route::get('/automations/workflows',  fn() => view('stub', ['title' => 'Workflows']))->name('automations.workflows');
Route::get('/automations/email',      fn() => view('stub', ['title' => 'Email Automation']))->name('automations.email');
Route::get('/automations/triggers',   fn() => view('stub', ['title' => 'Triggers']))->name('automations.triggers');
Route::get('/automations/scheduled',  fn() => view('stub', ['title' => 'Scheduled Tasks']))->name('automations.scheduled');

Route::get('/reports',                fn() => view('stub', ['title' => 'Reports']))->name('reports.index');
Route::get('/reports/sales',          fn() => view('stub', ['title' => 'Sales Reports']))->name('reports.sales');
Route::get('/reports/users',          fn() => view('stub', ['title' => 'User Reports']))->name('reports.users');
Route::get('/reports/auctions',       fn() => view('stub', ['title' => 'Auction Reports']))->name('reports.auctions');
Route::get('/reports/exports',        fn() => view('stub', ['title' => 'Exports']))->name('reports.exports');

Route::get('/settings',               fn() => view('stub', ['title' => 'Settings']))->name('settings.index');
Route::get('/settings/general',       fn() => view('stub', ['title' => 'General Settings']))->name('settings.general');
Route::get('/settings/users',         fn() => view('stub', ['title' => 'Users and Roles']))->name('settings.users');
Route::get('/settings/integrations',  fn() => view('stub', ['title' => 'Integrations']))->name('settings.integrations');
Route::get('/settings/security',      fn() => view('stub', ['title' => 'Security Settings']))->name('settings.security');

Route::get('/notifications',          fn() => view('stub', ['title' => 'Notifications']))->name('notifications.index');
Route::get('/notifications/inbox',    fn() => view('stub', ['title' => 'Notification Inbox']))->name('notifications.inbox');
Route::get('/notifications/templates', fn() => view('stub', ['title' => 'Notification Templates']))->name('notifications.templates');
Route::get('/notifications/preferences', fn() => view('stub', ['title' => 'Notification Preferences']))->name('notifications.preferences');

Route::get('/tasks',                  fn() => view('stub', ['title' => 'Tasks']))->name('tasks.index');
Route::get('/tasks/mine',             fn() => view('stub', ['title' => 'My Tasks']))->name('tasks.mine');
Route::get('/tasks/team',             fn() => view('stub', ['title' => 'Team Tasks']))->name('tasks.team');
Route::get('/tasks/completed',        fn() => view('stub', ['title' => 'Completed Tasks']))->name('tasks.completed');


// ── A6. Static auth error pages (no auth required) ──────────────────────
Route::get('/account-locked',   fn() => view('auth.locked'))->name('account.locked');
Route::get('/session-expired',  fn() => view('auth.session-expired'))->name('session.expired');
