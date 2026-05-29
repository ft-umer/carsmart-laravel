 <script>
        (function() {
            'use strict';

            /* ── Lead data from PHP (embedded as JSON) ─────────────────────────────── */
            const LEADS = @json(array_values($leads));

            /* ── State ─────────────────────────────────────────────────────────────── */
            const state = {
                leads: LEADS.map(l => ({
                    ...l
                })),
                selectedIds: new Set(),
                activeLeadId: null,
                valuationJobs: {}, // { 'LED-2041': { status: 'queued'|'fetching'|'succeeded'|'failed', message } }
                pendingApply: null, // { leadId, valuationId, amount }
            };

            /* ── DOM shortcuts ──────────────────────────────────────────────────────── */
            const $ = (sel, ctx = document) => ctx.querySelector(sel);
            const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

            /* ── Toast ──────────────────────────────────────────────────────────────── */
            function toast(msg, type = 'info') {
                const wrap = $('#toast-container');
                const colours = {
                    success: 'bg-green-600 text-white',
                    error: 'bg-destructive text-white',
                    info: 'bg-foreground text-background',
                    warning: 'bg-amber-500 text-white',
                };
                const el = document.createElement('div');
                el.className =
                    `pointer-events-auto px-4 py-2 rounded-xl shadow-xl text-sm max-w-[320px] ${colours[type] || colours.info}`;
                el.textContent = msg;
                wrap.appendChild(el);
                setTimeout(() => el.remove(), 3500);
            }

            /* ── Modal helpers ──────────────────────────────────────────────────────── */
            function openModal(id) {
                const el = $('#' + id);
                if (!el) return;
                el.classList.remove('hidden');
                el.classList.add('flex');
                $$('.modal-close, [data-modal-backdrop]', el).forEach(b => {
                    b.addEventListener('click', () => closeModal(id), {
                        once: true
                    });
                });
                // backdrop
                $('.modal-backdrop', el)?.addEventListener('click', () => closeModal(id), {
                    once: true
                });
            }

            function closeModal(id) {
                const el = $('#' + id);
                if (!el) return;
                el.classList.remove('flex');
                el.classList.add('hidden');
            }
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') $$('.modal-overlay').forEach(m => {
                    m.classList.remove('flex');
                    m.classList.add('hidden');
                });
            });

            /* ── Selection ──────────────────────────────────────────────────────────── */
            function updateSelectionUI() {
                const n = state.selectedIds.size;
                const label = $('#selected-label');
                const badge = $('#bulk-count-badge');
                if (label) label.textContent = n + ' selected';
                if (badge) badge.textContent = n > 0 ? `(${n})` : '';
                const allCbs = $$('.row-cb');
                const selectAll = $('#select-all-cb');
                if (selectAll) {
                    selectAll.indeterminate = n > 0 && n < allCbs.length;
                    selectAll.checked = n === allCbs.length && allCbs.length > 0;
                }
            }

            $('#select-all-cb')?.addEventListener('change', function() {
                $$('.row-cb').forEach(cb => {
                    cb.checked = this.checked;
                    this.checked ? state.selectedIds.add(cb.value) : state.selectedIds.delete(cb.value);
                });
                updateSelectionUI();
            });

            document.addEventListener('change', e => {
                if (!e.target.classList.contains('row-cb')) return;
                e.target.checked ? state.selectedIds.add(e.target.value) : state.selectedIds.delete(e.target
                    .value);
                updateSelectionUI();
            });

            /* ── Valuation job status badges ─────────────────────────────────────────── */
            const JOB_COLOURS = {
                queued: 'kt-badge-outline',
                fetching: 'kt-badge-warning',
                succeeded: 'kt-badge-success',
                failed: 'kt-badge-destructive',
            };

            function renderJobBadge(leadId) {
                $$('.valuation-job-status[data-lead-id="' + leadId + '"]').forEach(el => {
                    const job = state.valuationJobs[leadId];
                    if (!job || job.status === 'idle') {
                        el.innerHTML = '';
                        return;
                    }
                    el.innerHTML =
                        `<span class="kt-badge ${JOB_COLOURS[job.status]} kt-badge-sm" title="${job.message || ''}">${job.status}</span>`;
                });
            }

            /* ── Pull valuation (single) ─────────────────────────────────────────────── */
            function pullValuationSingle(leadId) {
                const lead = state.leads.find(l => l.id === leadId);
                if (!lead || (!lead.vrm && !lead.vin)) {
                    toast('Add VRM or VIN to this lead first.', 'warning');
                    return;
                }
                state.valuationJobs[leadId] = {
                    status: 'queued',
                    message: 'Queued for fetch'
                };
                renderJobBadge(leadId);
                setTimeout(() => {
                    state.valuationJobs[leadId] = {
                        status: 'fetching',
                        message: 'Fetching from provider…'
                    };
                    renderJobBadge(leadId);
                    // Update quick view fetch status
                    updateQvFetchStatus(leadId, 'fetching', 'Contacting provider…');
                    // Simulate async
                    setTimeout(() => {
                        const ok = Math.random() < 0.8;
                        if (ok) {
                            const amt = Math.round(8000 + Math.random() * 18000);
                            const newVal = {
                                id: 'v' + Math.floor(Math.random() * 99999),
                                date: new Date().toISOString(),
                                source: 'AutoProvider',
                                valuer: 'ProviderX',
                                amount: amt,
                                notes: 'Auto-pulled',
                                comps: [],
                                applied: false,
                            };
                            lead.valuations.push(newVal);
                            state.valuationJobs[leadId] = {
                                status: 'succeeded',
                                message: '£' + amt.toLocaleString()
                            };
                            renderJobBadge(leadId);
                            toast('Valuation fetched: £' + amt.toLocaleString(), 'success');
                            updateQvFetchStatus(leadId, 'succeeded', '£' + amt.toLocaleString() +
                                ' pulled');
                            if (state.activeLeadId === leadId) renderQuickView(leadId);
                        } else {
                            state.valuationJobs[leadId] = {
                                status: 'failed',
                                message: 'Provider error / rate-limited. Retry.'
                            };
                            renderJobBadge(leadId);
                            toast('Valuation fetch failed. Provider down or rate-limited.', 'error');
                            updateQvFetchStatus(leadId, 'failed',
                                'Provider error. Please retry in a few minutes.');
                            if (state.activeLeadId === leadId) renderQuickView(leadId);
                        }
                    }, 1000 + Math.random() * 1500);
                }, 400);
            }

            /* ── Bulk pull valuations ────────────────────────────────────────────────── */
            $('#bulk-pull-valuations-btn')?.addEventListener('click', () => {
                if (!state.selectedIds.size) {
                    toast('Select leads first.', 'warning');
                    return;
                }
                const withVrm = [...state.selectedIds].filter(id => {
                    const l = state.leads.find(x => x.id === id);
                    return l && (l.vrm || l.vin);
                });
                if (!withVrm.length) {
                    toast('No selected leads have a VRM/VIN.', 'warning');
                    return;
                }
                toast(`Queuing ${withVrm.length} valuation fetch(es)…`, 'info');
                withVrm.forEach((id, i) => setTimeout(() => pullValuationSingle(id), i * 650));
                state.selectedIds.clear();
                updateSelectionUI();
                $$('.row-cb').forEach(cb => cb.checked = false);
            });

            function updateQvFetchStatus(leadId, status, msg) {
                if (state.activeLeadId !== leadId) return;
                const el = $('#qv-val-fetch-status');
                if (!el) return;
                const colours = {
                    fetching: 'bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-300 border border-amber-200',
                    succeeded: 'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-300 border border-green-200',
                    failed: 'bg-red-50 dark:bg-red-900/20 text-destructive border border-red-200',
                };
                el.className = 'text-xs px-2 py-1 rounded-md ' + (colours[status] || '');
                el.textContent = msg;
                el.classList.remove('hidden');
                if (status === 'succeeded') setTimeout(() => el.classList.add('hidden'), 4000);
            }

            /* ── Quick View ──────────────────────────────────────────────────────────── */
            function renderQuickView(leadId) {
                 openModal('modal-quick-view');

    const lead = state.leads.find(l => l.id === leadId);
    if (!lead) return;
                
                
                state.activeLeadId = leadId;

                // Header
                $('#qv-lead-name').textContent = lead.name + ' · ' + lead.stage;
                $('#qv-lead-meta').textContent = 'Owner: ' + (lead.owner || 'Unassigned') + ' · ' + lead.id;

                const openLink = $('#qv-open-link');
                if (openLink) {
                    openLink.href = '/crm/leads/' + lead.id;
                    openLink.classList.remove('hidden');
                }

                // Overview tab content
                const overviewHtml = `
      <div class="grid grid-cols-2 gap-3 text-xs">
        <div><span class="text-muted-foreground">Email</span><br><strong>${lead.email || '—'}</strong></div>
        <div><span class="text-muted-foreground">Phone</span><br><strong>${lead.phone || '—'}</strong></div>
        <div><span class="text-muted-foreground">Source</span><br><strong>${lead.source || '—'}</strong></div>
        <div><span class="text-muted-foreground">Date added</span><br><strong>${lead.date_added || '—'}</strong></div>
        <div><span class="text-muted-foreground">Priority</span><br><strong>${lead.priority || 'Normal'}</strong></div>
        <div><span class="text-muted-foreground">SLA due</span><br><strong>${lead.sla_due || '—'}</strong></div>
      </div>
      <div class="mt-3">
        <div class="text-xs text-muted-foreground mb-1">Consent</div>
        <div class="flex gap-2 flex-wrap">
          <span class="kt-badge ${lead.consent?.email ? 'kt-badge-success' : 'kt-badge-outline'} kt-badge-sm">Email ${lead.consent?.email ? '✔' : '✖'}</span>
          <span class="kt-badge ${lead.consent?.sms   ? 'kt-badge-success' : 'kt-badge-outline'} kt-badge-sm">SMS ${lead.consent?.sms   ? '✔' : '✖'}</span>
          <span class="kt-badge ${lead.consent?.whatsapp ? 'kt-badge-success' : 'kt-badge-outline'} kt-badge-sm">WhatsApp ${lead.consent?.whatsapp ? '✔' : '✖'}</span>
        </div>
      </div>
      ${lead.notes ? `<div class="mt-3 text-xs text-muted-foreground p-2 bg-muted/30 rounded-lg">${lead.notes}</div>` : ''}
    `;
                $('#qv-tab-overview').innerHTML = overviewHtml;

                // Vehicles tab
                const vehicleTitle = $('#qv-vehicle-title');
                const vehicleSub = $('#qv-vehicle-sub');
                const valCard = $('#qv-val-card');
                const btnPull = $('#qv-btn-pull-val');
                const btnAddV = $('#qv-btn-add-val');
                const btnApply = $('#qv-btn-apply-pricing');

                if (lead.vrm || lead.vin) {
                    vehicleTitle.textContent = lead.vrm || lead.vin;
                    vehicleSub.textContent = (lead.vrm ? 'VRM' : 'VIN') + ' present — valuation actions enabled.';
                    btnPull.disabled = false;
                    btnAddV.disabled = false;

                    const latest = lead.valuations?.slice(-1)[0];
                    if (latest) {
                        valCard.classList.remove('hidden');
                        $('#qv-val-amount').textContent = '£' + Number(latest.amount).toLocaleString();
                        $('#qv-val-source').textContent = latest.source + ' · ' + new Date(latest.date)
                            .toLocaleString();
                        $('#qv-val-delta').textContent = lead.linked_listing_id ? 'Δ vs guide: +£200 (+1.4%)' :
                            'Δ vs guide: —';
                        btnApply.disabled = !lead.linked_listing_id;
                        btnApply.title = lead.linked_listing_id ? '' :
                            'No linked listing. Value stored and carries over on conversion.';
                    } else {
                        valCard.classList.add('hidden');
                        btnApply.disabled = true;
                    }

                    // Wire pull button
                    btnPull.onclick = () => pullValuationSingle(leadId);
                    btnAddV.onclick = () => {
                        $('#val-lead-id').value = leadId;
                        openModal('modal-add-valuation');
                    };
                    btnApply.onclick = () => {
                        const v = lead.valuations?.slice(-1)[0];
                        if (!v) return;
                        state.pendingApply = {
                            leadId,
                            valuationId: v.id,
                            amount: v.amount
                        };
                        $('#apply-val-amount').textContent = '£' + Number(v.amount).toLocaleString();
                        $('#apply-val-delta').textContent = lead.linked_listing_id ? 'Δ vs current guide: +£200' :
                            '—';
                        $('#apply-listing-info').textContent = lead.linked_listing_id ? 'Listing: ' + lead
                            .linked_listing_id : '';
                        openModal('modal-apply-pricing');
                    };

                    // Valuation history
                    renderValHistory(lead);
                } else {
                    vehicleTitle.textContent = 'No VRM/VIN';
                    vehicleSub.textContent = 'Add VRM or VIN first to enable valuation actions.';
                    valCard.classList.add('hidden');
                    btnPull.disabled = btnAddV.disabled = btnApply.disabled = true;
                    $('#qv-val-history-body').innerHTML =
                        '<tr><td colspan="5" class="p-3 text-center text-xs text-muted-foreground">No valuations</td></tr>';
                }

                // Tasks tab
                const taskList = $('#qv-tasks-list');
                if (lead.tasks?.length) {
                    taskList.innerHTML = lead.tasks.map(t => `
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-muted/30 text-xs">
          <span class="kt-badge ${t.status === 'overdue' ? 'kt-badge-destructive' : 'kt-badge-outline'} kt-badge-sm">${t.status}</span>
          <span class="flex-1">${t.title}</span>
          <span class="text-muted-foreground">${t.due}</span>
        </div>
      `).join('');
                } else {
                    taskList.innerHTML = '<p class="text-xs text-muted-foreground">No tasks.</p>';
                }

                // Activity tab
                const actList = $('#qv-activity-list');
                if (lead.activity?.length) {
                    actList.innerHTML = lead.activity.slice().reverse().map(a => `
        <div class="flex gap-2 text-xs">
          <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
          <div>
            <div class="font-medium">${a.description}</div>
            <div class="text-muted-foreground">${a.date}</div>
          </div>
        </div>
      `).join('');
                } else {
                    actList.innerHTML = '<p class="text-xs text-muted-foreground">No activity.</p>';
                }

                // Footer
                $('#qv-footer')?.classList.remove('hidden');
                $('#qv-btn-convert').onclick = () => {
                    if (confirm('Convert LED-' + leadId + ' to Listing?')) {
                        window.location.href = '/crm/leads/' + leadId + '/convert-listing';
                    }
                };

                // Switch to overview tab
                switchQvTab('overview');
            }

            function renderValHistory(lead) {
                const tbody = $('#qv-val-history-body');
                if (!lead.valuations?.length) {
                    tbody.innerHTML =
                        '<tr><td colspan="5" class="p-3 text-center text-xs text-muted-foreground">No valuations</td></tr>';
                    return;
                }
                tbody.innerHTML = lead.valuations.slice().reverse().map(v => `
      <tr>
        <td class="p-2">${new Date(v.date).toLocaleDateString()}</td>
        <td class="p-2">${v.source}</td>
        <td class="p-2 text-right">£${Number(v.amount).toLocaleString()}</td>
        <td class="p-2">${v.notes || '—'}</td>
        <td class="p-2">
          <button class="kt-btn kt-btn-ghost kt-btn-sm apply-val-btn"
                  data-val-id="${v.id}" data-amount="${v.amount}"
                  ${lead.linked_listing_id ? '' : 'disabled title="No linked listing"'}>
            Apply
          </button>
        </td>
      </tr>
    `).join('');

                $$('.apply-val-btn', tbody).forEach(btn => {
                    btn.addEventListener('click', () => {
                        const amt = Number(btn.dataset.amount);
                        state.pendingApply = {
                            leadId: lead.id,
                            valuationId: btn.dataset.valId,
                            amount: amt
                        };
                        $('#apply-val-amount').textContent = '£' + amt.toLocaleString();
                        $('#apply-val-delta').textContent = lead.linked_listing_id ?
                            'Δ vs current guide: —' : '—';
                        $('#apply-listing-info').textContent = lead.linked_listing_id ? 'Listing: ' +
                            lead.linked_listing_id : '';
                        openModal('modal-apply-pricing');
                    });
                });
            }

            function switchQvTab(tab) {
                $$('.qv-tab-btn').forEach(b => b.classList.toggle('kt-btn-mono', b.dataset.qvTab === tab));
                $$('.qv-tab-content').forEach(c => c.classList.toggle('hidden', c.id !== 'qv-tab-' + tab));
            }

            $$('.qv-tab-btn').forEach(btn => btn.addEventListener('click', () => switchQvTab(btn.dataset.qvTab)));

            /* ── Row action delegation ───────────────────────────────────────────────── */
            document.addEventListener('click', e => {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;
                const action = btn.dataset.action;
                const id = btn.dataset.id;

                if (action === 'quick-view') renderQuickView(id);
                if (action === 'pull-val-single') pullValuationSingle(id);
                if (action === 'delete-single') {
                    const form = $('#form-delete-lead');
                    if (form) form.action = '/crm/leads/' + id;
                    openModal('modal-delete');
                }
            });

            /* ── Quick add lead modal ─────────────────────────────────────────────────── */
            $('#btn-quick-add-lead')?.addEventListener('click', () => openModal('modal-quick-add'));

            /* ── Add valuation form ──────────────────────────────────────────────────── */
            $('#form-add-valuation')?.addEventListener('submit', async function(e) {
                e.preventDefault();
                const fd = new FormData(this);
                const leadId = $('#val-lead-id').value;
                const lead = state.leads.find(l => l.id === leadId);
                if (!lead) return;

                const newVal = {
                    id: 'v' + Math.floor(Math.random() * 99999),
                    date: new Date().toISOString(),
                    source: fd.get('source'),
                    valuer: fd.get('valuer') || '—',
                    amount: parseFloat(fd.get('amount')),
                    notes: fd.get('notes') || '',
                    comps: fd.get('comps') ? fd.get('comps').split(',').map(s => s.trim()) : [],
                    applied: false,
                };
                lead.valuations.push(newVal);

                closeModal('modal-add-valuation');
                toast('Valuation added: £' + newVal.amount.toLocaleString(), 'success');
                if (state.activeLeadId === leadId) renderQuickView(leadId);
                this.reset();
            });

            /* ── Apply to pricing confirm ─────────────────────────────────────────────── */
            $('#btn-confirm-apply-pricing')?.addEventListener('click', () => {
                if (!state.pendingApply) return;
                const {
                    leadId,
                    valuationId,
                    amount
                } = state.pendingApply;
                const lead = state.leads.find(l => l.id === leadId);
                if (lead) {
                    const v = lead.valuations.find(x => x.id === valuationId);
                    if (v) v.applied = true;
                }
                closeModal('modal-apply-pricing');
                toast('Valuation applied to ' + (lead?.linked_listing_id || 'listing') + '.', 'success');
                state.pendingApply = null;
                if (state.activeLeadId === leadId) renderQuickView(leadId);
            });

            /* ── Bulk action modal triggers ──────────────────────────────────────────── */
            $$('.bulk-action-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const action = btn.dataset.bulk;
                    if (!state.selectedIds.size && action !== 'pull-valuations') {
                        toast('Select at least one lead first.', 'warning');
                        return;
                    }
                    if (action === 'assign-owner') openModal('modal-bulk-assign');
                    if (action === 'move-stage') openModal('modal-bulk-stage');
                });
            });

            $('#btn-confirm-bulk-assign')?.addEventListener('click', () => {
                const owner = $('#bulk-assign-owner-select').value;
                state.leads.filter(l => state.selectedIds.has(l.id)).forEach(l => l.owner = owner);
                closeModal('modal-bulk-assign');
                toast('Owner assigned: ' + owner, 'success');
            });

            $('#btn-confirm-bulk-stage')?.addEventListener('click', () => {
                const stage = $('#bulk-stage-select').value;
                state.leads.filter(l => state.selectedIds.has(l.id)).forEach(l => l.stage = stage);
                closeModal('modal-bulk-stage');
                toast('Stage moved: ' + stage, 'success');
            });

            /* ── Refresh table ────────────────────────────────────────────────────────── */
            $('#btn-refresh-table')?.addEventListener('click', () => {
                Object.keys(state.valuationJobs).forEach(id => {
                    if (state.valuationJobs[id].status === 'succeeded') delete state.valuationJobs[id];
                });
                toast('Table refreshed.', 'info');
            });

            /* ── Init lucide icons ────────────────────────────────────────────────────── */
            if (typeof lucide !== 'undefined') lucide.createIcons();

        })();
        
        document.addEventListener('click', function (e) {
    const target = e.target.closest('[data-close-modal]');

    if (!target) return;

    const modalId = target.dataset.closeModal;

    const modal = document.getElementById(modalId);

    if (!modal) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');
});
    </script>
    
    