/**
 * public/js/auctions/auctions.js
 * Phase 2 — Auctions module (frontend-only, in-memory mock)
 * Covers: A0 Index, A1 Calendar, A2 Wizard, A3 Detail, A4 Lots,
 *         A5 Live Console, A6 Lot Detail, A7 Participants,
 *         A8 Post-auction, A9 Exchange Proposals, A10 Automations
 */

(function () {
    'use strict';

    /* ================================================================
       TOAST
       ================================================================ */
    const toastBox = document.createElement('div');
    toastBox.className = 'fixed top-4 right-4 z-[200] flex flex-col gap-2 pointer-events-none';
    document.body.appendChild(toastBox);

    function toast(msg, type = 'success') {
        const n = document.createElement('div');
        n.className = 'px-3 py-2 rounded text-sm shadow text-white pointer-events-auto ' +
            ({ error: 'bg-red-600', warning: 'bg-yellow-600', info: 'bg-blue-600' }[type] || 'bg-green-600');
        n.textContent = msg;
        toastBox.appendChild(n);
        setTimeout(() => n.remove(), 3500);
    }

    /* ================================================================
       MOCK DATA
       ================================================================ */
    const auctions = [
        {
            id: 'AUC-205', name: 'October Prime Sale',
            start: '2025-10-12T10:00', end: '2025-10-12T16:00',
            status: 'Planned', visibility: 'Public', owner: 'JR',
            description: 'Monthly flagship sale of quality vehicles.',
            cohort_tag: 'Prime', closing_style: 'single',
            proxy_bidding: true, sniper_protection: true, sniper_minutes: 2,
            auto_accept: true, bin_precedence: true,
            start_price_mode: 'zero', increment_schema: 'standard',
            participant_mode: 'all', gate_kyc: true, gate_card: true,
            allow_exchange: true,
            lots: [
                { id: 'L001', lot_num: '001', listing: 'LST-1023', vehicle: 'BMW 330i M Sport 2019',
                  start_price: 0, reserve: true, bin_enabled: false, state: 'Ready',
                  current_bid: 14000, reserve_met: true, bidders: 4,
                  bids: [
                      { ts: '14:02:11', vendor: 'Vendor X', amount: 14000, proxy: true },
                      { ts: '14:01:59', vendor: 'Vendor Y', amount: 13750, proxy: false },
                      { ts: '14:01:30', vendor: 'Vendor X', amount: 13500, proxy: true },
                  ]},
                { id: 'L002', lot_num: '002', listing: 'LST-1040', vehicle: 'Audi A6 Avant 2017',
                  start_price: 500, reserve: false, bin_enabled: false, state: 'Ready',
                  current_bid: 10500, reserve_met: false, bidders: 2,
                  bids: [
                      { ts: '14:03:05', vendor: 'Vendor Z', amount: 10500, proxy: false },
                  ]},
            ],
            participants: [
                { name: 'Vendor X Ltd',  kyb: 'Verified', card: true,  status: 'Accepted', last_seen: '5m ago' },
                { name: 'Vendor Y Cars', kyb: 'Verified', card: true,  status: 'Accepted', last_seen: '12m ago' },
                { name: 'Vendor Z',      kyb: 'Pending',  card: false, status: 'Invited',  last_seen: '—' },
            ],
            messages: [],
            activity: [
                { ts: '2025-10-01T09:00Z', actor: 'JR', event: 'auction_created',   detail: 'Auction created as Draft' },
                { ts: '2025-10-05T14:00Z', actor: 'JR', event: 'auction_published', detail: 'Auction published' },
            ],
            exchange_proposals: [
                { id: 'EP-001', lot_id: 'L001', offered_by: 'Vendor Y Cars',
                  offered_vehicle: 'Mercedes C220d 2020 (LST-1041)', cash_diff: 500,
                  expiry: '2025-10-12T09:00', notes: 'Clean car, full service history', active: true }
            ],
        },
        {
            id: 'AUC-206', name: 'Evening Prestige',
            start: '2025-10-20T18:00', end: '2025-10-20T21:00',
            status: 'Published', visibility: 'Private', owner: 'AM',
            description: 'Curated prestige and performance vehicles.',
            cohort_tag: 'Prestige', closing_style: 'staggered',
            proxy_bidding: true, sniper_protection: true, sniper_minutes: 3,
            auto_accept: true, bin_precedence: true,
            start_price_mode: 'guide', increment_schema: 'premium',
            participant_mode: 'invite', gate_kyc: true, gate_card: true,
            allow_exchange: false,
            lots: [
                { id: 'L003', lot_num: '001', listing: 'LST-1055', vehicle: 'Porsche 911 Carrera 2021',
                  start_price: 0, reserve: true, bin_enabled: false, state: 'Ready',
                  current_bid: 0, reserve_met: false, bidders: 0, bids: [] },
            ],
            participants: [
                { name: 'Prestige Motors',  kyb: 'Verified', card: true, status: 'Accepted', last_seen: '1h ago' },
            ],
            messages: [],
            activity: [
                { ts: '2025-10-08T10:00Z', actor: 'AM', event: 'auction_created',   detail: 'Auction created' },
                { ts: '2025-10-09T11:00Z', actor: 'AM', event: 'auction_published', detail: 'Published' },
            ],
            exchange_proposals: [],
        },
    ];

    /* ================================================================
       STATE
       ================================================================ */
    const state = {
        view: 'list',
        calView: 'month',
        calDate: new Date(2025, 9, 1), // Oct 2025
        selectedAuctions: new Set(),
        activeAuction: null,
        activeLot: null,
        wizardStep: 1,
        wizardTotal: 7,
        filterSearch: '',
        filterStatus: '',
        filterVisibility: '',
        liveConsoleInterval: null,
        publishTarget: null,
    };

    /* ================================================================
       DOM HELPERS
       ================================================================ */
    const $ = id => document.getElementById(id);
    function fmt(n) { return typeof n === 'number' ? '£' + n.toLocaleString() : '—'; }
    function genId(p = 'AUC') { return p + '-' + Math.floor(Math.random() * 9000 + 1000); }
    function fmtDT(s) {
        if (!s) return '—';
        try { return new Date(s).toLocaleString('en-GB', { dateStyle: 'short', timeStyle: 'short' }); }
        catch { return s; }
    }

    function statusBadge(s) {
        const map = { Planned: 'kt-badge-outline', Published: 'kt-badge-info', Live: 'kt-badge-success',
                      Paused: 'kt-badge-warning', Ended: 'kt-badge-secondary', Archived: 'kt-badge-outline' };
        return `<span class="kt-badge kt-badge-sm ${map[s] || 'kt-badge-outline'}">${s}</span>`;
    }

    /* ================================================================
       A0 — RENDER TABLE
       ================================================================ */
    function getFiltered() {
        return auctions.filter(a => {
            const t = state.filterSearch.toLowerCase();
            if (t && !([a.id, a.name, a.owner].some(v => v?.toLowerCase().includes(t)))) return false;
            if (state.filterStatus     && a.status !== state.filterStatus)         return false;
            if (state.filterVisibility && a.visibility !== state.filterVisibility) return false;
            return true;
        });
    }

    function renderTable() {
        const tbody = $('auctions-tbody');
        const filtered = getFiltered();

        if (!filtered.length) {
            tbody.innerHTML = `<tr><td colspan="10" class="p-6 text-center text-sm text-muted-foreground">
                No auctions found. <button class="kt-btn kt-btn-ghost kt-btn-sm underline"
                onclick="document.getElementById('btn-reset-filters').click()">Reset filters</button>
            </td></tr>`;
            $('auctions-pagination').textContent = 'No auctions';
            return;
        }

        tbody.innerHTML = filtered.map((a) => {
            const liveLots  = a.lots.filter(l => l.state === 'Live').length;
            const endedLots = a.lots.filter(l => l.state === 'Ended').length;
            const metLots   = a.lots.filter(l => l.reserve_met).length;
            return `
            <tr data-id="${a.id}" class="hover:bg-muted/5 transition-colors">
                <td class="p-3">
                    <input data-id="${a.id}" class="auction-cb form-checkbox" type="checkbox"
                           ${state.selectedAuctions.has(a.id) ? 'checked' : ''}>
                </td>
                <td class="p-3">
                    <div class="font-medium text-sm">${a.id}</div>
                    <div class="text-xs text-muted-foreground">${a.name}</div>
                    ${a.cohort_tag ? `<span class="kt-badge kt-badge-sm kt-badge-outline mt-1">${a.cohort_tag}</span>` : ''}
                </td>
                <td class="p-3 text-xs">
                    ${fmtDT(a.start)}<br>
                    <span class="text-muted-foreground">→ ${fmtDT(a.end)}</span>
                </td>
                <td class="p-3 text-sm text-right">${a.lots.length}</td>
                <td class="p-3 text-sm text-right">
                    ${liveLots > 0 ? `<span class="text-green-600 font-medium">${liveLots}</span>` : liveLots}
                </td>
                <td class="p-3 text-sm text-right">${endedLots}</td>
                <td class="p-3 text-xs">${metLots}/${a.lots.length}</td>
                <td class="p-3 text-xs">
                    <span class="kt-badge kt-badge-sm ${a.visibility === 'Private' ? 'kt-badge-outline' : 'kt-badge-info'}">${a.visibility}</span>
                </td>
                <td class="p-3 text-xs">${a.owner}</td>
                <td class="p-3">
                    <div class="flex gap-1 flex-wrap justify-end">
                        <button class="kt-btn kt-btn-ghost kt-btn-sm"    data-act="open"      data-id="${a.id}">Open</button>
                        <button class="kt-btn kt-btn-outline kt-btn-sm"  data-act="quick"     data-id="${a.id}">Quick view</button>
                        <button class="kt-btn kt-btn-ghost kt-btn-sm"    data-act="publish"   data-id="${a.id}">Publish</button>
                        <button class="kt-btn kt-btn-ghost kt-btn-sm"    data-act="console"   data-id="${a.id}">Console</button>
                        <button class="kt-btn kt-btn-outline kt-btn-sm"  data-act="duplicate" data-id="${a.id}">Duplicate</button>
                    </div>
                </td>
            </tr>`;
        }).join('');

        // checkboxes
        tbody.querySelectorAll('.auction-cb').forEach(cb => cb.onchange = () => {
            cb.checked ? state.selectedAuctions.add(cb.dataset.id) : state.selectedAuctions.delete(cb.dataset.id);
            updateBulkCount();
        });

        // row actions
        tbody.querySelectorAll('[data-act]').forEach(b => {
            b.onclick = () => {
                const id  = b.dataset.id;
                const act = b.dataset.act;
                if (act === 'open' || act === 'quick') openAuctionDetail(id);
                if (act === 'publish')   openPublishConfirm(id);
                if (act === 'console')   openLiveConsole(id);
                if (act === 'duplicate') duplicateAuction(id);
            };
        });

        $('auctions-pagination').textContent = `Showing ${filtered.length} auction${filtered.length !== 1 ? 's' : ''}`;
    }

    function updateBulkCount() {
        $('bulk-count').textContent = `${state.selectedAuctions.size} selected`;
        $('select-all').indeterminate = state.selectedAuctions.size > 0 && state.selectedAuctions.size < auctions.length;
        $('select-all').checked = state.selectedAuctions.size === auctions.length && auctions.length > 0;
    }

    /* ================================================================
       FILTERS
       ================================================================ */
    function bindFilters() {
        $('btn-apply-filters').onclick = applyFilters;
        $('btn-reset-filters').onclick = resetFilters;
        $('filter-search').addEventListener('input', debounce(applyFilters, 280));
        $('btn-export-csv').onclick = exportCSV;
    }

    function applyFilters() {
        state.filterSearch     = $('filter-search').value.trim();
        state.filterStatus     = $('filter-status').value;
        state.filterVisibility = $('filter-visibility').value;
        renderTable();
        renderCalendar();
    }

    function resetFilters() {
        ['filter-search','filter-status','filter-visibility'].forEach(id => $(id) && ($(id).value = ''));
        state.filterSearch = ''; state.filterStatus = ''; state.filterVisibility = '';
        renderTable(); renderCalendar();
    }

    function exportCSV() {
        const rows = getFiltered().map(a =>
            [a.id, `"${a.name}"`, fmtDT(a.start), fmtDT(a.end), a.lots.length, a.status, a.visibility, a.owner].join(','));
        const csv = ['ID,Name,Start,End,Lots,Status,Visibility,Owner', ...rows].join('\n');
        const a = document.createElement('a');
        a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
        a.download = 'auctions-export.csv';
        a.click();
        toast('CSV exported');
    }

    function debounce(fn, ms) { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; }

    /* ================================================================
       VIEW SWITCHING (List / Calendar)
       ================================================================ */
    function bindViewSwitching() {
        document.querySelectorAll('.view-tab-btn').forEach(b => b.onclick = () => {
            state.view = b.dataset.view;
            document.querySelectorAll('.view-tab-btn').forEach(x => {
                x.classList.toggle('border-primary',         x.dataset.view === state.view);
                x.classList.toggle('font-medium',            x.dataset.view === state.view);
                x.classList.toggle('text-foreground',        x.dataset.view === state.view);
                x.classList.toggle('border-transparent',     x.dataset.view !== state.view);
                x.classList.toggle('text-muted-foreground',  x.dataset.view !== state.view);
            });
            $('view-list').classList.toggle('hidden',     state.view !== 'list');
            $('view-calendar').classList.toggle('hidden', state.view !== 'calendar');
            if (state.view === 'calendar') renderCalendar();
        });
        $('btn-view-calendar').onclick = () =>
            document.querySelector('[data-view="calendar"]')?.click();
    }

    /* ================================================================
       SELECT ALL + BULK ACTIONS
       ================================================================ */
    function bindSelectAll() {
        $('select-all').onchange = function () {
            state.selectedAuctions.clear();
            if (this.checked) auctions.forEach(a => state.selectedAuctions.add(a.id));
            updateBulkCount(); renderTable();
        };
    }

    function bindBulkActions() {
        $('bulk-actions-toggle').onclick = () =>
            $('bulk-actions-menu').classList.toggle('hidden');
        document.querySelectorAll('[data-bulk]').forEach(b => b.onclick = () => {
            $('bulk-actions-menu').classList.add('hidden');
            const ids = Array.from(state.selectedAuctions);
            if (!ids.length) { toast('Select auctions first', 'warning'); return; }
            const action = b.dataset.bulk;
            if (action === 'publish')   ids.forEach(id => { const a = auctions.find(x => x.id === id); if (a) a.status = 'Published'; });
            if (action === 'pause')     ids.forEach(id => { const a = auctions.find(x => x.id === id); if (a) a.status = 'Paused'; });
            if (action === 'duplicate') ids.forEach(id => duplicateAuction(id, true));
            if (action === 'archive')   ids.forEach(id => { const a = auctions.find(x => x.id === id); if (a) a.status = 'Archived'; });
            logEvent('auction_bulk_action_performed', { action, ids });
            toast(`${action} applied to ${ids.length} auction(s)`);
            state.selectedAuctions.clear(); updateBulkCount(); renderTable();
        });
    }

    /* ================================================================
       A1 — CALENDAR
       ================================================================ */
    function renderCalendar() {
        const grid  = $('cal-grid');
        const title = $('cal-title');
        if (!grid) return;

        const y = state.calDate.getFullYear();
        const m = state.calDate.getMonth();
        title.textContent = state.calDate.toLocaleString('en-GB', { month: 'long', year: 'numeric' });

        const firstDay = new Date(y, m, 1).getDay(); // 0=Sun
        const offset   = firstDay === 0 ? 6 : firstDay - 1; // Mon=0
        const daysInMonth = new Date(y, m + 1, 0).getDate();

        const cells = [];
        for (let i = 0; i < offset; i++) cells.push(null);
        for (let d = 1; d <= daysInMonth; d++) cells.push(d);
        while (cells.length % 7 !== 0) cells.push(null);

        const today = new Date();
        let html = '';
        cells.forEach(d => {
            if (!d) { html += `<div class="min-h-[80px] bg-muted/10 p-1"></div>`; return; }
            const isToday = d === today.getDate() && m === today.getMonth() && y === today.getFullYear();
            const dayAuctions = auctions.filter(a => {
                const s = new Date(a.start);
                return s.getDate() === d && s.getMonth() === m && s.getFullYear() === y;
            });
            const clash = dayAuctions.length > 1;
            html += `
            <div class="min-h-[80px] p-1 hover:bg-muted/20 cursor-pointer transition-colors ${clash ? 'bg-yellow-50/10' : ''}"
                 data-cal-day="${d}">
                <div class="text-xs font-medium mb-1 ${isToday ? 'w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center' : 'text-muted-foreground'}">
                    ${d}
                </div>
                ${dayAuctions.map(a => `
                <div class="text-[10px] rounded px-1 py-0.5 mb-0.5 truncate cursor-pointer font-medium
                            ${a.status === 'Live' ? 'bg-green-100 text-green-800' :
                              a.status === 'Published' ? 'bg-blue-100 text-blue-800' :
                              'bg-muted text-muted-foreground'}"
                     data-act="open" data-id="${a.id}" title="${a.name}">
                    ${a.id} ${a.name}
                </div>`).join('')}
            </div>`;
        });
        grid.innerHTML = html;

        // clash warning
        const clashDays = auctions.reduce((acc, a) => {
            const s = new Date(a.start);
            if (s.getMonth() === m && s.getFullYear() === y) {
                const k = s.toDateString();
                acc[k] = (acc[k] || 0) + 1;
            }
            return acc;
        }, {});
        const hasClash = Object.values(clashDays).some(v => v > 1);
        $('cal-clash-warning')?.classList.toggle('hidden', !hasClash);

        // click handlers on calendar cells
        grid.querySelectorAll('[data-act="open"]').forEach(el =>
            el.onclick = (e) => { e.stopPropagation(); openAuctionDetail(el.dataset.id); });

        // drag-create: clicking empty day opens create modal
        grid.querySelectorAll('[data-cal-day]').forEach(cell => cell.onclick = () => {
            const d = parseInt(cell.dataset.calDay);
            if (!cell.querySelector('[data-id]')) {
                // pre-fill start date in wizard
                const dateStr = `${y}-${String(m + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}T10:00`;
                openModal($('create-auction-modal'));
                setTimeout(() => {
                    const startInput = document.querySelector('#create-auction-form [name="start"]');
                    if (startInput) startInput.value = dateStr;
                }, 100);
            }
        });

        logEvent('auction_calendar_opened', {});
    }

    function bindCalendarControls() {
        $('cal-prev').onclick = () => { state.calDate.setMonth(state.calDate.getMonth() - 1); renderCalendar(); };
        $('cal-next').onclick = () => { state.calDate.setMonth(state.calDate.getMonth() + 1); renderCalendar(); };
        $('cal-today').onclick = () => { state.calDate = new Date(); renderCalendar(); };
        document.querySelectorAll('.cal-view-btn').forEach(b => b.onclick = () => {
            state.calView = b.dataset.calView;
            document.querySelectorAll('.cal-view-btn').forEach(x => {
                x.classList.toggle('kt-btn-mono',  x.dataset.calView === state.calView);
                x.classList.toggle('kt-btn-ghost', x.dataset.calView !== state.calView);
            });
            toast(`${state.calView} view (mock)`, 'info');
        });
    }

    /* ================================================================
       A2 — CREATE WIZARD
       ================================================================ */
    function initWizard() {
        $('btn-create-auction').onclick = () => {
            state.wizardStep = 1;
            document.getElementById('create-auction-form')?.reset();
            showWizardStep(1);
            openModal($('create-auction-modal'));
        };
        $('wizard-back').onclick    = () => showWizardStep(state.wizardStep - 1);
        $('wizard-next').onclick    = advanceWizard;
        $('wizard-create').onclick  = createAuctionFromWizard;
        $('wizard-save-draft').onclick = () => createAuctionFromWizard(true);

        // staggered interval reveal
        document.querySelectorAll('[name="closing_style"]').forEach(r =>
            r.onchange = () => $('staggered-interval-wrap').classList.toggle('hidden', r.value !== 'staggered'));

        // sniper minutes reveal
        const sniperToggle = $('sniper-toggle');
        if (sniperToggle) sniperToggle.onchange = () =>
            $('sniper-minutes-wrap').classList.toggle('hidden', !sniperToggle.checked);

        // start price custom reveal
        document.querySelector('[name="start_price_mode"]').onchange = function () {
            $('start-price-custom-wrap').classList.toggle('hidden', this.value !== 'custom');
        };

        // participant mode reveal
        document.querySelectorAll('[name="participant_mode"]').forEach(r =>
            r.onchange = () =>
                $('participant-set-select').classList.toggle('hidden', r.value !== 'set'));

        // step pills click
        document.querySelectorAll('.wizard-pill').forEach(p =>
            p.onclick = () => showWizardStep(parseInt(p.dataset.pill)));
    }

    const wizardStepTitles = ['Basics','Schedule','Rules','Participants','Lot defaults','Assets','Summary'];

    function showWizardStep(step) {
        state.wizardStep = step;
        document.querySelectorAll('#create-auction-form .wizard-step').forEach(s =>
            s.classList.toggle('hidden', +s.dataset.step !== step));
        $('wizard-back').disabled = step === 1;
        $('wizard-next').classList.toggle('hidden', step === state.wizardTotal);
        $('wizard-create').classList.toggle('hidden', step !== state.wizardTotal);

        // pills
        document.querySelectorAll('.wizard-pill').forEach(p => {
            const n = +p.dataset.pill;
            p.classList.toggle('border-primary',     n === step);
            p.classList.toggle('bg-primary/10',      n === step);
            p.classList.toggle('text-primary',        n === step);
            p.classList.toggle('font-medium',         n === step);
            p.classList.toggle('border-border',       n !== step);
            p.classList.toggle('text-muted-foreground', n !== step);
        });
        $('wizard-step-label').textContent = `Step ${step} of ${state.wizardTotal}`;
        $('wizard-progress-bar').style.width = Math.round(((step - 1) / (state.wizardTotal - 1)) * 100) + '%';

        if (step === state.wizardTotal) generateWizardSummary();
    }

    function advanceWizard() {
        // validation for step 2 dates
        if (state.wizardStep === 2) {
            const form  = document.getElementById('create-auction-form');
            const start = form.querySelector('[name="start"]')?.value;
            const end   = form.querySelector('[name="end"]')?.value;
            if (!start || !end) { toast('Start and end date/time required', 'error'); return; }
            if (new Date(start) >= new Date(end)) { toast('Start must be before End', 'error'); return; }
            // clash check
            const newStart = new Date(start), newEnd = new Date(end);
            const clash = auctions.some(a => {
                const as = new Date(a.start), ae = new Date(a.end);
                return newStart < ae && newEnd > as;
            });
            $('schedule-clash-warning')?.classList.toggle('hidden', !clash);
        }
        if (state.wizardStep < state.wizardTotal) showWizardStep(state.wizardStep + 1);
    }

    function generateWizardSummary() {
        const fd = new FormData(document.getElementById('create-auction-form'));
        const rows = [];
        fd.forEach((val, key) => { if (val && typeof val === 'string') rows.push(`<div><strong>${key}:</strong> ${val}</div>`); });
        $('auction-wizard-summary').innerHTML = rows.join('') || '<div class="text-muted-foreground">No data</div>';
    }

    function createAuctionFromWizard(asDraft = false) {
        const form = document.getElementById('create-auction-form');
        const fd   = new FormData(form);
        const name = fd.get('name')?.trim();
        if (!name) { toast('Auction name is required', 'error'); return; }

        const start = fd.get('start'), end = fd.get('end');
        if (!asDraft && (!start || !end)) { toast('Schedule required before creating', 'error'); return; }

        const a = {
            id:              genId('AUC'),
            name,
            start,
            end,
            status:          asDraft ? 'Planned' : 'Planned',
            visibility:      fd.get('visibility') || 'Public',
            owner:           'Me',
            description:     fd.get('description') || '',
            cohort_tag:      fd.get('cohort_tag')  || '',
            closing_style:   fd.get('closing_style')    || 'single',
            proxy_bidding:   !!fd.get('proxy_bidding'),
            sniper_protection: !!fd.get('sniper_protection'),
            sniper_minutes:  parseInt(fd.get('sniper_minutes')) || 2,
            auto_accept:     !!fd.get('auto_accept'),
            bin_precedence:  !!fd.get('bin_precedence'),
            start_price_mode: fd.get('start_price_mode') || 'zero',
            increment_schema: fd.get('increment_schema') || 'standard',
            participant_mode: fd.get('participant_mode') || 'all',
            gate_kyc:        !!fd.get('gate_kyc'),
            gate_card:       !!fd.get('gate_card'),
            allow_exchange:  !!fd.get('allow_exchange'),
            lots:            [],
            participants:    [],
            messages:        [],
            activity:        [{ ts: new Date().toISOString(), actor: 'Me', event: 'auction_created', detail: asDraft ? 'Draft saved' : 'Auction created' }],
            exchange_proposals: [],
        };
        auctions.unshift(a);
        logEvent('auction_created', { id: a.id, name: a.name });
        toast(asDraft ? `Draft saved: ${a.id}` : `Auction created: ${a.id}`);
        closeModal($('create-auction-modal'));
        form.reset(); showWizardStep(1);
        renderTable(); renderCalendar();
    }

    /* ================================================================
       A3 — AUCTION DETAIL
       ================================================================ */
    function openAuctionDetail(id) {
        const a = auctions.find(x => x.id === id);
        if (!a) return;
        state.activeAuction = a;
        populateAuctionDetail(a);
        switchAucTab('overview');
        openModal($('auction-detail-modal'));
    }

    function populateAuctionDetail(a) {
        $('auc-detail-title').textContent = `${a.id} — ${a.name}`;
        $('auc-detail-sub').textContent   = `${a.status} · ${fmtDT(a.start)} → ${fmtDT(a.end)} · Owner: ${a.owner}`;

        // badges
        const badges = $('auc-detail-badges');
        badges.innerHTML = '';
        if (a.visibility === 'Private') badges.innerHTML += `<span class="kt-badge kt-badge-sm kt-badge-outline">Private</span>`;
        if (a.sniper_protection)        badges.innerHTML += `<span class="kt-badge kt-badge-sm kt-badge-info">Sniper ✓</span>`;
        if (a.proxy_bidding)            badges.innerHTML += `<span class="kt-badge kt-badge-sm kt-badge-info">Proxy ✓</span>`;

        // KPIs
        $('auc-kpi-lots').textContent    = a.lots.length;
        $('auc-kpi-live').textContent    = a.lots.filter(l => l.state === 'Live').length;
        $('auc-kpi-ended').textContent   = a.lots.filter(l => l.state === 'Ended').length;
        $('auc-kpi-reserve').textContent = `${a.lots.filter(l => l.reserve_met).length}/${a.lots.length}`;
        $('auc-kpi-bidders').textContent = a.participants.filter(p => p.status === 'Accepted').length;

        // overview panel
        const checklist = $('overview-checklist');
        if (checklist) {
            const items = [
                { label: 'At least one lot added', pass: a.lots.length > 0 },
                { label: 'Schedule set',            pass: !!a.start && !!a.end },
                { label: 'Participants configured', pass: a.participants.length > 0 || a.participant_mode === 'all' },
                { label: 'Rules configured',        pass: true },
            ];
            checklist.innerHTML = items.map(i =>
                `<div class="flex items-center gap-2"><span class="${i.pass ? 'text-green-600' : 'text-red-500'}">${i.pass ? '✔' : '✖'}</span>${i.label}</div>`
            ).join('');
        }

        const alerts = $('overview-alerts');
        if (alerts) {
            const warn = [];
            if (!a.lots.length)        warn.push('No lots added');
            if (!a.participants.length && a.participant_mode !== 'all') warn.push('No participants invited');
            alerts.innerHTML = warn.length
                ? warn.map(w => `<div class="text-yellow-700">⚠ ${w}</div>`).join('')
                : '<div class="text-green-600">No alerts</div>';
        }

        if ($('overview-schedule')) $('overview-schedule').innerHTML =
            `${fmtDT(a.start)}<br><span class="text-muted-foreground">→ ${fmtDT(a.end)}</span>`;

        if ($('overview-rules')) $('overview-rules').innerHTML = `
            <div>Proxy bidding: <strong>${a.proxy_bidding ? 'On' : 'Off'}</strong></div>
            <div>Sniper protection: <strong>${a.sniper_protection ? `On (${a.sniper_minutes}m)` : 'Off'}</strong></div>
            <div>Auto-accept: <strong>${a.auto_accept ? 'On' : 'Off'}</strong></div>
            <div>BIN precedence: <strong>${a.bin_precedence ? 'On' : 'Off'}</strong></div>
            <div>Increments: <strong>${a.increment_schema}</strong></div>`;

        if ($('overview-participants')) $('overview-participants').textContent =
            `${a.participants.length} invited · Mode: ${a.participant_mode}`;

        populateLots(a);
        populateParticipantsTab(a);
        populateRulesPanel(a);
        populateActivityPanel(a);
        populateBidFeedFilter(a);
    }

    function populateLots(a) {
        const tbody = $('lots-tbody');
        if (!tbody) return;
        if (!a.lots.length) {
            tbody.innerHTML = `<tr><td colspan="9" class="p-4 text-center text-xs text-muted-foreground">No lots yet. Use "Add from Listings".</td></tr>`;
            return;
        }
        tbody.innerHTML = a.lots.map(l => `
            <tr>
                <td class="p-2"><input class="lot-cb form-checkbox" data-lot-id="${l.id}"></td>
                <td class="p-2 text-xs font-mono">${l.lot_num}</td>
                <td class="p-2 text-xs">${l.listing}</td>
                <td class="p-2 text-xs">${l.vehicle}</td>
                <td class="p-2 text-xs text-right">${l.start_price ? fmt(l.start_price) : '£0'}</td>
                <td class="p-2 text-xs">${l.reserve ? '<span class="text-green-600">✔</span>' : '<span class="text-muted-foreground">✖</span>'}</td>
                <td class="p-2 text-xs">${l.bin_enabled ? '<span class="kt-badge kt-badge-sm kt-badge-outline">BIN</span>' : 'Off'}</td>
                <td class="p-2 text-xs"><span class="kt-badge kt-badge-sm kt-badge-outline">${l.state}</span></td>
                <td class="p-2 text-xs">
                    <button class="kt-btn kt-btn-ghost kt-btn-sm" data-lot-act="open"     data-lot-id="${l.id}">Open</button>
                    <button class="kt-btn kt-btn-ghost kt-btn-sm" data-lot-act="withdraw" data-lot-id="${l.id}">Withdraw</button>
                    <button class="kt-btn kt-btn-outline kt-btn-sm" data-lot-act="rerun"  data-lot-id="${l.id}">Re-run</button>
                    ${a.allow_exchange ? `<button class="kt-btn kt-btn-ghost kt-btn-sm" data-lot-act="exchange" data-lot-id="${l.id}">Exchange</button>` : ''}
                </td>
            </tr>`).join('');

        tbody.querySelectorAll('[data-lot-act]').forEach(b => {
            b.onclick = () => {
                const lot = a.lots.find(l => l.id === b.dataset.lotId);
                if (!lot) return;
                const act = b.dataset.lotAct;
                if (act === 'open')     openLotDetail(a, lot);
                if (act === 'withdraw') { lot.state = 'Withdrawn'; logEvent('lot_withdrawn', { lotId: lot.id }); toast(`Lot ${lot.lot_num} withdrawn`); populateLots(a); }
                if (act === 'rerun')    { toast(`Re-run scheduled for lot ${lot.lot_num}`, 'info'); logEvent('lot_rerun_created', { lotId: lot.id }); }
                if (act === 'exchange') openExchangeProposal(a, lot);
            };
        });
    }

    function populateParticipantsTab(a) {
        const tbody = $('participants-tbody');
        if (!tbody) return;
        if (!a.participants.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="p-4 text-center text-xs text-muted-foreground">No participants invited.</td></tr>`;
            return;
        }
        tbody.innerHTML = a.participants.map(p => `
            <tr>
                <td class="p-2 text-sm font-medium">${p.name}</td>
                <td class="p-2 text-xs">
                    <span class="kt-badge kt-badge-sm ${p.kyb === 'Verified' ? 'kt-badge-success' : 'kt-badge-warning'}">${p.kyb}</span>
                </td>
                <td class="p-2 text-xs">${p.card ? '<span class="text-green-600">✔</span>' : '<span class="text-red-500">✖</span>'}</td>
                <td class="p-2 text-xs">
                    <span class="kt-badge kt-badge-sm ${p.status === 'Accepted' ? 'kt-badge-success' : p.status === 'Declined' ? 'kt-badge-destructive' : 'kt-badge-outline'}">${p.status}</span>
                </td>
                <td class="p-2 text-xs">${p.last_seen}</td>
                <td class="p-2 text-xs">
                    <button class="kt-btn kt-btn-ghost kt-btn-sm" data-p-act="resend" data-p-name="${p.name}">Resend</button>
                    <button class="kt-btn kt-btn-outline kt-btn-sm" data-p-act="revoke" data-p-name="${p.name}">Revoke</button>
                </td>
            </tr>`).join('');

        tbody.querySelectorAll('[data-p-act]').forEach(b => b.onclick = () => {
            if (b.dataset.pAct === 'resend') { toast(`Invite resent to ${b.dataset.pName}`); logEvent('auction_invite_sent', { vendor: b.dataset.pName }); }
            if (b.dataset.pAct === 'revoke') {
                a.participants = a.participants.filter(p => p.name !== b.dataset.pName);
                toast(`${b.dataset.pName} revoked`); populateParticipantsTab(a);
            }
        });
    }

    function populateRulesPanel(a) {
        const el = $('panel-rules-content');
        if (!el) return;
        el.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="border border-border rounded p-3 space-y-2">
                    <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Bidding</div>
                    <div class="flex justify-between text-sm"><span>Proxy bidding</span><strong>${a.proxy_bidding ? 'On' : 'Off'}</strong></div>
                    <div class="flex justify-between text-sm"><span>Sniper protection</span><strong>${a.sniper_protection ? `On (${a.sniper_minutes}m)` : 'Off'}</strong></div>
                </div>
                <div class="border border-border rounded p-3 space-y-2">
                    <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Reserve</div>
                    <div class="flex justify-between text-sm"><span>Auto-accept ≥ Reserve</span><strong>${a.auto_accept ? 'On' : 'Off'}</strong></div>
                    <div class="flex justify-between text-sm"><span>BIN precedence</span><strong>${a.bin_precedence ? 'On' : 'Off'}</strong></div>
                    <div class="flex justify-between text-sm"><span>Start price</span><strong>${a.start_price_mode}</strong></div>
                </div>
            </div>
            <div class="border border-border rounded p-3 space-y-2">
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Bid increment schema: <strong class="font-normal capitalize">${a.increment_schema}</strong></div>
                <div class="text-xs text-muted-foreground bg-muted/30 p-2 rounded">
                    ${a.increment_schema === 'standard'
                        ? 'Band 0–£10k: £250 · £10k–£25k: £500 · £25k+: £1,000'
                        : a.increment_schema === 'premium'
                        ? 'Band 0–£10k: £500 · £10k–£50k: £1,000 · £50k+: £2,500'
                        : 'Custom schema'}
                </div>
            </div>`;
    }

    function populateActivityPanel(a) {
        const el = $('panel-activity');
        if (!el) return;
        el.innerHTML = (a.activity || []).slice().reverse().map(ev => `
            <div class="flex gap-3 border-b border-border pb-2">
                <div class="text-xs text-muted-foreground w-28 shrink-0">${fmtDT(ev.ts)}</div>
                <div><div class="text-xs font-medium">${ev.event}</div>
                     <div class="text-xs text-muted-foreground">${ev.detail} — ${ev.actor}</div></div>
            </div>`).join('') || '<div class="text-xs text-muted-foreground">No activity</div>';
    }

    function populateBidFeedFilter(a) {
        const sel = $('bid-feed-lot-filter');
        if (!sel) return;
        sel.innerHTML = `<option value="">All lots</option>` +
            a.lots.map(l => `<option value="${l.id}">Lot ${l.lot_num} — ${l.vehicle}</option>`).join('');
    }

    /* ── Detail header buttons ── */
    function bindDetailActions() {
        $('auc-btn-publish').onclick = () => {
            if (!state.activeAuction) return;
            openPublishConfirm(state.activeAuction.id);
        };
        $('auc-btn-start').onclick = () => {
            if (!state.activeAuction) return;
            const a = state.activeAuction;
            if (!a.lots.length) { toast('Cannot start: no lots', 'error'); return; }
            a.status = 'Live'; a.lots.forEach(l => { if (l.state === 'Ready') l.state = 'Live'; });
            logEvent('auction_started', { id: a.id });
            toast(`Auction ${a.id} started`); populateAuctionDetail(a); renderTable();
        };
        $('auc-btn-pause').onclick = () => {
            if (!state.activeAuction) return;
            state.activeAuction.status = state.activeAuction.status === 'Paused' ? 'Live' : 'Paused';
            logEvent('auction_paused', { id: state.activeAuction.id });
            toast(`${state.activeAuction.id} ${state.activeAuction.status}`);
            populateAuctionDetail(state.activeAuction); renderTable();
        };
        $('auc-btn-extend').onclick = () => {
            const min = parseInt($('auc-extend-min').value) || 5;
            if (!state.activeAuction) return;
            const a = state.activeAuction;
            const newEnd = new Date(new Date(a.end).getTime() + min * 60000);
            a.end = newEnd.toISOString().slice(0, 16);
            logEvent('auction_extended', { id: a.id, minutes: min });
            toast(`${a.id} extended by ${min} min`);
            populateAuctionDetail(a); renderTable();
        };
        $('auc-btn-add-lots').onclick   = () => { if (!state.activeAuction) return; addMockLot(state.activeAuction); };
        $('auc-btn-invite').onclick     = () => openModal($('participants-modal'));
        $('auc-btn-announce').onclick   = () => { switchAucTab('messages'); };
        $('auc-more-toggle').onclick    = () => $('auc-more-menu').classList.toggle('hidden');
        document.querySelectorAll('[data-auc-action]').forEach(b => b.onclick = () => {
            $('auc-more-menu').classList.add('hidden');
            handleDetailMoreAction(b.dataset.aucAction);
        });

        $('btn-send-announcement').onclick = () => {
            const txt = $('announcement-text').value.trim();
            if (!txt || !state.activeAuction) return toast('Enter announcement text', 'warning');
            state.activeAuction.messages.push({ ts: new Date().toISOString(), text: txt });
            $('announcement-text').value = '';
            renderMessagesLog(state.activeAuction);
            logEvent('announcement_sent', { id: state.activeAuction.id, text: txt });
            toast('Announcement sent');
        };

        $('btn-add-from-listings').onclick = () => { if (state.activeAuction) addMockLot(state.activeAuction); };
        $('btn-open-live-console').onclick  = () => { if (state.activeAuction) openLiveConsole(state.activeAuction.id); };
    }

    function renderMessagesLog(a) {
        const el = $('messages-log');
        if (!el) return;
        el.innerHTML = (a.messages || []).slice().reverse().map(m => `
            <div class="p-3 text-sm">
                <div class="text-xs text-muted-foreground mb-1">${fmtDT(m.ts)}</div>
                <div>${m.text}</div>
            </div>`).join('') || '<div class="p-3 text-xs text-muted-foreground text-center">No messages</div>';
    }

    function handleDetailMoreAction(act) {
        if (!state.activeAuction) return;
        if (act === 'live-console') openLiveConsole(state.activeAuction.id);
        if (act === 'duplicate')    duplicateAuction(state.activeAuction.id);
        if (act === 'post-auction') { openModal($('post-auction-modal')); populatePostAuction(state.activeAuction); }
        if (act === 'archive') {
            state.activeAuction.status = 'Archived';
            toast(`${state.activeAuction.id} archived`);
            closeModal($('auction-detail-modal'));
            renderTable();
        }
    }

    function addMockLot(a) {
        const num = String(a.lots.length + 1).padStart(3, '0');
        const mock = { id: 'L' + genId(''), lot_num: num, listing: 'LST-' + Math.floor(Math.random()*9000+1000),
                       vehicle: 'Mock Vehicle 20' + Math.floor(Math.random()*5+20),
                       start_price: 0, reserve: true, bin_enabled: false, state: 'Ready',
                       current_bid: 0, reserve_met: false, bidders: 0, bids: [] };
        a.lots.push(mock);
        logEvent('lot_added', { auctionId: a.id, lotId: mock.id });
        toast(`Lot ${num} added`);
        populateLots(a); populateAuctionDetail(a);
    }

    /* ── Auction detail tab switching ── */
    function bindAucTabs() {
        document.querySelectorAll('.auc-tab-btn').forEach(b =>
            b.onclick = () => switchAucTab(b.dataset.tab));
    }

    function switchAucTab(tab) {
        document.querySelectorAll('.auc-tab-btn').forEach(b => {
            b.classList.toggle('border-primary',         b.dataset.tab === tab);
            b.classList.toggle('font-medium',            b.dataset.tab === tab);
            b.classList.toggle('text-foreground',        b.dataset.tab === tab);
            b.classList.toggle('border-transparent',     b.dataset.tab !== tab);
            b.classList.toggle('text-muted-foreground',  b.dataset.tab !== tab);
        });
        document.querySelectorAll('.auc-panel').forEach(p =>
            p.classList.toggle('hidden', p.dataset.panel !== tab));
    }

    /* ================================================================
       A5 — LIVE CONSOLE
       ================================================================ */
    function openLiveConsole(auctionId) {
        const a = auctions.find(x => x.id === auctionId);
        if (!a) return;
        state.activeAuction = a;
        $('console-title').textContent = `Live Console — ${a.id} ${a.name}`;
        $('console-sub').textContent   = `${fmtDT(a.start)} → ${fmtDT(a.end)}`;
        renderConsoleLotsList(a);
        if (a.lots.length) followLot(a, a.lots[0]);
        openModal($('live-console-modal'));
        startCountdownTimer();
    }

    function renderConsoleLotsList(a) {
        const el = $('console-lots-list');
        if (!el) return;
        el.innerHTML = a.lots.map(l => `
            <div class="p-3 cursor-pointer hover:bg-muted/20 transition-colors ${state.activeLot?.id === l.id ? 'bg-primary/5 border-l-2 border-primary' : ''}"
                 data-follow-lot="${l.id}">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <div class="text-xs font-semibold">${l.lot_num} · ${l.vehicle.slice(0,22)}…</div>
                        <div class="text-[11px] text-muted-foreground mt-0.5">Top: ${fmt(l.current_bid)}</div>
                    </div>
                    <div class="text-xs">
                        <span class="kt-badge kt-badge-sm ${l.state === 'Live' ? 'kt-badge-success' : 'kt-badge-outline'}">${l.state}</span>
                    </div>
                </div>
            </div>`).join('') || '<div class="p-3 text-xs text-muted-foreground">No lots</div>';

        el.querySelectorAll('[data-follow-lot]').forEach(b => b.onclick = () => {
            const lot = a.lots.find(l => l.id === b.dataset.followLot);
            if (lot) followLot(a, lot);
        });
    }

    function followLot(a, lot) {
        state.activeLot = lot;
        $('console-lot-title').textContent    = `Lot ${lot.lot_num} — ${lot.vehicle}`;
        $('console-lot-meta').textContent     = `Listing: ${lot.listing} · State: ${lot.state}`;
        $('console-current-bid').textContent  = fmt(lot.current_bid);
        $('console-next-min').textContent     = fmt(calculateNextMin(a, lot));
        $('console-reserve-status').innerHTML = lot.reserve_met
            ? '<span class="text-green-600 font-semibold">Met</span>'
            : '<span class="text-yellow-600">Not met</span>';
        $('console-bidders').textContent = lot.bidders;
        renderConsoleBidFeed(lot);
        renderConsoleLotsList(a); // re-highlight
    }

    function calculateNextMin(a, lot) {
        if (!lot.current_bid) return 0;
        const bands = a.increment_schema === 'premium'
            ? [[50000, 2500],[10000, 1000],[0, 500]]
            : [[25000, 1000],[10000, 500],[0, 250]];
        const band = bands.find(([threshold]) => lot.current_bid >= threshold);
        return lot.current_bid + (band ? band[1] : 250);
    }

    function renderConsoleBidFeed(lot) {
        const el = $('console-bid-feed');
        if (!el) return;
        const showProxy = $('console-proxy-filter')?.checked;
        const bids = showProxy ? lot.bids.filter(b => b.proxy) : lot.bids;
        el.innerHTML = bids.length
            ? bids.map(b => `
                <div class="flex items-center gap-3 p-2 ${b.proxy ? 'bg-blue-50/5' : ''}">
                    <span class="text-muted-foreground w-16 shrink-0">${b.ts}</span>
                    <span class="flex-1">${b.vendor}</span>
                    <span class="font-semibold">${fmt(b.amount)}</span>
                    ${b.proxy ? '<span class="kt-badge kt-badge-sm kt-badge-info">proxy</span>' : ''}
                </div>`).join('')
            : '<div class="p-3 text-muted-foreground text-center">No bids yet</div>';
    }

    function startCountdownTimer() {
        if (state.liveConsoleInterval) clearInterval(state.liveConsoleInterval);
        state.liveConsoleInterval = setInterval(() => {
            if (!state.activeAuction) return;
            const end = new Date(state.activeAuction.end);
            const diff = Math.max(0, Math.floor((end - Date.now()) / 1000));
            const h = Math.floor(diff / 3600), m = Math.floor((diff % 3600) / 60), s = diff % 60;
            const el = $('console-countdown');
            if (el) el.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        }, 1000);
    }

    function bindConsoleControls() {
        $('console-btn-extend').onclick = () => {
            const min = parseInt($('console-extend-min').value) || 2;
            if (!state.activeAuction || !state.activeLot) return;
            const a = state.activeAuction;
            const newEnd = new Date(new Date(a.end).getTime() + min * 60000);
            a.end = newEnd.toISOString().slice(0, 16);
            $('sniper-indicator')?.classList.remove('hidden');
            logEvent('lot_extended', { lotId: state.activeLot.id, minutes: min });
            toast(`Lot ${state.activeLot.lot_num} extended by ${min} min`);
        };
        $('console-btn-pause-lot').onclick = () => {
            if (!state.activeLot) return;
            state.activeLot.state = state.activeLot.state === 'Paused' ? 'Live' : 'Paused';
            logEvent('lot_paused', { lotId: state.activeLot.id });
            toast(`Lot ${state.activeLot.lot_num} ${state.activeLot.state}`);
            followLot(state.activeAuction, state.activeLot);
        };
        $('console-btn-end-lot').onclick = () => {
            if (!state.activeLot || !confirm('Force-end this lot?')) return;
            state.activeLot.state = 'Ended';
            logEvent('lot_force_ended', { lotId: state.activeLot.id });
            toast(`Lot ${state.activeLot.lot_num} ended`);
            followLot(state.activeAuction, state.activeLot);
            renderConsoleLotsList(state.activeAuction);
        };
        $('console-btn-rerun-lot').onclick = () => { toast('Re-run scheduled', 'info'); logEvent('lot_rerun_created', { lotId: state.activeLot?.id }); };
        $('console-btn-announce-lot').onclick = () => toast('Announcement sent to participants', 'info');
        $('console-btn-pause-all').onclick = () => {
            if (!state.activeAuction) return;
            state.activeAuction.status = 'Paused'; state.activeAuction.lots.forEach(l => { if (l.state === 'Live') l.state = 'Paused'; });
            toast('All lots paused'); renderConsoleLotsList(state.activeAuction);
        };
        $('console-proxy-filter').onchange = () => { if (state.activeLot) renderConsoleBidFeed(state.activeLot); };
    }

    /* ================================================================
       A6 — LOT DETAIL
       ================================================================ */
    function openLotDetail(a, lot) {
        state.activeLot = lot;
        $('lot-detail-title').textContent = `Lot ${lot.lot_num} — ${a.name}`;
        $('lot-detail-sub').textContent   = `${lot.listing} · ${lot.vehicle} · ${lot.state}`;

        // overview
        if ($('lot-vehicle-summary')) $('lot-vehicle-summary').innerHTML = `
            <div class="text-xs text-muted-foreground mb-1">Vehicle</div>
            <div class="font-medium">${lot.vehicle}</div>
            <div class="text-xs text-muted-foreground mt-1">Listing: ${lot.listing}</div>`;
        if ($('lot-pricing-summary')) $('lot-pricing-summary').innerHTML = `
            <div class="text-xs text-muted-foreground mb-1">Pricing</div>
            <div>Start price: <strong>${fmt(lot.start_price) || '£0'}</strong></div>
            <div>Current bid: <strong>${fmt(lot.current_bid)}</strong></div>
            <div>Reserve met: <strong class="${lot.reserve_met ? 'text-green-600' : 'text-yellow-600'}">${lot.reserve_met ? 'Yes' : 'No'}</strong></div>`;

        // bid feed
        const feedEl = $('lot-bid-feed');
        if (feedEl) feedEl.innerHTML = lot.bids.length
            ? lot.bids.map(b => `
                <div class="flex gap-3 p-2 ${b.proxy ? 'bg-blue-50/5' : ''}">
                    <span class="text-muted-foreground w-16 shrink-0">${b.ts}</span>
                    <span class="flex-1">${b.vendor}</span>
                    <span class="font-semibold">${fmt(b.amount)}</span>
                    ${b.proxy ? '<span class="kt-badge kt-badge-sm kt-badge-info">auto/proxy</span>' : ''}
                </div>`).join('')
            : '<div class="p-3 text-muted-foreground text-center">No bids</div>';

        // rules
        if ($('lot-rules-content')) $('lot-rules-content').innerHTML = `
            <div class="text-xs text-muted-foreground mb-1">Lot-level overrides vs auction defaults</div>
            <div>BIN enabled: <strong>${lot.bin_enabled ? 'Yes' : 'No — follows auction'}</strong></div>
            <div class="text-xs text-muted-foreground mt-2">Note: BIN is disabled once first valid bid exists.</div>`;

        switchLotTab('overview');
        openModal($('lot-detail-modal'));

        // tab buttons
        document.querySelectorAll('.lot-tab-btn').forEach(b => b.onclick = () => switchLotTab(b.dataset.tab));

        // action buttons
        $('lot-btn-extend').onclick  = () => { toast(`Lot ${lot.lot_num} extended`, 'info'); logEvent('lot_extended', { lotId: lot.id }); };
        $('lot-btn-withdraw').onclick = () => { lot.state = 'Withdrawn'; toast(`Lot ${lot.lot_num} withdrawn`); logEvent('lot_withdrawn', { lotId: lot.id }); };
        $('lot-btn-rerun').onclick    = () => { toast('Re-run scheduled', 'info'); logEvent('lot_rerun_created', { lotId: lot.id }); };
        $('lot-btn-announce').onclick = () => toast('Announcement sent', 'info');
        $('lot-btn-save-notes').onclick = () => toast('Notes saved');
    }

    function switchLotTab(tab) {
        document.querySelectorAll('.lot-tab-btn').forEach(b => {
            b.classList.toggle('border-primary',         b.dataset.tab === tab);
            b.classList.toggle('font-medium',            b.dataset.tab === tab);
            b.classList.toggle('text-foreground',        b.dataset.tab === tab);
            b.classList.toggle('border-transparent',     b.dataset.tab !== tab);
            b.classList.toggle('text-muted-foreground',  b.dataset.tab !== tab);
        });
        document.querySelectorAll('.lot-panel').forEach(p =>
            p.classList.toggle('hidden', p.dataset.panel !== tab));
    }

    /* ================================================================
       A8 — POST-AUCTION
       ================================================================ */
    function populatePostAuction(a) {
        const tbody = $('post-auction-tbody');
        if (!tbody) return;
        const ended = a.lots.filter(l => l.state === 'Ended' || l.current_bid > 0);
        if (!ended.length) { tbody.innerHTML = `<tr><td colspan="6" class="p-4 text-center text-xs text-muted-foreground">No ended lots yet</td></tr>`; return; }
        tbody.innerHTML = ended.map(l => {
            const outcome = l.reserve_met ? 'Deal Pending' : 'Unsold';
            return `<tr>
                <td class="p-2 text-xs">${l.lot_num}</td>
                <td class="p-2 text-xs">${l.vehicle}</td>
                <td class="p-2 text-xs text-right font-medium">${fmt(l.current_bid)}</td>
                <td class="p-2 text-xs">
                    <span class="${l.reserve_met ? 'text-green-600' : 'text-yellow-600'} font-medium">${l.reserve_met ? 'Yes' : 'No'}</span>
                </td>
                <td class="p-2 text-xs">
                    <span class="kt-badge kt-badge-sm ${l.reserve_met ? 'kt-badge-success' : 'kt-badge-outline'}">${outcome}</span>
                </td>
                <td class="p-2 text-xs">
                    ${l.reserve_met
                        ? `<button class="kt-btn kt-btn-mono kt-btn-sm" data-pa-act="open-deal" data-lot-id="${l.id}">Open deal</button>
                           <button class="kt-btn kt-btn-ghost kt-btn-sm ml-1" data-pa-act="objection" data-lot-id="${l.id}">7-day objection</button>`
                        : `<button class="kt-btn kt-btn-ghost kt-btn-sm" data-pa-act="rerun" data-lot-id="${l.id}">Re-run</button>
                           <button class="kt-btn kt-btn-ghost kt-btn-sm ml-1" data-pa-act="offer-highest" data-lot-id="${l.id}">Offer highest</button>
                           <button class="kt-btn kt-btn-outline kt-btn-sm ml-1" data-pa-act="switch-bin" data-lot-id="${l.id}">Switch BIN/Offer</button>`}
                </td>
            </tr>`;
        }).join('');

        tbody.querySelectorAll('[data-pa-act]').forEach(b => b.onclick = () => {
            const lot = a.lots.find(l => l.id === b.dataset.lotId);
            const act = b.dataset.paAct;
            if (act === 'open-deal')    { logEvent('deal_opened_from_auction', { lotId: lot.id }); toast(`Deal opened for lot ${lot.lot_num}`); }
            if (act === 'objection')    { toast(`7-day objection window noted for lot ${lot.lot_num}`, 'info'); }
            if (act === 'rerun')        { logEvent('lot_rerun_created', { lotId: lot.id }); toast(`Re-run scheduled for lot ${lot.lot_num}`); }
            if (act === 'offer-highest'){ logEvent('post_auction_offer_sent', { lotId: lot.id }); toast(`Offer sent to highest bidder for lot ${lot.lot_num}`); }
            if (act === 'switch-bin')   { if (lot) lot.bin_enabled = true; toast(`Lot ${lot.lot_num} switched to BIN/Offer`); }
        });
    }

    /* ================================================================
       A9 — EXCHANGE PROPOSALS
       ================================================================ */
    function openExchangeProposal(a, lot) {
        const ep = a.exchange_proposals.find(e => e.lot_id === lot.id && e.active);
        const card     = $('ep-incoming-card');
        const noActive = $('ep-no-proposal');
        if (ep) {
            card?.classList.remove('hidden'); noActive?.classList.add('hidden');
            $('ep-offered-by').textContent      = ep.offered_by;
            $('ep-offered-vehicle').textContent = ep.offered_vehicle;
            $('ep-cash-diff').textContent       = ep.cash_diff ? fmt(ep.cash_diff) : 'Even swap';
            $('ep-expiry').textContent          = fmtDT(ep.expiry);
            $('ep-notes').textContent           = ep.notes || '—';
        } else {
            card?.classList.add('hidden'); noActive?.classList.remove('hidden');
        }
        openModal($('exchange-proposal-modal'));
        bindExchangeProposalActions(a, lot, ep);
    }

    function bindExchangeProposalActions(a, lot, ep) {
        $('ep-btn-accept').onclick = () => {
            if (!ep) return;
            ep.active = false;
            logEvent('exchange_proposal_accepted', { lotId: lot.id, proposalId: ep.id });
            toast(`Exchange proposal accepted — draft deal created`);
            closeModal($('exchange-proposal-modal'));
        };
        $('ep-btn-decline').onclick = () => {
            if (!ep) return; ep.active = false;
            toast('Proposal declined'); closeModal($('exchange-proposal-modal'));
        };
        $('ep-btn-counter').onclick = () => toast('Counter-proposal (mock)', 'info');
        $('ep-btn-submit').onclick  = () => {
            const existingActive = a.exchange_proposals.find(e => e.lot_id === lot.id && e.active);
            if (existingActive) { toast('Only 1 active proposal per listing allowed', 'error'); return; }
            const newEp = {
                id: 'EP-' + genId(''),
                lot_id: lot.id,
                offered_by: 'Me',
                offered_vehicle: document.querySelector('[name="ep_vehicle"]')?.value || '—',
                cash_diff: parseFloat(document.querySelector('[name="ep_cash_diff"]')?.value) || 0,
                expiry: document.querySelector('[name="ep_expiry"]')?.value || '',
                notes: document.querySelector('[name="ep_notes"]')?.value || '',
                active: true,
            };
            a.exchange_proposals.push(newEp);
            logEvent('exchange_proposal_created', { lotId: lot.id });
            toast('Exchange proposal submitted');
            closeModal($('exchange-proposal-modal'));
        };
    }

    /* ================================================================
       PUBLISH CONFIRM
       ================================================================ */
    function openPublishConfirm(id) {
        state.publishTarget = id;
        const a = auctions.find(x => x.id === id);
        const checklist = $('publish-checklist');
        if (checklist && a) {
            const items = [
                { label: 'Has at least one lot', pass: a.lots.length > 0 },
                { label: 'Schedule set',          pass: !!a.start && !!a.end },
                { label: 'Participants configured', pass: a.participant_mode === 'all' || a.participants.length > 0 },
            ];
            checklist.innerHTML = items.map(i =>
                `<div class="flex items-center gap-2 text-sm"><span class="${i.pass ? 'text-green-600' : 'text-red-500'}">${i.pass ? '✔' : '✖'}</span>${i.label}</div>`
            ).join('');
        }
        openModal($('publish-confirm-modal'));
    }

    function bindPublishConfirm() {
        $('confirm-publish-btn').onclick = () => {
            const a = auctions.find(x => x.id === state.publishTarget);
            if (!a) return closeModal($('publish-confirm-modal'));
            if (!a.lots.length) { toast('Cannot publish: no lots', 'error'); return; }
            a.status = 'Published';
            logEvent('auction_published', { id: a.id });
            toast(`${a.id} published`);
            closeModal($('publish-confirm-modal'));
            if (state.activeAuction?.id === a.id) populateAuctionDetail(a);
            renderTable();
        };
    }

    /* ================================================================
       A10 — NOTIFICATIONS & AUTOMATIONS
       ================================================================ */
    function bindNotifications() {
        $('btn-save-notifications').onclick = () => {
            logEvent('automation_settings_saved', {});
            toast('Notification settings saved');
            closeModal($('notifications-modal'));
        };
    }

    /* ================================================================
       DUPLICATE
       ================================================================ */
    function duplicateAuction(id, silent = false) {
        const src = auctions.find(x => x.id === id);
        if (!src) return;
        const dup = JSON.parse(JSON.stringify(src));
        dup.id = genId('AUC');
        dup.name = src.name + ' (Copy)';
        dup.status = 'Planned';
        dup.lots = dup.lots.map(l => ({ ...l, id: 'L' + genId(''), state: 'Ready', current_bid: 0, reserve_met: false, bids: [] }));
        dup.activity = [{ ts: new Date().toISOString(), actor: 'Me', event: 'auction_created', detail: `Duplicated from ${id}` }];
        auctions.unshift(dup);
        if (!silent) toast(`Duplicated: ${dup.id}`);
        logEvent('auction_created', { id: dup.id, duplicatedFrom: id });
        renderTable();
    }

    /* ================================================================
       MODAL HELPERS
       ================================================================ */
    function openModal(el) {
        if (!el) return;
        el.classList.remove('hidden'); el.classList.add('flex');
        document.body.style.overflow = 'hidden';
        const d = el.querySelector('[role="dialog"]');
        if (d) requestAnimationFrame(() => { d.style.opacity = '1'; d.style.transform = 'scale(1)'; });
    }

    function closeModal(el) {
        if (!el) return;
        el.classList.add('hidden'); el.classList.remove('flex');
        document.body.style.overflow = '';
        const d = el.querySelector('[role="dialog"]');
        if (d) { d.style.opacity = ''; d.style.transform = ''; }
        // stop live console timer if console modal closed
        if (el.id === 'live-console-modal' && state.liveConsoleInterval) {
            clearInterval(state.liveConsoleInterval); state.liveConsoleInterval = null;
        }
    }

    function bindModalClose() {
        document.querySelectorAll('[data-modal-close]').forEach(e =>
            e.onclick = () => closeModal(e.closest('.fixed')));
        document.querySelectorAll('[data-modal-backdrop]').forEach(e =>
            e.onclick = () => closeModal(e.closest('.fixed')));
        document.addEventListener('keydown', ev => {
            if (ev.key === 'Escape') {
                const open = document.querySelector('.fixed.z-50.flex');
                if (open) closeModal(open);
            }
        });
        // close dropdown menus on outside click
        document.addEventListener('click', ev => {
            if (!ev.target.closest('#bulk-actions-toggle') && !ev.target.closest('#bulk-actions-menu'))
                $('bulk-actions-menu')?.classList.add('hidden');
            if (!ev.target.closest('#auc-more-toggle') && !ev.target.closest('#auc-more-menu'))
                $('auc-more-menu')?.classList.add('hidden');
        });
    }

    /* ================================================================
       EVENT LOGGER
       ================================================================ */
    function logEvent(event, data) {
        console.info(`[Event] ${event}`, data);
        // append to automation log if visible
        const log = $('automation-log');
        if (log) {
            const div = document.createElement('div');
            div.className = 'p-2 text-[10px]';
            div.textContent = `${new Date().toLocaleTimeString()} · ${event} · ${JSON.stringify(data)}`;
            if (log.firstChild?.classList?.contains('text-center')) log.innerHTML = '';
            log.prepend(div);
        }
    }

    /* ================================================================
       INIT
       ================================================================ */
    function init() {
        bindFilters();
        bindViewSwitching();
        bindSelectAll();
        bindBulkActions();
        bindCalendarControls();
        bindAucTabs();
        bindDetailActions();
        bindConsoleControls();
        bindPublishConfirm();
        bindNotifications();
        bindModalClose();
        initWizard();
        renderTable();

        // expose for debugging
        window.__cs_auctions       = auctions;
        window.__cs_auctions_state = state;
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();

})();