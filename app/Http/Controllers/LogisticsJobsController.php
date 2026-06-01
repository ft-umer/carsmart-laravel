<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class LogisticsJobsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | L2: Jobs index (list + calendar)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $search     = $request->input('search', '');
        $jobStatus  = $request->input('status', '');
        $provider   = $request->input('provider', '');
        $owner      = $request->input('owner', '');
        $view       = $request->input('view', 'list');
        $weekOffset = (int) $request->input('week_offset', 0);

        // --- Replace with real Eloquent query ---
        $jobs = collect($this->mockJobs());

        if ($search) {
            $jobs = $jobs->filter(fn($j) =>
                str_contains(strtolower($j['ref'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($j['vehicle_title'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($j['vrm'] ?? ''), strtolower($search)) ||
                str_contains(strtolower($j['deal_ref'] ?? ''), strtolower($search))
            );
        }
        if ($jobStatus) {
            $jobs = $jobs->where('status', $jobStatus);
        }
        if ($provider) {
            $jobs = $jobs->where('provider', $provider);
        }
        if ($owner) {
            $jobs = $jobs->where('owner', $owner);
        }

        \Illuminate\Support\Facades\Log::info('logistics_jobs_viewed', [
            'user' => $request->user()?->id,
            'view' => $view,
        ]);

        return view('logistics.jobs.index', [
            'jobs'      => $jobs->values(),
            'total'     => $jobs->count(),
            'view'      => $view,
            'search'    => $search,
            'jobStatus' => $jobStatus,
            'provider'  => $provider,
            'owner'     => $owner,
            'providers' => $this->providerList(),
            'owners'    => ['AM', 'JB', 'SK', 'CL'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | L2: Job detail (includes L3 checklist + L4 transport chat)
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, string $id): View
    {
        $job = $this->findJob($id);
        abort_if(!$job, 404, 'Job not found.');

        \Illuminate\Support\Facades\Log::info('logistics_job_viewed', [
            'user'   => $request->user()?->id,
            'job_id' => $id,
        ]);

        return view('logistics.jobs.show', [
            'job'       => $job,
            'owners'    => ['AM', 'JB', 'SK', 'CL'],
            'providers' => $this->providerList(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | L2: Create job form
    |--------------------------------------------------------------------------
    */
    public function create(Request $request): View
    {
        return view('logistics.jobs.create', [
            'providers' => $this->providerList(),
            'owners'    => ['AM', 'JB', 'SK', 'CL'],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | L2: Store job
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'deal_ref'        => 'nullable|string',
            'deal_id'         => 'nullable|string',
            'vrm'             => 'nullable|string|max:10',
            'vehicle_title'   => 'nullable|string|max:255',
            'pickup_address'  => 'required|string',
            'drop_address'    => 'required|string',
            'pickup_contact'  => 'nullable|string|max:255',
            'drop_contact'    => 'nullable|string|max:255',
            'slot_date'       => 'required|date|after_or_equal:today',
            'slot_window'     => 'required|in:AM,PM,Any',
            'provider'        => 'nullable|string',
            'tracking_ref'    => 'nullable|string|max:100',
            'owner'           => 'nullable|string',
            'notes'           => 'nullable|string|max:1000',
        ]);

        // Build slot string
        $validated['slot'] = $validated['slot_date'] . ' ' . $validated['slot_window'];

        // --- Replace with real model creation ---
        $ref = 'JOB-' . strtoupper(substr(uniqid(), -5));

        \Illuminate\Support\Facades\Log::info('job_created', [
            'user'   => $request->user()?->id,
            'ref'    => $ref,
            'data'   => $validated,
        ]);

        return redirect()->route('logistics.jobs.index')
                         ->with('success', "Transport job {$ref} created.");
    }

    /*
    |--------------------------------------------------------------------------
    | L2: Update job
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'owner'        => 'nullable|string',
            'provider'     => 'nullable|string',
            'tracking_ref' => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
        ]);

        // --- Replace with real update ---

        \Illuminate\Support\Facades\Log::info('job_updated', [
            'user'   => $request->user()?->id,
            'job_id' => $id,
            'data'   => $validated,
        ]);

        return back()->with('success', 'Job updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | L2: Mark in transit
    |--------------------------------------------------------------------------
    */
    public function markInTransit(Request $request, string $id): RedirectResponse
    {
        // --- Replace with real status update ---

        \Illuminate\Support\Facades\Log::info('job_in_transit', [
            'user'   => $request->user()?->id,
            'job_id' => $id,
        ]);

        return back()->with('success', 'Job marked as in transit.');
    }

    /*
    |--------------------------------------------------------------------------
    | L2: Mark delivered
    |--------------------------------------------------------------------------
    */
    public function markDelivered(Request $request, string $id): RedirectResponse
    {
        // --- Replace with real status update ---

        \Illuminate\Support\Facades\Log::info('job_delivered', [
            'user'   => $request->user()?->id,
            'job_id' => $id,
        ]);

        return back()->with('success', 'Job marked as delivered. Complete the handover checklist.');
    }

    /*
    |--------------------------------------------------------------------------
    | L3: Confirm handover (checklist complete)
    |--------------------------------------------------------------------------
    */
    public function confirmHandover(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'chk_buyer_id'   => 'boolean',
            'chk_seller_id'  => 'boolean',
            'chk_v5c'        => 'boolean',
            'chk_keys'       => 'boolean',
            'key_count'      => 'nullable|integer|min:0|max:10',
            'condition_notes'=> 'nullable|string|max:2000',
            // Signatures stored as base64 data URIs
            'buyer_signature' => 'nullable|string',
            'seller_signature'=> 'nullable|string',
        ]);

        // Require all checklist items
        $required = ['chk_buyer_id','chk_seller_id','chk_v5c','chk_keys'];
        foreach ($required as $field) {
            if (!($validated[$field] ?? false)) {
                return back()->withErrors([$field => 'All checklist items must be completed.']);
            }
        }

        // --- Replace with real handover confirmation + doc storage ---

        \Illuminate\Support\Facades\Log::info('handover_confirmed', [
            'user'       => $request->user()?->id,
            'job_id'     => $id,
            'key_count'  => $validated['key_count'] ?? null,
        ]);

        return back()->with('success', 'Handover confirmed. Payout approval is now available.');
    }

    /*
    |--------------------------------------------------------------------------
    | L4: Send transport chat message
    |--------------------------------------------------------------------------
    */
    public function sendChatMessage(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'body'        => 'required|string|max:2000',
            'attachment'  => 'nullable|file|max:25600', // 25 MB
        ]);

        $attachment = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            if ($file->getSize() > 25 * 1024 * 1024) {
                return response()->json(['error' => 'Attachment exceeds 25 MB limit.'], 422);
            }
            // --- Replace with real file storage ---
            $attachment = [
                'name' => $file->getClientOriginalName(),
                'size' => round($file->getSize() / 1024) . ' KB',
                'url'  => '#',
            ];
        }

        // --- Replace with real message persistence ---

        \Illuminate\Support\Facades\Log::info('transport_chat_message_sent', [
            'user'       => $request->user()?->id,
            'job_id'     => $id,
            'has_attach' => (bool) $attachment,
        ]);

        return response()->json([
            'success'    => true,
            'message'    => [
                'from'        => $request->user()?->name ?? 'Me',
                'direction'   => 'outbound',
                'body'        => $validated['body'],
                'sent_at'     => now()->format('d M Y H:i'),
                'attachments' => $attachment ? [$attachment] : [],
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | L2: Upload proof of collection/delivery
    |--------------------------------------------------------------------------
    */
    public function uploadProof(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:25600',
            'proof_type' => 'required|in:collection,delivery',
        ]);

        // --- Replace with real file storage ---

        \Illuminate\Support\Facades\Log::info('handover_docs_uploaded', [
            'user'   => $request->user()?->id,
            'job_id' => $id,
            'type'   => $request->input('proof_type'),
        ]);

        return back()->with('success', 'Proof uploaded successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers / mock data
    |--------------------------------------------------------------------------
    */
    private function findJob(string $id): ?array
    {
        return collect($this->mockJobs())->firstWhere('id', $id);
    }

    private function providerList(): array
    {
        return ['AutoTransport Pro', 'SwiftCar Logistics', 'National Vehicle Move', 'Internal'];
    }

    private function mockJobs(): array
    {
        $now = now();
        return [
            [
                'id'               => 'JOB-0041',
                'ref'              => 'JOB-0041',
                'deal_ref'         => 'DEL-3098',
                'deal_id'          => 'DEL-3098',
                'vehicle_title'    => 'Audi A4 2021',
                'vrm'              => 'AU21 ABC',
                'pickup_address'   => '12 Seller St, Manchester M1 1AA',
                'drop_address'     => '45 Buyer Ave, London SW1A 1AA',
                'pickup_contact'   => 'Sarah Jones — 07700 900111',
                'drop_contact'     => 'Premium Autos — 020 7946 0000',
                'slot'             => $now->copy()->addDays(1)->format('Y-m-d') . ' AM',
                'provider'         => 'AutoTransport Pro',
                'tracking_ref'     => 'ATP-882211',
                'owner'            => 'JB',
                'status'           => 'Scheduled',
                'created_at'       => $now->copy()->subDays(1)->format('d M Y'),
                'chk_buyer_id'     => false,
                'chk_seller_id'    => false,
                'chk_v5c'          => false,
                'chk_keys'         => false,
                'key_count'        => 2,
                'condition_notes'  => '',
                'buyer_signature'  => null,
                'seller_signature' => null,
                'photos'           => [],
                'documents'        => [],
                'chat_messages'    => [
                    [
                        'from'        => 'AutoTransport Pro',
                        'direction'   => 'inbound',
                        'body'        => 'Confirmed for tomorrow morning. Driver: Mike T.',
                        'sent_at'     => $now->copy()->subHours(2)->format('d M Y H:i'),
                        'attachments' => [],
                    ],
                ],
                'activity' => [
                    ['description' => 'Job created', 'date' => $now->copy()->subDays(1)->format('d M Y H:i'), 'by' => 'JB'],
                    ['description' => 'Provider assigned: AutoTransport Pro', 'date' => $now->copy()->subDays(1)->format('d M Y H:i'), 'by' => 'JB'],
                ],
            ],
            [
                'id'               => 'JOB-0039',
                'ref'              => 'JOB-0039',
                'deal_ref'         => 'DEL-3088',
                'deal_id'          => 'DEL-3088',
                'vehicle_title'    => 'Ford Focus 2020',
                'vrm'              => 'FO20 XYZ',
                'pickup_address'   => '8 Oak Road, Birmingham B1 2AB',
                'drop_address'     => '101 Main St, Bristol BS1 3CD',
                'pickup_contact'   => 'Tom Brown — 07700 900222',
                'drop_contact'     => 'Fast Cars Ltd — 0117 496 0000',
                'slot'             => $now->copy()->subDays(2)->format('Y-m-d') . ' PM',
                'provider'         => 'SwiftCar Logistics',
                'tracking_ref'     => 'SWC-441199',
                'owner'            => 'SK',
                'status'           => 'Delivered',
                'created_at'       => $now->copy()->subDays(5)->format('d M Y'),
                'chk_buyer_id'     => true,
                'chk_seller_id'    => true,
                'chk_v5c'          => true,
                'chk_keys'         => true,
                'key_count'        => 2,
                'condition_notes'  => 'Minor scratch on rear bumper — pre-existing, noted.',
                'buyer_signature'  => 'data:image/png;base64,stub',
                'seller_signature' => 'data:image/png;base64,stub',
                'photos'           => [],
                'documents'        => [
                    ['name' => 'proof_delivery.jpg', 'uploaded_at' => $now->copy()->subDays(2)->format('d M Y'), 'uploaded_by' => 'SK', 'url' => '#'],
                ],
                'chat_messages'    => [],
                'activity'         => [
                    ['description' => 'Job created',        'date' => $now->copy()->subDays(5)->format('d M Y H:i'), 'by' => 'SK'],
                    ['description' => 'Marked in transit',  'date' => $now->copy()->subDays(3)->format('d M Y H:i'), 'by' => 'SK'],
                    ['description' => 'Marked delivered',   'date' => $now->copy()->subDays(2)->format('d M Y H:i'), 'by' => 'SK'],
                    ['description' => 'Handover confirmed', 'date' => $now->copy()->subDays(2)->format('d M Y H:i'), 'by' => 'SK'],
                ],
            ],
        ];
    }
}