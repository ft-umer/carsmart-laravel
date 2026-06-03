<?php

use App\Http\Controllers\AuctionsController;
use App\Http\Controllers\AutomationsController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealsController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingCreateController;
use App\Http\Controllers\ListingDetailController;
use App\Http\Controllers\ChargesController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\DisputesController;
use App\Http\Controllers\LogisticsJobsController;
use App\Http\Controllers\LogisticsQuotesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\PayoutsController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\QAController;
use App\Http\Controllers\ReconciliationController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TasksController;
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


// L7 — Bulk actions
Route::post('/listings/bulk', [ListingController::class, 'bulk'])->name('listings.bulk');

// L2 — Detail (AJAX partial for modal)
Route::get('/listings/{id}', [ListingController::class, 'show'])->name('listings.show');

// L8 — State transitions
Route::post('/listings/{id}/transition', [ListingController::class, 'transition'])->name('listings.transition');


// L4 — Valuations module
Route::get('/valuations', [ValuationController::class, 'index'])->name('valuations.index');
Route::post('/listings/{listingId}/valuations', [ValuationController::class, 'store'])->name('valuations.store');
Route::post('/listings/{listingId}/valuations/pull', [ValuationController::class, 'pull'])->name('valuations.pull');
Route::post('/listings/{listingId}/valuations/apply', [ValuationController::class, 'apply'])->name('valuations.apply');

