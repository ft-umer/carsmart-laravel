<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportsController extends Controller
{
    /**
     * GET /crm/imports
     * C9 — Imports & Deduplication
     */
    public function index()
    {
        return view('crm.imports', [
            'import_history' => $this->mockHistory(),
            'duplicates'     => $this->mockDuplicates(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Import wizard steps
    // -------------------------------------------------------------------------

    /**
     * POST /crm/imports/validate
     * Step 3 — Validate uploaded file with column mapping
     * Returns error rows + valid row count for preview
     */
    public function validate(Request $request)
    {
        $request->validate([
            'file'        => 'required|file|mimes:csv,txt|max:51200',
            'import_type' => 'required|in:people,vendors,leads',
            'mapping'     => 'required|array',
        ]);

        // TODO: Parse CSV; validate required fields per import_type; detect duplicates
        // TODO: Return { valid_count, error_rows: [{row, field, reason}], duplicate_rows }
        return response()->json([
            'ok'          => true,
            'valid_count' => 92,
            'error_rows'  => [
                ['row' => 14, 'field' => 'email', 'reason' => 'Missing required field: email'],
                ['row' => 37, 'field' => 'phone',  'reason' => 'Invalid phone format: 07700abc123'],
            ],
            'duplicate_rows' => [
                ['row' => 55, 'match_id' => 'CST-088', 'match_name' => 'Jane Doe', 'reason' => 'email match'],
            ],
        ]);
    }

    /**
     * POST /crm/imports/run
     * Step 5 — Execute the import (valid rows only)
     * Event: import_completed
     */
    public function run(Request $request)
    {
        $request->validate([
            'import_type' => 'required|in:people,vendors,leads',
            'mapping'     => 'required|array',
            'file_ref'    => 'required|string',   // temp file reference from validate step
        ]);

        // TODO: Load validated file; persist records; skip error rows
        // TODO: Fire import_completed event with counts
        // TODO: Log actor, row count, import type, timestamp

        return response()->json([
            'ok'       => true,
            'imported' => 92,
            'skipped'  => 3,
        ]);
    }

    /**
     * GET /crm/imports/sample
     * Download a sample CSV for a given import type
     */
    public function sample(Request $request)
    {
        $type = $request->get('type', 'people');

        $headers = match ($type) {
            'people'  => ['first_name','last_name','email','phone','source','tags','notes'],
            'vendors' => ['company_name','legal_name','company_no','vat_no','email','phone','address'],
            'leads'   => ['name','email','phone','vrm','vin','source','notes'],
            default   => ['first_name','last_name','email'],
        };

        $csv = implode(',', $headers) . "\n";
        $csv .= implode(',', array_fill(0, count($headers), 'example'));

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"sample-{$type}-import.csv\"",
        ]);
    }

    // -------------------------------------------------------------------------
    // Deduplication
    // -------------------------------------------------------------------------

    /**
     * GET /crm/imports/duplicates
     * List potential duplicates for current user's records
     */
    public function duplicates()
    {
        return response()->json($this->mockDuplicates());
    }

    /**
     * POST /crm/imports/merge
     * Merge two records (field-level picks, master chosen)
     * Event: duplicates_merged
     */
    public function merge(Request $request)
    {
        $request->validate([
            'master_id' => 'required|string',
            'merge_id'  => 'required|string',
            'picks'     => 'required|array',  // ['field' => 'master'|'merge']
        ]);

        // TODO: Apply field picks; transfer relationships; soft-delete merge_id; audit log
        return response()->json(['ok' => true, 'master_id' => $request->master_id]);
    }

    /**
     * POST /crm/imports/duplicates/{id}/dismiss
     * Dismiss a duplicate suggestion without merging
     */
    public function dismissDuplicate(string $id)
    {
        // TODO: Mark suggestion dismissed; exclude from future suggestions for this pair
        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------
    // Mock helpers
    // -------------------------------------------------------------------------

    private function mockHistory(): array
    {
        return [
            ['date'=>'2 days ago','type'=>'People','imported'=>92,'skipped'=>3,'by'=>'AM'],
            ['date'=>'1 week ago','type'=>'Leads','imported'=>240,'skipped'=>8,'by'=>'SR'],
        ];
    }

    private function mockDuplicates(): array
    {
        return [
            [
                'a' => ['id'=>'CST-001','name'=>'Jane Doe','email'=>'jane.doe@example.com','phone'=>'+44 7700 900001'],
                'b' => ['id'=>'CST-099','name'=>'J. Doe','email'=>'jane.doe@example.com','phone'=>'+44 7700 900001'],
                'reason' => 'email + phone match',
            ],
            [
                'a' => ['id'=>'CST-045','name'=>'David Hughes','email'=>'david.h@example.com','phone'=>''],
                'b' => ['id'=>'LED-2055','name'=>'D. Hughes','email'=>'david.h@example.com','phone'=>''],
                'reason' => 'email match',
            ],
        ];
    }
}
