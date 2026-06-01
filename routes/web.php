<?php

use App\Http\Controllers\AuctionsController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealsController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingCreateController;
use App\Http\Controllers\ListingDetailController;
use App\Http\Controllers\ChargesController;
use App\Http\Controllers\DisputesController;
use App\Http\Controllers\LogisticsJobsController;
use App\Http\Controllers\LogisticsQuotesController;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\PayoutsController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\QAController;
use App\Http\Controllers\ReconciliationController;
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



/*
    |------------------------------------------------------------------
    | Leads
    |------------------------------------------------------------------
    */
Route::prefix('leads')->name('leads.')->group(function () {

    // C1 — Browse / Pipeline (table + board)
    Route::get('/',            [LeadsController::class, 'index'])->name('index');

    // C2 — Create (quick add POST + full form GET/POST)
    Route::get('/create',      [LeadsController::class, 'create'])->name('create');
    Route::post('/',           [LeadsController::class, 'store'])->name('store');

    // C3 — Lead Detail
    Route::get('/{lead}',      [LeadsController::class, 'show'])->name('show');
    Route::get('/{lead}/edit', [LeadsController::class, 'edit'])->name('edit');
    Route::put('/{lead}',      [LeadsController::class, 'update'])->name('update');
    Route::delete('/{lead}',   [LeadsController::class, 'destroy'])->name('destroy');

    // Stage move (board drag / button)
    Route::patch('/{lead}/stage',  [LeadsController::class, 'moveStage'])->name('stage');

    // Owner assign
    Route::patch('/{lead}/assign', [LeadsController::class, 'assign'])->name('assign');

    // Convert to Listing / Customer
    Route::post('/{lead}/convert-listing',  [LeadsController::class, 'convertToListing'])->name('convert.listing');
    Route::post('/{lead}/convert-customer', [LeadsController::class, 'convertToCustomer'])->name('convert.customer');

    // Mark Do-Not-Contact
    Route::patch('/{lead}/dnc', [LeadsController::class, 'markDnc'])->name('dnc');

    // Merge duplicate
    Route::post('/{lead}/merge', [LeadsController::class, 'merge'])->name('merge');

    /*
        |--------------------------------------------------------------
        | Valuations (Phase 3 update)
        |--------------------------------------------------------------
        */
    Route::prefix('/{lead}/valuations')->name('valuations.')->group(function () {

        // Pull latest valuation from provider (single lead)
        Route::post('/pull',   [LeadsController::class, 'pullValuation'])->name('pull');

        // Add manual valuation
        Route::post('/',       [LeadsController::class, 'addValuation'])->name('store');

        // Apply valuation to linked Listing
        Route::post('/{valuation}/apply', [LeadsController::class, 'applyValuation'])->name('apply');
    });

    /*
        |--------------------------------------------------------------
        | Bulk actions (C1 — Leads index)
        |--------------------------------------------------------------
        */
    Route::prefix('/bulk')->name('bulk.')->group(function () {
        Route::post('/assign',          [LeadsController::class, 'bulkAssign'])->name('assign');
        Route::post('/stage',           [LeadsController::class, 'bulkStage'])->name('stage');
        Route::post('/message',         [LeadsController::class, 'bulkMessage'])->name('message');
        Route::post('/task',            [LeadsController::class, 'bulkTask'])->name('task');
        Route::post('/merge',           [LeadsController::class, 'bulkMerge'])->name('merge');
        // Phase 3: Pull valuations for selected leads with VRM/VIN
        Route::post('/pull-valuations', [LeadsController::class, 'bulkPullValuations'])->name('pull-valuations');
    });
});


/*
|--------------------------------------------------------------------------
| Listings — Phase 1
|--------------------------------------------------------------------------
*/

// L0 — Index (Browse/Search)
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');

// L1 — Create wizard
Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');

// L2 — Detail (AJAX partial for modal)
Route::get('/listings/{id}', [ListingController::class, 'show'])->name('listings.show');

// L8 — State transitions
Route::post('/listings/{id}/transition', [ListingController::class, 'transition'])->name('listings.transition');

// L7 — Bulk actions
Route::post('/listings/bulk', [ListingController::class, 'bulk'])->name('listings.bulk');

// L4 — Valuations module
Route::get('/valuations', [ValuationController::class, 'index'])->name('valuations.index');
Route::post('/listings/{listingId}/valuations', [ValuationController::class, 'store'])->name('valuations.store');
Route::post('/listings/{listingId}/valuations/pull', [ValuationController::class, 'pull'])->name('valuations.pull');
Route::post('/listings/{listingId}/valuations/apply', [ValuationController::class, 'apply'])->name('valuations.apply');

Route::prefix('auctions')->group(function () {
    Route::get('/', [AuctionsController::class, 'index'])
        ->name('auctions.index');
    Route::get('/{auction}', [AuctionsController::class, 'show'])
        ->name('auctions.show');
    Route::get('/{auction}/lots/{lot}', [AuctionsController::class, 'lotDetail'])
        ->name('auctions.lots.detail');
    Route::get('/auctions/live', [AuctionsController::class, 'live'])
        ->name('auctions.live');
    Route::get('/auctions/upcoming', [AuctionsController::class, 'upcoming'])
        ->name('auctions.upcoming');
    Route::get('/auctions/closed', [AuctionsController::class, 'closed'])
        ->name('auctions.closed');
    Route::get('/auctions/bids', [AuctionsController::class, 'bids'])
        ->name('auctions.bids');
});