Route::prefix('auctions')->group(function () {

    Route::get('/', [AuctionsController::class, 'index'])
        ->name('auctions.index');

    Route::get('/live', [AuctionsController::class, 'live'])
        ->name('auctions.live');

    Route::get('/upcoming', [AuctionsController::class, 'upcoming'])
        ->name('auctions.upcoming');

    Route::get('/closed', [AuctionsController::class, 'closed'])
        ->name('auctions.closed');

    Route::get('/bids', [AuctionsController::class, 'bids'])
        ->name('auctions.bids');

    Route::get('/{auction}', [AuctionsController::class, 'show'])
        ->name('auctions.show');

    Route::get('/{auction}/lots/{lot}', [AuctionsController::class, 'lotDetail'])
        ->name('auctions.lots.detail');
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


// ──────────────────────────────────────────────────────────────────────────
// CMS
// ──────────────────────────────────────────────────────────────────────────
Route::prefix('cms')->name('cms.')->group(function () {

    // Library / overview
    Route::get('/',           [CmsController::class, 'index'])->name('index');

    // Pages
    Route::get('/pages/create', [CmsController::class, 'createPage'])->name('pages.create');
    Route::post('/pages',       [CmsController::class, 'storePage'])->name('pages.store');

    // Posts
    Route::get('/posts/create', [CmsController::class, 'createPost'])->name('posts.create');
    Route::post('/posts',       [CmsController::class, 'storePost'])->name('posts.store');

    // Editor (shared for pages & posts)
    Route::get('/{cms}/edit',   [CmsController::class, 'edit'])->name('edit');
    Route::patch('/{cms}',      [CmsController::class, 'update'])->name('update');
    Route::delete('/{cms}',     [CmsController::class, 'destroy'])->name('destroy');

    // Publish / schedule / archive actions
    Route::patch('/{cms}/publish',  [CmsController::class, 'publish'])->name('publish');
    Route::patch('/{cms}/schedule', [CmsController::class, 'schedule'])->name('schedule');
    Route::patch('/{cms}/archive',  [CmsController::class, 'archive'])->name('archive');

    // Preview
    Route::get('/{cms}/preview', [CmsController::class, 'preview'])->name('preview');

    // Version history
    Route::get('/{cms}/versions',             [CmsController::class, 'versions'])->name('versions');
    Route::post('/{cms}/versions/{version}/rollback', [CmsController::class, 'rollback'])->name('rollback');

    // Banners & Features
    Route::get('/banners',            [CmsController::class, 'banners'])->name('banners');
    Route::post('/banners',           [CmsController::class, 'storeFeature'])->name('banners.store');
    Route::patch('/banners/{feature}', [CmsController::class, 'updateFeature'])->name('banners.update');
    Route::delete('/banners/{feature}', [CmsController::class, 'destroyFeature'])->name('banners.destroy');

    // Media Library
    Route::get('/media',          [CmsController::class, 'media'])->name('media');
    Route::post('/media/upload',  [CmsController::class, 'upload'])->name('media.upload');
    Route::delete('/media/{media}', [CmsController::class, 'destroyMedia'])->name('media.destroy');
});

// ──────────────────────────────────────────────────────────────────────────
// Automations
// ──────────────────────────────────────────────────────────────────────────
Route::prefix('automations')->name('automations.')->group(function () {

    // Journeys
    Route::get('/',              [AutomationsController::class, 'index'])->name('index');
    Route::get('/create',        [AutomationsController::class, 'create'])->name('create');
    Route::post('/',             [AutomationsController::class, 'store'])->name('store');
    Route::get('/{journey}/edit', [AutomationsController::class, 'edit'])->name('edit');
    Route::patch('/{journey}',   [AutomationsController::class, 'update'])->name('update');
    Route::delete('/{journey}',  [AutomationsController::class, 'destroy'])->name('destroy');

    // Journey actions
    Route::patch('/{journey}/pause',     [AutomationsController::class, 'pause'])->name('pause');
    Route::patch('/{journey}/resume',    [AutomationsController::class, 'resume'])->name('resume');
    Route::post('/{journey}/duplicate',  [AutomationsController::class, 'duplicate'])->name('duplicate');
    Route::patch('/{journey}/publish',   [AutomationsController::class, 'publish'])->name('publish');

    // Triggers registry
    Route::get('/triggers',            [AutomationsController::class, 'triggers'])->name('triggers');
    Route::get('/triggers/{trigger}',  [AutomationsController::class, 'triggerSchema'])->name('triggers.schema');
    Route::post('/triggers/{trigger}/test', [AutomationsController::class, 'testFire'])->name('triggers.test');

    // Templates
    Route::get('/templates',             [AutomationsController::class, 'templates'])->name('templates');
    Route::post('/templates',            [AutomationsController::class, 'storeTemplate'])->name('templates.store');
    Route::patch('/templates/{template}', [AutomationsController::class, 'updateTemplate'])->name('templates.update');
    Route::delete('/templates/{template}', [AutomationsController::class, 'destroyTemplate'])->name('templates.destroy');

    // Runs & monitoring
    Route::get('/runs',         [AutomationsController::class, 'runs'])->name('runs');
    Route::get('/runs/{run}',   [AutomationsController::class, 'runLog'])->name('runs.log');
    Route::post('/runs/{run}/retry', [AutomationsController::class, 'retry'])->name('runs.retry');

    // Suppressions
    Route::get('/suppressions',          [AutomationsController::class, 'suppressions'])->name('suppressions');
    Route::post('/suppressions',         [AutomationsController::class, 'storeSuppression'])->name('suppressions.store');
    Route::delete('/suppressions/{sup}', [AutomationsController::class, 'destroySuppression'])->name('suppressions.destroy');
});

// ──────────────────────────────────────────────────────────────────────────
// Reports & Analytics
// ──────────────────────────────────────────────────────────────────────────
Route::prefix('reports')->name('reports.')->group(function () {

    Route::get('/',         [ReportsController::class, 'index'])->name('index');
    Route::get('/custom',   [ReportsController::class, 'custom'])->name('custom');
    Route::post('/custom/run', [ReportsController::class, 'run'])->name('run');
    Route::post('/custom/save', [ReportsController::class, 'save'])->name('save');

    // Individual report pages (slug-based)
    Route::get('/{report}', [ReportsController::class, 'show'])->name('show');

    // Schedule a report email
    Route::post('/schedule', [ReportsController::class, 'scheduleEmail'])->name('schedule');

    // Export
    Route::get('/{report}/export', [ReportsController::class, 'export'])->name('export');
});

// ──────────────────────────────────────────────────────────────────────────
// Settings & Governance
// ──────────────────────────────────────────────────────────────────────────
Route::prefix('settings')->name('settings.')->group(function () {

    Route::get('/', [SettingsController::class, 'index'])->name('index');

    // Audit log
    Route::get('/audit', [SettingsController::class, 'audit'])->name('audit');

    // S1 — Users & Roles (RBAC)
    Route::get('/rbac',          [SettingsController::class, 'rbac'])->name('rbac');
    Route::post('/rbac/invite',  [SettingsController::class, 'inviteUser'])->name('rbac.invite');
    Route::patch('/rbac/{user}', [SettingsController::class, 'updateUser'])->name('rbac.update');
    Route::patch('/rbac/{user}/disable', [SettingsController::class, 'disableUser'])->name('rbac.disable');
    Route::patch('/rbac/{user}/enable',  [SettingsController::class, 'enableUser'])->name('rbac.enable');
    Route::post('/rbac/{user}/reset-password', [SettingsController::class, 'resetPassword'])->name('rbac.reset');
    Route::post('/rbac/roles',   [SettingsController::class, 'storeRole'])->name('rbac.roles.store');
    Route::patch('/rbac/roles/{role}', [SettingsController::class, 'updateRole'])->name('rbac.roles.update');
    Route::delete('/rbac/roles/{role}', [SettingsController::class, 'destroyRole'])->name('rbac.roles.destroy');

    // S2 — Providers & Channels
    Route::get('/providers',              [SettingsController::class, 'providers'])->name('providers');
    Route::patch('/providers/{provider}', [SettingsController::class, 'updateProvider'])->name('providers.update');
    Route::post('/providers/{provider}/test', [SettingsController::class, 'testProvider'])->name('providers.test');

    // S3 — Identity & Compliance
    Route::get('/identity',    [SettingsController::class, 'identity'])->name('identity');
    Route::patch('/identity',  [SettingsController::class, 'updateIdentity'])->name('identity.update');

    // S4 — Auctions Reference
    Route::get('/auctions',           [SettingsController::class, 'auctions'])->name('auctions');
    Route::patch('/auctions/sniper',  [SettingsController::class, 'updateSniper'])->name('auctions.sniper');
    Route::post('/auctions/bands',    [SettingsController::class, 'updateBands'])->name('auctions.bands');

    // S5 — Payments
    Route::get('/payments',   [SettingsController::class, 'payments'])->name('payments');
    Route::patch('/payments', [SettingsController::class, 'updatePayments'])->name('payments.update');

    // S6 — Automations Policy
    Route::get('/automations',   [SettingsController::class, 'automationsPolicy'])->name('automations');
    Route::patch('/automations', [SettingsController::class, 'updateAutomationsPolicy'])->name('automations.update');

    // S7 — Consent & Privacy
    Route::get('/privacy',   [SettingsController::class, 'privacy'])->name('privacy');
    Route::patch('/privacy', [SettingsController::class, 'updatePrivacy'])->name('privacy.update');
    Route::post('/privacy/rtbf-test', [SettingsController::class, 'rtbfTest'])->name('privacy.rtbf-test');

    // S8 — Branding
    Route::get('/branding',   [SettingsController::class, 'branding'])->name('branding');
    Route::patch('/branding', [SettingsController::class, 'updateBranding'])->name('branding.update');

    // S9 — Environment
    Route::get('/environment',                [SettingsController::class, 'environment'])->name('environment');
    Route::patch('/environment/keys',         [SettingsController::class, 'updateEnvKeys'])->name('environment.keys');
    Route::patch('/environment/flags',        [SettingsController::class, 'updateFlags'])->name('environment.flags');
    Route::post('/environment/seed',          [SettingsController::class, 'seedData'])->name('environment.seed');
    Route::post('/environment/reset',         [SettingsController::class, 'resetData'])->name('environment.reset');
    Route::post('/environment/flush-jobs',    [SettingsController::class, 'flushJobs'])->name('environment.flush');
    Route::post('/environment/purge',         [SettingsController::class, 'purgeAll'])->name('environment.purge');
});

// ──────────────────────────────────────────────────────────────────────────
// Notifications
// ──────────────────────────────────────────────────────────────────────────
Route::prefix('notifications')->name('notifications.')->group(function () {

    Route::get('/',                    [NotificationsController::class, 'index'])->name('index');
    Route::patch('/{id}/read',         [NotificationsController::class, 'markRead'])->name('read');
    Route::post('/mark-all-read',      [NotificationsController::class, 'markAllRead'])->name('mark-all-read');
    Route::patch('/{id}/mute',         [NotificationsController::class, 'mute'])->name('mute');
    Route::delete('/{id}',             [NotificationsController::class, 'destroy'])->name('destroy');

    // Preferences
    Route::get('/preferences',         [NotificationsController::class, 'preferences'])->name('preferences');
    Route::patch('/preferences',       [NotificationsController::class, 'updatePreferences'])->name('preferences.update');
});

// ──────────────────────────────────────────────────────────────────────────
// Global Tasks
// ──────────────────────────────────────────────────────────────────────────
Route::prefix('tasks')->name('tasks.')->group(function () {

    Route::get('/',             [TasksController::class, 'index'])->name('index');
    Route::post('/',            [TasksController::class, 'store'])->name('store');
    Route::get('/{task}',       [TasksController::class, 'show'])->name('show');
    Route::patch('/{task}',     [TasksController::class, 'update'])->name('update');
    Route::delete('/{task}',    [TasksController::class, 'destroy'])->name('destroy');

    // Actions
    Route::patch('/{task}/complete', [TasksController::class, 'complete'])->name('complete');
    Route::patch('/{task}/snooze',   [TasksController::class, 'snooze'])->name('snooze');
    Route::patch('/{task}/assign',   [TasksController::class, 'assign'])->name('assign');

    // Bulk
    Route::post('/bulk/complete', [TasksController::class, 'bulkComplete'])->name('bulk.complete');
    Route::post('/bulk/assign',   [TasksController::class, 'bulkAssign'])->name('bulk.assign');
    Route::post('/bulk/delete',   [TasksController::class, 'bulkDelete'])->name('bulk.delete');
});

// ── A6. Static auth error pages (no auth required) ──────────────────────
Route::get('/account-locked',   fn() => view('auth.locked'))->name('account.locked');
Route::get('/session-expired',  fn() => view('auth.session-expired'))->name('session.expired');
