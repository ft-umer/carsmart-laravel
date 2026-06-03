<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    // S0 — Overview
    public function index(): View
    {
        $auditLog = collect([
            ['user' => 'Alice Morgan', 'action' => 'Updated branding colours',       'target' => 'settings.branding',        'time' => now()->subMinutes(15)],
            ['user' => 'Ben Carter',   'action' => 'Invited user emma@carsmart.co',  'target' => 'settings.rbac',            'time' => now()->subHours(1)],
            ['user' => 'System',       'action' => 'Scheduled report executed',       'target' => 'reports.valuation-coverage', 'time' => now()->subHours(2)],
            ['user' => 'Alice Morgan', 'action' => 'Updated automation policy',       'target' => 'settings.automations',    'time' => now()->subHours(4)],
            ['user' => 'Ben Carter',   'action' => 'Updated email provider config',   'target' => 'settings.providers',      'time' => now()->subHours(6)],
            ['user' => 'Clara James',  'action' => 'Disabled user john@example.com',  'target' => 'settings.rbac',            'time' => now()->subDays(1)],
            ['user' => 'System',       'action' => 'Purged 240 expired notifications', 'target' => 'notifications',            'time' => now()->subDays(1)],
            ['user' => 'Alice Morgan', 'action' => 'Updated KYC provider to Onfido',  'target' => 'settings.identity',       'time' => now()->subDays(2)],
            ['user' => 'Ben Carter',   'action' => 'Added new role: Compliance',       'target' => 'settings.rbac',            'time' => now()->subDays(2)],
            ['user' => 'System',       'action' => 'Bid increment bands updated',      'target' => 'settings.auctions',       'time' => now()->subDays(3)],
        ]);

        return view('settings.index', compact('auditLog'));
    }

    public function audit(): View
    {
        $log = collect([
            ['user' => 'Alice Morgan', 'action' => 'Updated branding colours',        'target' => 'settings.branding',         'time' => now()->subMinutes(15)],
            ['user' => 'Ben Carter',   'action' => 'Invited user emma@carsmart.co',   'target' => 'settings.rbac',             'time' => now()->subHours(1)],
            ['user' => 'System',       'action' => 'Scheduled report executed',        'target' => 'reports.valuation-coverage', 'time' => now()->subHours(2)],
            ['user' => 'Alice Morgan', 'action' => 'Updated automation policy',        'target' => 'settings.automations',     'time' => now()->subHours(4)],
            ['user' => 'Ben Carter',   'action' => 'Updated email provider config',    'target' => 'settings.providers',       'time' => now()->subHours(6)],
            ['user' => 'Clara James',  'action' => 'Disabled user john@example.com',   'target' => 'settings.rbac',            'time' => now()->subDays(1)],
            ['user' => 'System',       'action' => 'Purged 240 expired notifications', 'target' => 'notifications',            'time' => now()->subDays(1)],
            ['user' => 'Alice Morgan', 'action' => 'Updated KYC provider to Onfido',   'target' => 'settings.identity',        'time' => now()->subDays(2)],
            ['user' => 'Ben Carter',   'action' => 'Added new role: Compliance',        'target' => 'settings.rbac',            'time' => now()->subDays(2)],
            ['user' => 'System',       'action' => 'Bid increment bands updated',       'target' => 'settings.auctions',        'time' => now()->subDays(3)],
            ['user' => 'Emma Walsh',   'action' => 'Reset password for david@carsmart.co', 'target' => 'settings.rbac',        'time' => now()->subDays(4)],
            ['user' => 'Alice Morgan', 'action' => 'Updated privacy retention to 36m', 'target' => 'settings.privacy',         'time' => now()->subDays(5)],
        ]);

        return view('settings.audit', ['log' => $log]);
    }

    // S1 — Users & Roles (RBAC)
    public function rbac(): View
    {
        $users = collect([
            ['id' => 1, 'name' => 'Alice Morgan', 'email' => 'alice@carsmart.co',  'roles' => ['Admin'],        'status' => 'Active',   'last_active' => now()->subMinutes(5)],
            ['id' => 2, 'name' => 'Ben Carter',   'email' => 'ben@carsmart.co',    'roles' => ['Admin'],        'status' => 'Active',   'last_active' => now()->subHours(1)],
            ['id' => 3, 'name' => 'Clara James',  'email' => 'clara@carsmart.co',  'roles' => ['Operations'],   'status' => 'Active',   'last_active' => now()->subHours(3)],
            ['id' => 4, 'name' => 'David Singh',  'email' => 'david@carsmart.co',  'roles' => ['Sales'],        'status' => 'Active',   'last_active' => now()->subDays(1)],
            ['id' => 5, 'name' => 'Emma Walsh',   'email' => 'emma@carsmart.co',   'roles' => ['Sales'],        'status' => 'Active',   'last_active' => now()->subDays(1)],
            ['id' => 6, 'name' => 'Frank Olsen',  'email' => 'frank@carsmart.co',  'roles' => ['Compliance'],   'status' => 'Active',   'last_active' => now()->subDays(2)],
            ['id' => 7, 'name' => 'Grace Kim',    'email' => 'grace@carsmart.co',  'roles' => ['Viewer'],       'status' => 'Disabled', 'last_active' => now()->subDays(30)],
        ]);

        $roles = collect([
            ['id' => 1, 'name' => 'Admin',      'user_count' => 2, 'description' => 'Full platform access.',               'permissions' => ['listings.manage', 'auctions.manage', 'settings.manage', 'reports.view', 'users.manage']],
            ['id' => 2, 'name' => 'Operations', 'user_count' => 1, 'description' => 'Logistics and deal management.',       'permissions' => ['listings.view', 'listings.edit', 'logistics.manage', 'deals.manage', 'reports.view']],
            ['id' => 3, 'name' => 'Sales',      'user_count' => 2, 'description' => 'Lead and listing management.',         'permissions' => ['listings.view', 'listings.edit', 'leads.manage', 'tasks.manage']],
            ['id' => 4, 'name' => 'Compliance', 'user_count' => 1, 'description' => 'KYC/KYB and dispute oversight.',       'permissions' => ['kyc.manage', 'disputes.manage', 'reports.view', 'users.view']],
            ['id' => 5, 'name' => 'Viewer',     'user_count' => 1, 'description' => 'Read-only access to listings.',        'permissions' => ['listings.view']],
        ]);

        return view('settings.rbac', compact('users', 'roles'));
    }

    public function inviteUser(Request $request): RedirectResponse
    {
        return back()->with('success', 'Invitation sent to ' . $request->input('email', 'user'));
    }

    public function updateUser(Request $request, int $id): RedirectResponse
    {
        return back()->with('success', 'User updated.');
    }

    public function disableUser(int $id): RedirectResponse
    {
        return back()->with('success', 'User disabled.');
    }

    public function enableUser(int $id): RedirectResponse
    {
        return back()->with('success', 'User enabled.');
    }

    public function resetPassword(int $id): RedirectResponse
    {
        return back()->with('success', 'Password reset email sent.');
    }

    public function storeRole(Request $request): RedirectResponse
    {
        return back()->with('success', 'Role created.');
    }

    public function updateRole(Request $request, int $id): RedirectResponse
    {
        return back()->with('success', 'Role updated.');
    }

    public function destroyRole(int $id): RedirectResponse
    {
        return back()->with('success', 'Role deleted.');
    }

    // S2 — Providers
    public function providers(): View
    {
        $providers = [
            'email' => [
                'provider'   => 'sendgrid',
                'domain'     => 'mail.carsmart.co',
                'api_key'    => 'sg_test_placeholder',
                'from_name'  => 'Carsmart',
                'from_email' => 'noreply@carsmart.co',
            ],
            'sms' => [
                'provider'   => 'twilio',
                'sender_id'  => 'CARSMART',
                'api_key'    => 'twilio_sid_placeholder',
                'auth_token' => 'twilio_token_placeholder',
            ],
            'whatsapp' => [
                'business_id'  => '',
                'phone_id'     => '',
                'access_token' => '',
            ],
            'valuation' => [
                'primary'   => 'cap_hpi',
                'api_key'   => 'cap_test_key',
                'fallback'  => 'cazana',
            ],
            'logistics' => [
                'provider' => 'movecars',
                'api_key'  => 'mc_test_key',
            ],
        ];

        $providerStatus = [
            'email'     => true,
            'sms'       => true,
            'whatsapp'  => false,
            'valuation' => true,
            'logistics' => true,
        ];

        return view('settings.providers', compact('providers', 'providerStatus'));
    }

    public function updateProvider(Request $request, string $provider): RedirectResponse
    {
        return back()->with('success', ucfirst($provider) . ' configuration saved.');
    }

    public function testProvider(Request $request, string $provider): RedirectResponse
    {
        return back()->with('success', 'Test sent.');
    }

    // S3 — Identity
    public function identity(): View
    {
        $settings = [
            'kyc_provider'       => 'onfido',
            'kyc_api_key'        => 'onfido_test_placeholder',
            'kyc_required_docs'  => ['passport', 'driving_licence'],
            'kyb_required_docs'  => ['companies_house', 'bank_statement'],
            'state_labels'       => [
                'pending'    => 'Pending',
                'in_review'  => 'In Review',
                'approved'   => 'Approved',
                'rejected'   => 'Rejected',
            ],
            'override_compliance' => false,
            'require_reason'     => true,
            'require_attachment' => true,
        ];

        return view('settings.identity', compact('settings'));
    }

    public function updateIdentity(Request $request): RedirectResponse
    {
        return back()->with('success', 'Identity settings saved.');
    }

    // S4 — Auctions Reference
    public function auctions(): View
    {
        $bands = collect([
            ['id' => 1, 'from' => 0,      'to' => 5000,  'increment' => 100],
            ['id' => 2, 'from' => 5000,   'to' => 10000, 'increment' => 200],
            ['id' => 3, 'from' => 10000,  'to' => 20000, 'increment' => 500],
            ['id' => 4, 'from' => 20000,  'to' => 50000, 'increment' => 1000],
            ['id' => 5, 'from' => 50000,  'to' => 99999999, 'increment' => 2500],
        ]);

        $settings = [
            'sniper_minutes'         => 5,
            'auction_start_hour'     => 10,
            'auction_end_hour'       => 22,
            'default_reserve_buffer' => 5,
            'auto_extend_enabled'    => true,
        ];

        return view('settings.auctions', compact('bands', 'settings'));
    }

    public function updateSniper(Request $request): RedirectResponse
    {
        return back()->with('success', 'Sniper default updated.');
    }

    public function updateBands(Request $request): RedirectResponse
    {
        return back()->with('success', 'Bid increment bands saved.');
    }

    // S5 — Payments
    public function payments(): View
    {
        $settings = [
            'deposit_pct'              => 10,
            'balance_due_days'         => 14,
            'stripe_publishable_key'   => 'pk_test_placeholder',
            'stripe_secret_key'        => 'sk_test_placeholder',
            'stripe_webhook_secret'    => 'whsec_placeholder',
            'payout_auto_approve_below' => 5000,
            'payout_approval_roles'    => ['Admin', 'Finance'],
            'currency'                 => 'GBP',
            'vat_number'               => 'GB123456789',
            'psp_webhook_secret'       => 'psp_test_placeholder',
            'psp_secret_key'            => 'psp_sk_test_placeholder',
            'invoice_prefix'           => 'CSM-',
        ];

        return view('settings.payments', compact('settings'));
    }

    public function updatePayments(Request $request): RedirectResponse
    {
        return back()->with('success', 'Payment settings saved.');
    }

    // S6 — Automations Policy
    public function automationsPolicy(): View
    {
        $policy = [
            'quiet_hours_enabled'        => true,
            'quiet_hours_start'          => '22:00',
            'quiet_hours_end'            => '07:00',
            'quiet_channels'             => ['sms', 'whatsapp'],
            'max_per_recipient_day'      => 5,
            'max_platform_sends_hour'    => 500,
            'approval_required'          => ['Email', 'WhatsApp'],
            'valuation_fetch_rate_limit' => 100,
            'valuation_quiet_hours'      => true,
        ];

        return view('settings.automations', compact('policy'));
    }

    public function updateAutomationsPolicy(Request $request): RedirectResponse
    {
        return back()->with('success', 'Automations policy saved.');
    }

    // S7 — Consent & Privacy
    public function privacy(): View
    {
        $settings = [
            'retention_months'         => 36,
            'include_archived_default' => false,
            'masked_fields'            => ['email', 'phone', 'address'],
            'rtbf_fields'              => ['email', 'phone', 'address', 'name'],
        ];

        return view('settings.privacy', compact('settings'));
    }

    public function updatePrivacy(Request $request): RedirectResponse
    {
        return back()->with('success', 'Privacy settings saved.');
    }

    public function rtbfTest(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'identifier'   => $request->input('identifier'),
            'records_found' => 14,
            'tables'       => ['users', 'leads', 'notifications', 'audit_logs'],
            'preview'      => 'Dry run only — no data removed.',
        ]);
    }

    // S8 — Branding
    public function branding(): View
    {
        $branding = [
            'logo_url'        => null,
            'logo_dark_url'   => null,
            'primary'         => '#2563eb',
            'primary_fg'      => '#ffffff',
            'success'         => '#16a34a',
            'warning'         => '#d97706',
            'destructive'     => '#dc2626',
            'info'            => '#0284c7',
            'font_body'       => 'Inter',
            'font_mono'       => 'JetBrains Mono',
            'font_size_base'  => 14,
            'line_height'     => 1.5,
            'btn_radius'      => 8,
            'card_radius'     => 12,
            'shadow_style'    => 'sm',
            'platform_name'   => 'Carsmart',
            'support_email'   => 'support@carsmart.co',
        ];

        return view('settings.branding', compact('branding'));
    }

    public function updateBranding(Request $request): RedirectResponse
    {
        return back()->with('success', 'Branding saved.');
    }

    // S9 — Environment
    public function environment(): View
    {
        $envKeys = [
            'STRIPE_KEY'           => '',
            'CAP_HPI_KEY'          => '',
            'CAZANA_KEY'           => '',
            'TWILIO_SID'           => '',
            'SENDGRID_API_KEY'     => '',
            'WHATSAPP_ACCESS_TOKEN' => '',
        ];

        $envFlags = [
            'FEATURE_EDITIONS'       => true,
            'FEATURE_VALUATIONS'     => true,
            'FEATURE_LOGISTICS'      => true,
            'FEATURE_AUTOMATIONS'    => true,
            'FEATURE_REPORTS'        => true,
            'DEBUG_MODE'             => false,
            'MAINTENANCE_MODE'       => false,
        ];

        return view('settings.environment', compact('envKeys', 'envFlags'));
    }

    public function updateEnvKeys(Request $request): RedirectResponse
    {
        return back()->with('success', 'Sandbox keys saved.');
    }

    public function updateFlags(Request $request): RedirectResponse
    {
        return back()->with('success', 'Toggles saved.');
    }

    public function seedData(): RedirectResponse
    {
        return back()->with('success', 'Demo data seeded.');
    }

    public function resetData(): RedirectResponse
    {
        return back()->with('success', 'Demo data reset.');
    }

    public function flushJobs(): RedirectResponse
    {
        return back()->with('success', 'Queue flushed.');
    }

    public function purgeAll(Request $request): RedirectResponse
    {
        return back()->with('success', 'All data purged.');
    }
}
