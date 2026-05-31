{{--
    L3 — Staging & Quality Assurance Queue
    Central queue for reviewers: checklist, pass/fail, request changes.
--}}
<div class="space-y-4">

    {{-- Filters --}}
    <div class="card border border-border p-3">
        <div class="flex flex-wrap gap-3 items-end">
            <select class="kt-input">
                <option>Any owner</option>
                <option>Me</option>
            </select>
            <select class="kt-input">
                <option>Any QA status</option>
                <option value="needs">Needs</option>
                <option value="in_progress">In Progress</option>
                <option value="passed">Passed</option>
                <option value="failed">Failed</option>
            </select>
            <select class="kt-input">
                <option>Missing items</option>
                <option value="photos">Photos</option>
                <option value="docs">Documents</option>
                <option value="kyc">KYC</option>
                <option value="pricing">Pricing</option>
            </select>
            <select class="kt-input">
                <option>Any reviewer</option>
                <option>JR</option>
                <option>AM</option>
            </select>
            <button class="kt-btn kt-btn-mono">Apply</button>
            <button class="kt-btn kt-btn-ghost">Reset</button>
        </div>
    </div>

    {{-- QA Table + Right Panel --}}
    <div class="flex gap-4">

        {{-- Table --}}
        <div class="flex-1 card border border-border overflow-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead class="bg-muted/40">
                    <tr>
                        <th class="p-3 text-left">Listing</th>
                        <th class="p-3 text-left">Vehicle</th>
                        <th class="p-3 text-right">Readiness %</th>
                        <th class="p-3 text-center">Missing Items</th>
                        <th class="p-3 text-left">QA Status</th>
                        <th class="p-3 text-left">Reviewer</th>
                        <th class="p-3 text-left">Updated</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $qaRows = [
                        ['id'=>'LST-1023','vehicle'=>'BMW 330i M Sport 2019','readiness'=>65,'missing'=>2,'status'=>'Needs','reviewer'=>'JR','updated'=>'2026-05-31'],
                        ['id'=>'LST-1025','vehicle'=>'Mercedes C200 AMG 2020','readiness'=>90,'missing'=>1,'status'=>'In Progress','reviewer'=>'AM','updated'=>'2026-05-30'],
                        ['id'=>'LST-1026','vehicle'=>'VW Golf GTI 2018','readiness'=>100,'missing'=>0,'status'=>'Passed','reviewer'=>'JR','updated'=>'2026-05-29'],
                        ['id'=>'LST-1027','vehicle'=>'Ford Focus ST 2019','readiness'=>40,'missing'=>4,'status'=>'Failed','reviewer'=>'AM','updated'=>'2026-05-28'],
                    ];
                    @endphp
                    @foreach($qaRows as $row)
                        <tr class="border-t border-border hover:bg-muted/5">
                            <td class="p-3 font-medium">{{ $row['id'] }}</td>
                            <td class="p-3">{{ $row['vehicle'] }}</td>
                            <td class="p-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-20 h-1.5 bg-muted rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full" style="width:{{ $row['readiness'] }}%"></div>
                                    </div>
                                    <span>{{ $row['readiness'] }}%</span>
                                </div>
                            </td>
                            <td class="p-3 text-center">
                                @if($row['missing'] > 0)
                                    <span class="kt-badge kt-badge-warning">{{ $row['missing'] }}</span>
                                @else
                                    <span class="kt-badge kt-badge-success">None</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="kt-badge
                                    @if($row['status']==='Passed') kt-badge-success
                                    @elseif($row['status']==='Failed') kt-badge-danger
                                    @elseif($row['status']==='In Progress') kt-badge-warning
                                    @else kt-badge-outline @endif">
                                    {{ $row['status'] }}
                                </span>
                            </td>
                            <td class="p-3">{{ $row['reviewer'] }}</td>
                            <td class="p-3 text-muted-foreground">{{ $row['updated'] }}</td>
                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button class="kt-btn kt-btn-xs kt-btn-outline">Open</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-mono">Pass</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-ghost text-danger">Fail</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Right panel: checklist --}}
        <div class="w-72 shrink-0 card border border-border p-4 space-y-4">
            <div class="font-semibold">QA Checklist</div>

            <div class="space-y-2 text-sm">
                @foreach([
                    ['Photos (6 required)','Incomplete','danger'],
                    ['V5C Document','Missing','danger'],
                    ['MOT Certificate','Present','success'],
                    ['Service Receipts','Present','success'],
                    ['Pricing Set','Complete','success'],
                    ['KYC Verified','Pending','warning'],
                    ['Seller Consent','Confirmed','success'],
                ] as [$item,$status,$badge])
                    <div class="flex items-center justify-between">
                        <span>{{ $item }}</span>
                        <span class="kt-badge kt-badge-{{ $badge }} text-xs">{{ $status }}</span>
                    </div>
                @endforeach
            </div>

            <div>
                <div class="text-xs font-medium mb-1">Reviewer Notes</div>
                <textarea class="kt-input w-full text-xs" rows="3" placeholder="Add notes…"></textarea>
            </div>

            <div class="space-y-2">
                <button class="kt-btn kt-btn-sm kt-btn-mono w-full">Pass QA</button>
                <button class="kt-btn kt-btn-sm kt-btn-outline w-full">Fail with Reasons</button>
                <button class="kt-btn kt-btn-sm kt-btn-ghost w-full">Request Changes to Owner</button>
                <button class="kt-btn kt-btn-sm kt-btn-ghost w-full">Assign Reviewer</button>
                <button class="kt-btn kt-btn-sm kt-btn-ghost w-full">Add Snag Ticket</button>
            </div>
        </div>

    </div>

</div>
