<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommunicationsController extends Controller
{
    // -------------------------------------------------------------------------
    // Mock threads
    // -------------------------------------------------------------------------

    private function mockThreads(): array
    {
        return [
            [
                'id'       => 1,
                'name'     => 'John Smith',
                'entity'   => 'Lead LED-2041',
                'channel'  => 'email',
                'preview'  => 'Thanks for getting back to me…',
                'time'     => '2h ago',
                'unread'   => 2,
                'resolved' => false,
                'messages' => [
                    ['from'=>'John Smith','dir'=>'in','channel'=>'email','time'=>'10 Oct 14:32','text'=>'Hi, I\'m interested in getting a valuation for my BMW 330i plate AB19 CDE. Could you help?','read'=>true],
                    ['from'=>'SR (you)','dir'=>'out','channel'=>'email','time'=>'11 Oct 09:15','text'=>'Hi John! Absolutely — we\'ve started a valuation on your BMW. I\'ll have figures ready shortly.','read'=>true],
                    ['from'=>'John Smith','dir'=>'in','channel'=>'email','time'=>'11 Oct 10:04','text'=>'Thanks for getting back to me, looking forward to the figures!','read'=>false],
                ],
            ],
            [
                'id'       => 2,
                'name'     => 'Fast Cars Ltd',
                'entity'   => 'Vendor VEN-001',
                'channel'  => 'whatsapp',
                'preview'  => 'We can pick up on Wednesday…',
                'time'     => 'Yesterday',
                'unread'   => 0,
                'resolved' => false,
                'messages' => [],
            ],
            [
                'id'       => 3,
                'name'     => 'Jane Doe',
                'entity'   => 'Customer CST-001',
                'channel'  => 'sms',
                'preview'  => 'Confirmed, see you then!',
                'time'     => '2 days ago',
                'unread'   => 0,
                'resolved' => true,
                'messages' => [],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // C6 — Compose & Threads
    // -------------------------------------------------------------------------

    /**
     * GET /communications
     */
    public function index()
    {
        return view('crm.communications', [
            'threads' => $this->mockThreads(),
        ]);
    }

    /**
     * GET /communications/{id}
     * Load a specific thread
     */
    public function show(int $id)
    {
        $thread = collect($this->mockThreads())->firstWhere('id', $id);
        return response()->json($thread ?? []);
    }

    /**
     * POST /communications
     * Send a new message
     * Event: message_sent
     * Validation: DNC + consent per channel + quiet hours
     */
    public function send(Request $request)
    {
        $request->validate([
            'to'      => 'required|string',
            'channel' => 'required|in:email,sms,whatsapp',
            'message' => 'required|string|max:10000',
        ]);

        // TODO: Check DNC + consent; enforce quiet hours + daily cap
        // TODO: Resolve template variables; send via provider; log message_sent event
        // TODO: If attachment present, validate size ≤ 25 MB

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'thread_id' => rand(100, 999)]);
        }

        return redirect()->route('communications.index')
            ->with('success', 'Message sent.');
    }

    /**
     * POST /communications/schedule
     * Schedule a message for later delivery
     * Event: message_scheduled
     */
    public function schedule(Request $request)
    {
        $request->validate([
            'to'           => 'required|string',
            'channel'      => 'required|in:email,sms,whatsapp',
            'message'      => 'required|string',
            'scheduled_at' => 'required|date|after:now',
        ]);

        // TODO: Store scheduled message; fire message_scheduled; enforce quiet hours
        return response()->json(['ok' => true]);
    }

    /**
     * PATCH /communications/{id}/resolve
     * Mark thread as resolved
     */
    public function resolve(int $id)
    {
        // TODO: Mark thread resolved + audit
        return response()->json(['ok' => true]);
    }
}
