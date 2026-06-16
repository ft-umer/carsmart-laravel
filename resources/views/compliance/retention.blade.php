@extends('layouts.app')
@section('title','Compliance — Data Retention')
@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div class="app-container container-xxl d-flex align-items-center justify-content-between">
            <div class="page-title"><h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Data Retention Enforcement</h1></div>
            <button class="btn btn-sm btn-light-danger"><i class="ki-outline ki-trash fs-2"></i> Run Retention Job</button>
        </div>
    </div>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div class="app-container container-xxl">

            {{-- Config card --}}
            <div class="card mb-6">
                <div class="card-header pt-5"><h3 class="card-title fw-bold">Retention Policy Configuration</h3></div>
                <div class="card-body pt-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Default retention period</label>
                            <div class="input-group"><input type="number" class="form-control form-control-solid" value="12"><span class="input-group-text">months</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Include archived records</label>
                            <div class="form-check form-switch form-check-custom form-check-solid mt-3">
                                <input class="form-check-input" type="checkbox" id="archiveToggle">
                                <label class="form-check-label fw-semibold" for="archiveToggle">Off</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Run schedule</label>
                            <select class="form-select form-select-solid"><option>Weekly (Sunday 02:00)</option><option>Monthly (1st, 02:00)</option><option>Manual only</option></select>
                        </div>
                    </div>
                    <div class="mt-4"><button class="btn btn-primary">Save policy</button></div>
                </div>
            </div>

            {{-- Recent runs --}}
            <div class="card">
                <div class="card-header pt-5"><h3 class="card-title fw-bold">Recent Retention Runs</h3></div>
                <div class="card-body pt-0">
                    <table class="table table-row-dashed align-middle gs-0 gy-3">
                        <thead><tr class="fw-bold text-muted bg-light"><th class="ps-4 rounded-start">Run time</th><th>Triggered by</th><th>Records reviewed</th><th>Redacted</th><th>Deleted</th><th>Status</th><th class="rounded-end text-end pe-4">Log</th></tr></thead>
                        <tbody>
                            <tr>
                                <td class="ps-4 text-muted fs-7">2025-10-06 02:00</td>
                                <td>Scheduler</td><td>8,412</td><td>23</td><td>5</td>
                                <td><span class="badge badge-light-success">Completed</span></td>
                                <td class="text-end pe-4"><button class="btn btn-sm btn-light">View log</button></td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted fs-7">2025-09-29 02:00</td>
                                <td>Scheduler</td><td>8,301</td><td>18</td><td>3</td>
                                <td><span class="badge badge-light-success">Completed</span></td>
                                <td class="text-end pe-4"><button class="btn btn-sm btn-light">View log</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