Route::get('/editions',               fn() => view('stub', ['title' => 'All Editions']))->name('editions.index');
Route::get('/editions/create',        fn() => view('stub', ['title' => 'Create Edition']))->name('editions.create');
Route::get('/editions/schedules',     fn() => view('stub', ['title' => 'Edition Schedules']))->name('editions.schedules');

Route::get('/vendors',                [WalletsController::class, 'index'])->name('vendors.index');
Route::get('/customers',              fn() => view('stub', ['title' => 'Customer List']))->name('customers.index');
Route::get('/customers/segments',     fn() => view('stub', ['title' => 'Customer Segments']))->name('customers.segments');
Route::get('/customers/support',      fn() => view('stub', ['title' => 'Support Requests']))->name('customers.support');
Route::get('/customers/history',      fn() => view('stub', ['title' => 'Purchase History']))->name('customers.history');


// ── DEALS ─────────────────────────────────────────────────────────────────────
Route::prefix('deals')->name('deals.')->group(function () {
    Route::get('/',                    [DealsController::class, 'index'])->name('index');
    Route::get('/create',              [DealsController::class, 'create'])->name('create');
    Route::post('/',                   [DealsController::class, 'store'])->name('store');
    Route::get('/{deal}',              [DealsController::class, 'show'])->name('show');
    Route::get('/{deal}/edit',         [DealsController::class, 'edit'])->name('edit');
    Route::patch('/{deal}',            [DealsController::class, 'update'])->name('update');
    Route::delete('/{deal}',           [DealsController::class, 'destroy'])->name('destroy');
});

// ── PAYMENTS ──────────────────────────────────────────────────────────────────
Route::prefix('payments')->name('payments.')->group(function () {

    // Charges & Fees (P1)
    Route::get('/charges',             [ChargesController::class, 'index'])->name('charges');

    // Wallets (P2)
    Route::prefix('wallets')->name('wallets.')->group(function () {
        Route::get('/',                [WalletsController::class, 'index'])->name('index');
        Route::get('/{wallet}',        [WalletsController::class, 'show'])->name('show');
    });

    // Payment Methods / Cards on file (P3)
    Route::get('/methods',             [PaymentMethodsController::class, 'index'])->name('methods');

    // Payout Approvals (P4)
    Route::get('/payouts',             [PayoutsController::class, 'index'])->name('payouts');
    Route::post('/payouts/{payout}/approve', [PayoutsController::class, 'approve'])->name('payouts.approve');
    Route::post('/payouts/{payout}/reject',  [PayoutsController::class, 'reject'])->name('payouts.reject');

    // Reconciliation (P5)
    Route::get('/reconciliation',      [ReconciliationController::class, 'index'])->name('reconciliation');
    Route::post('/reconciliation/run', [ReconciliationController::class, 'run'])->name('reconciliation.run');
});

// ── LOGISTICS ─────────────────────────────────────────────────────────────────
Route::prefix('logistics')->name('logistics.')->group(function () {

    // Quotes (L1)
    Route::get('/quotes',              [LogisticsQuotesController::class, 'index'])->name('quotes');

    // Jobs (L2/L3/L4)
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/',                [LogisticsJobsController::class, 'index'])->name('index');
        Route::get('/create',          [LogisticsJobsController::class, 'create'])->name('create');
        Route::post('/',               [LogisticsJobsController::class, 'store'])->name('store');
        Route::get('/{job}',           [LogisticsJobsController::class, 'show'])->name('show');
        Route::patch('/{job}',         [LogisticsJobsController::class, 'update'])->name('update');
        Route::post('/{job}/transit',  [LogisticsJobsController::class, 'markInTransit'])->name('transit');
        Route::post('/{job}/deliver',  [LogisticsJobsController::class, 'markDelivered'])->name('deliver');
        Route::post('/{job}/handover', [LogisticsJobsController::class, 'confirmHandover'])->name('handover');
        Route::post('/{job}/chat',     [LogisticsJobsController::class, 'sendChatMessage'])->name('chat');
    });
});

// ── DISPUTES ──────────────────────────────────────────────────────────────────
Route::prefix('disputes')->name('disputes.')->group(function () {
    Route::get('/',                    [DisputesController::class, 'index'])->name('index');
    Route::get('/create',              [DisputesController::class, 'create'])->name('create');
    Route::post('/',                   [DisputesController::class, 'store'])->name('store');
    Route::get('/{dispute}',           [DisputesController::class, 'show'])->name('show');
    Route::patch('/{dispute}',         [DisputesController::class, 'update'])->name('update');
    Route::post('/{dispute}/ack',      [DisputesController::class, 'sendAck'])->name('ack');
    Route::post('/{dispute}/outcome',  [DisputesController::class, 'decideOutcome'])->name('outcome');
    Route::post('/{dispute}/charge',   [DisputesController::class, 'applyCharge'])->name('charge');
    Route::post('/{dispute}/close',    [DisputesController::class, 'close'])->name('close');
    Route::post('/{dispute}/escalate', [DisputesController::class, 'escalate'])->name('escalate');
});
Route::get('/disputes/escalations', [DisputesController::class, 'escalations'])->name('disputes.escalations');

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
