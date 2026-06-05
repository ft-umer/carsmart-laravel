<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplatesController extends Controller
{
    // -------------------------------------------------------------------------
    // Mock data
    // -------------------------------------------------------------------------

    private function mockTemplates(): array
    {
        return [
            ['id'=>1,'name'=>'Welcome — Lead received','channel'=>'Email','folder'=>'Leads','status'=>'Approved','owner'=>'SR','updated'=>'2 days ago','body'=>'Dear {{first_name}},\n\nThank you for getting in touch…','variables'=>['first_name','listing_number']],
            ['id'=>2,'name'=>'Valuation ready','channel'=>'Email','folder'=>'Leads','status'=>'Approved','owner'=>'SR','updated'=>'1 week ago','body'=>'Hi {{first_name}}, your valuation for {{vrm}} is ready: £{{valuation_amount}}.','variables'=>['first_name','vrm','valuation_amount']],
            ['id'=>3,'name'=>'Auction invite','channel'=>'Email','folder'=>'Auctions','status'=>'Approved','owner'=>'AM','updated'=>'2 weeks ago','body'=>'You are invited to {{auction_name}}…','variables'=>['auction_name','first_name']],
            ['id'=>4,'name'=>'Outbid notification','channel'=>'WhatsApp','folder'=>'Auctions','status'=>'Pending approval','owner'=>'AM','updated'=>'3 days ago','body'=>'Hi {{first_name}}, you have been outbid on lot {{listing_number}}.','variables'=>['first_name','listing_number']],
            ['id'=>5,'name'=>'Handover confirmation','channel'=>'SMS','folder'=>'Logistics','status'=>'Approved','owner'=>'JR','updated'=>'5 days ago','body'=>'Hi {{first_name}}, your handover for {{vrm}} is confirmed.','variables'=>['first_name','vrm']],
            ['id'=>6,'name'=>'Bulk promo broadcast','channel'=>'Email','folder'=>'Marketing','status'=>'Draft','owner'=>'AM','updated'=>'1 day ago','body'=>'Dear {{first_name}},\n\nExciting news…','variables'=>['first_name']],
        ];
    }

    // -------------------------------------------------------------------------
    // C8 — Templates
    // -------------------------------------------------------------------------

    /**
     * GET /crm/templates
     */
    public function index(Request $request)
    {
        $templates = $this->mockTemplates();

        if ($channel = $request->get('channel')) {
            $templates = array_filter($templates, fn($t) => strtolower($t['channel']) === strtolower($channel));
        }

        if ($status = $request->get('status')) {
            $templates = array_filter($templates, fn($t) => $t['status'] === $status);
        }

        return view('crm.templates', [
            'templates' => array_values($templates),
        ]);
    }

    /**
     * GET /crm/templates/{id}
     */
    public function show(int $id)
    {
        $template = collect($this->mockTemplates())->firstWhere('id', $id);
        return response()->json($template ?? []);
    }

    /**
     * POST /crm/templates
     * Event: template_submitted (if submitted) or saved as draft
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'channel' => 'required|in:Email,SMS,WhatsApp',
            'body'    => 'required|string',
            'folder'  => 'nullable|string',
        ]);

        // TODO: Validate variables against data model; save draft or submit for approval
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'id' => rand(100, 999)]);
        }

        return redirect()->route('crm.templates.index')
            ->with('success', 'Template saved.');
    }

    /**
     * PATCH /crm/templates/{id}
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        // TODO: Update; if previously approved, reset to Draft (re-approval required)
        return redirect()->route('crm.templates.index')
            ->with('success', 'Template updated.');
    }

    /**
     * POST /crm/templates/{id}/submit
     * Submit draft for approval
     * Event: template_submitted
     */
    public function submit(int $id)
    {
        // TODO: Set status → Pending approval + notify Compliance
        return response()->json(['ok' => true]);
    }

    /**
     * POST /crm/templates/{id}/approve
     * Approve template (Compliance / Admin)
     * Event: template_approved
     */
    public function approve(int $id)
    {
        // TODO: Set status → Approved + log actor + timestamp
        return response()->json(['ok' => true]);
    }

    /**
     * PATCH /crm/templates/{id}/archive
     */
    public function archive(int $id)
    {
        // TODO: Set status → Archived; prevent use in new messages
        return response()->json(['ok' => true]);
    }

    /**
     * DELETE /crm/templates/{id}
     */
    public function destroy(int $id)
    {
        // TODO: Hard delete only if Draft; otherwise archive
        return redirect()->route('crm.templates.index')
            ->with('success', 'Template deleted.');
    }
}
