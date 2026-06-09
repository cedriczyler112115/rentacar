import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

(() => {
    const state = {
        images: [],
        index: 0,
        title: 'Photos',
        subtitle: '',
        actions: [],
        isOpen: false,
        controlsVariant: 'default',
    };

    let modal;
    let imgEl;
    let titleEl;
    let subtitleEl;
    let actionsEl;

    function ensure() {
        if (modal) return;

        const style = document.createElement('style');
        style.textContent = `
            #aarCarouselModal { display:none; position:fixed; inset:0; background:rgba(2,6,23,0.88); z-index:200000; align-items:center; justify-content:center; padding:20px; }
            #aarCarouselModal.aar-open { display:flex; }
            #aarCarouselPanel { width:min(92vw, 1400px); height:80vh; background:#111827; border-radius:12px; overflow:hidden; border:1px solid #374151; display:flex; flex-direction:column; transform: translateY(8px); opacity:0; transition: transform .16s ease, opacity .16s ease; }
            #aarCarouselModal.aar-open #aarCarouselPanel { transform: translateY(0); opacity:1; }
            #aarCarouselHeader { padding:12px 14px; background:#0f172a; color:white; display:flex; justify-content:space-between; gap:12px; align-items:center; }
            #aarCarouselHeaderLeft { min-width:0; }
            #aarCarouselTitle { font-weight:900; letter-spacing:.2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
            #aarCarouselSubtitle { margin-top:4px; color:#cbd5e1; font-weight:700; font-size:.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
            #aarCarouselHeaderRight { display:flex; gap:8px; align-items:center; flex-wrap:wrap; justify-content:flex-end; }
            #aarCarouselActions { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
            .aar-btn { background:#1f2937; color:#e5e7eb; border:1px solid #374151; padding:8px 10px; border-radius:8px; font-weight:900; cursor:pointer; }
            .aar-btn:disabled { opacity:.55; cursor:not-allowed; }
            .aar-btn-danger { background:#ef4444; color:white; border:1px solid #b91c1c; }
            .aar-btn-accent { background:rgba(245,158,11,0.14); border:1px solid rgba(245,158,11,0.28); color: var(--accent, #f59e0b); }
            .aar-btn-success { background:rgba(16,185,129,0.14); border:1px solid rgba(16,185,129,0.28); color:#10b981; }
            .aar-controls-accent .aar-btn-control { background:rgba(245,158,11,0.14); border:1px solid rgba(245,158,11,0.28); color: var(--accent, #f59e0b); }
            #aarCarouselBody { flex:1; display:flex; align-items:center; justify-content:center; background:#111827; padding:10px; }
            #aarCarouselImg { max-width:100%; max-height:100%; object-fit:contain; transition: opacity .16s ease; opacity:1; }
        `;
        document.head.appendChild(style);

        modal = document.createElement('div');
        modal.id = 'aarCarouselModal';
        modal.innerHTML = `
            <div id="aarCarouselBackdrop" style="position:absolute; inset:0;"></div>
            <div id="aarCarouselPanel" role="dialog" aria-modal="true" aria-label="Photo viewer">
                <div id="aarCarouselHeader">
                    <div id="aarCarouselHeaderLeft">
                        <div id="aarCarouselTitle"></div>
                        <div id="aarCarouselSubtitle"></div>
                    </div>
                    <div id="aarCarouselHeaderRight">
                        <div id="aarCarouselActions"></div>
                        <button type="button" id="aarCarouselPrev" class="aar-btn aar-btn-control">Prev</button>
                        <button type="button" id="aarCarouselNext" class="aar-btn aar-btn-control">Next</button>
                        <button type="button" id="aarCarouselClose" class="aar-btn aar-btn-control aar-btn-danger">Close</button>
                    </div>
                </div>
                <div id="aarCarouselBody">
                    <img id="aarCarouselImg" src="" alt="">
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        imgEl = modal.querySelector('#aarCarouselImg');
        titleEl = modal.querySelector('#aarCarouselTitle');
        subtitleEl = modal.querySelector('#aarCarouselSubtitle');
        actionsEl = modal.querySelector('#aarCarouselActions');

        modal.querySelector('#aarCarouselBackdrop')?.addEventListener('click', close);
        modal.querySelector('#aarCarouselClose')?.addEventListener('click', close);
        modal.querySelector('#aarCarouselPrev')?.addEventListener('click', () => nav(-1));
        modal.querySelector('#aarCarouselNext')?.addEventListener('click', () => nav(1));

        document.addEventListener('keydown', (e) => {
            if (!state.isOpen) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft') nav(-1);
            if (e.key === 'ArrowRight') nav(1);
        });
    }

    function renderControls() {
        if (!modal) return;
        modal.classList.toggle('aar-controls-accent', state.controlsVariant === 'accent');

        const closeBtn = modal.querySelector('#aarCarouselClose');
        if (closeBtn) {
            closeBtn.classList.toggle('aar-btn-danger', state.controlsVariant !== 'accent');
            closeBtn.classList.toggle('aar-btn-control', true);
        }
    }

    function renderHeader() {
        if (!titleEl || !subtitleEl || !actionsEl) return;
        titleEl.textContent = String(state.title || 'Photos');
        subtitleEl.textContent = String(state.subtitle || '');
        subtitleEl.style.display = state.subtitle ? 'block' : 'none';

        actionsEl.innerHTML = '';
        (Array.isArray(state.actions) ? state.actions : []).forEach((a) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'aar-btn' + (a.variant === 'danger' ? ' aar-btn-danger' : a.variant === 'accent' ? ' aar-btn-accent' : a.variant === 'success' ? ' aar-btn-success' : '');
            btn.textContent = String(a.label || 'Action');
            btn.disabled = !!a.disabled;
            btn.addEventListener('click', () => {
                document.dispatchEvent(new CustomEvent('aarcarousel:action', { detail: { id: a.id, index: state.index, src: state.images[state.index] } }));
            });
            actionsEl.appendChild(btn);
        });
    }

    function renderImage() {
        if (!imgEl) return;
        imgEl.style.opacity = '0.35';
        const nextSrc = state.images[state.index] || '';
        imgEl.onload = () => {
            imgEl.style.opacity = '1';
        };
        imgEl.src = nextSrc;
        document.dispatchEvent(new CustomEvent('aarcarousel:change', { detail: { index: state.index, src: state.images[state.index] } }));
    }

    function open(opts) {
        ensure();
        const images = Array.isArray(opts?.images) ? opts.images.filter(Boolean).map(String) : [];
        state.images = images;
        state.index = Math.max(0, Math.min(Number(opts?.startIndex || 0), Math.max(0, images.length - 1)));
        state.title = opts?.title || 'Photos';
        state.subtitle = opts?.subtitle || '';
        state.actions = Array.isArray(opts?.actions) ? opts.actions : [];
        state.controlsVariant = opts?.controlsVariant || 'default';
        state.isOpen = true;

        renderControls();
        renderHeader();
        renderImage();

        modal.classList.add('aar-open');
        document.body.style.overflow = 'hidden';
        document.dispatchEvent(new CustomEvent('aarcarousel:open', { detail: { images: state.images, index: state.index } }));
    }

    function close() {
        if (!modal) return;
        state.isOpen = false;
        modal.classList.remove('aar-open');
        document.body.style.overflow = 'auto';
        document.dispatchEvent(new CustomEvent('aarcarousel:close', { detail: { index: state.index } }));
    }

    function nav(offset) {
        if (!state.isOpen || state.images.length === 0) return;
        state.index = (state.index + offset + state.images.length) % state.images.length;
        renderImage();
    }

    function setTitle(t) {
        state.title = t || '';
        renderHeader();
    }

    function setSubtitle(t) {
        state.subtitle = t || '';
        renderHeader();
    }

    function setActions(actions) {
        state.actions = Array.isArray(actions) ? actions : [];
        renderHeader();
    }

    function getState() {
        return { images: state.images.slice(), index: state.index, isOpen: state.isOpen };
    }

    window.AARCarousel = { open, close, nav, setTitle, setSubtitle, setActions, getState };
})();

(() => {
    const state = {
        isOpen: false,
        mode: 'month',
        cursor: new Date(),
        title: 'Calendar',
        eventsUrl: '',
        ownerBookingUrl: '',
        canOwnerBook: false,
        vehicleId: 0,
        items: [],
        activeDate: '',
    };

    let modal;
    let titleEl;
    let labelEl;
    let gridEl;
    let detailsEl;
    let monthBtn;
    let weekBtn;
    let prevBtn;
    let nextBtn;
    let todayBtn;
    let closeBtn;
    let addBtn;
    let errorEl;

    let formModal;
    let formFrom;
    let formTo;
    let formNote;
    let formSubmit;
    let formCancel;
    let formErr;

    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const weekday = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    const pad = (n) => String(n).padStart(2, '0');
    const toDateStr = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    const dayKey = (iso) => (iso ? String(iso).split('T')[0] : '');
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const parseDateStr = (s) => {
        if (!s) return null;
        const m = String(s).match(/^(\d{4})-(\d{2})-(\d{2})/);
        if (!m) return null;
        const dt = new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]));
        return Number.isNaN(dt.getTime()) ? null : dt;
    };

    const setError = (msg) => {
        if (!errorEl) return;
        if (!msg) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
            return;
        }
        errorEl.textContent = msg;
        errorEl.style.display = 'block';
    };

    const statusColor = (status, tag) => {
        const t = String(tag || '').toLowerCase();
        if (t === 'owner_booking') return { bg: 'rgba(15,23,42,0.08)', bd: 'rgba(15,23,42,0.18)', tx: '#0f172a' };
        const s = String(status || '').toLowerCase();
        if (s === 'pending') return { bg: 'rgba(59,130,246,0.15)', bd: 'rgba(59,130,246,0.25)', tx: '#1d4ed8' };
        if (s === 'confirmed') return { bg: 'rgba(16,185,129,0.15)', bd: 'rgba(16,185,129,0.25)', tx: '#047857' };
        if (s === 'completed') return { bg: 'rgba(167,139,250,0.18)', bd: 'rgba(167,139,250,0.30)', tx: '#6d28d9' };
        if (s === 'rejected' || s === 'cancelled' || s === 'canceled') return { bg: 'rgba(239,68,68,0.14)', bd: 'rgba(239,68,68,0.25)', tx: '#b91c1c' };
        if (s === 'owner booking') return { bg: 'rgba(15,23,42,0.08)', bd: 'rgba(15,23,42,0.18)', tx: '#0f172a' };
        return { bg: 'rgba(148,163,184,0.16)', bd: 'rgba(148,163,184,0.30)', tx: '#475569' };
    };

    function ensure() {
        if (modal) return;

        const style = document.createElement('style');
        style.textContent = `
            #aarBookingCalendarModal { display:none; position:fixed; inset:0; background:rgba(2,6,23,0.84); z-index:200900; align-items:center; justify-content:center; padding:18px; }
            #aarBookingCalendarModal.aar-open { display:flex; }
            #aarBookingCalendarPanel { width:min(1200px, calc(100vw - 36px)); max-height: min(88vh, 920px); background:white; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.35); display:flex; flex-direction:column; }
            #aarBookingCalendarHeader { padding: 12px 14px; background:#0f172a; color:white; display:flex; align-items:center; justify-content:space-between; gap:12px; }
            #aarBookingCalendarTitle { font-weight: 900; letter-spacing: .2px; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
            #aarBookingCalendarHeaderRight { display:flex; gap:8px; align-items:center; flex-wrap:wrap; justify-content:flex-end; }
            #aarBookingCalendarBody { padding: 14px; background:#f8fafc; overflow:auto; flex: 1; -webkit-overflow-scrolling: touch; }
            #aarBookingCalendarToolbar { display:flex; gap:10px; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; }
            #aarBookingCalendarToolbarLeft { display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; }
            #aarBookingCalendarLabel { min-width: 200px; text-align:center; font-weight: 900; color:#0f172a; }
            #aarBookingCalendarMain { margin-top: 12px; display:grid; grid-template-columns: 1fr 360px; gap: 12px; align-items:start; }
            #aarBookingCalendarGridWrap { border: 1px solid #e2e8f0; border-radius: 14px; background: white; overflow:auto; }
            #aarBookingCalendarGrid { min-width: 860px; padding: 10px; display:grid; grid-template-columns: repeat(7, minmax(120px, 1fr)); gap: 8px; }
            #aarBookingCalendarDetails { border: 1px solid #e2e8f0; border-radius: 14px; background: white; overflow:hidden; }
            #aarBookingCalendarDetailsHead { padding: 12px 14px; background: rgba(15,23,42,0.04); border-bottom: 1px solid #e2e8f0; font-weight: 900; color:#0f172a; }
            #aarBookingCalendarDetailsBody { padding: 12px 14px; }
            .aarcal-btn { background:#ffffff; border:1px solid #e2e8f0; color:#0f172a; padding: 10px 12px; border-radius: 10px; font-weight: 900; cursor:pointer; }
            .aarcal-btn-primary { background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.28); color: var(--accent, #f59e0b); }
            .aarcal-btn-danger { background: #ef4444; border: 1px solid #b91c1c; color: white; }
            .aarcal-pill { padding: 6px 8px; border-radius: 10px; font-weight: 900; font-size: 0.78rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            #aarBookingCalendarError { display:none; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(239,68,68,0.25); background: rgba(239,68,68,0.10); color:#b91c1c; font-weight: 900; }
            #aarBookingCalendarForm { display:none; position:fixed; inset:0; z-index:200910; background: rgba(2,6,23,0.78); align-items:center; justify-content:center; padding:18px; }
            #aarBookingCalendarForm.aar-open { display:flex; }
            #aarBookingCalendarFormPanel { width: min(560px, calc(100vw - 36px)); background: white; border:1px solid #e2e8f0; border-radius: 14px; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.35); display:flex; flex-direction:column; }
            #aarBookingCalendarFormHead { padding: 12px 14px; background:#0f172a; color:white; font-weight: 900; display:flex; align-items:center; justify-content:space-between; gap: 10px; }
            #aarBookingCalendarFormBody { padding: 14px; background:#f8fafc; }
            #aarBookingCalendarFormBody label { display:block; font-weight: 900; margin-bottom: 6px; color:#0f172a; }
            #aarBookingCalendarFormBody input, #aarBookingCalendarFormBody textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; background: white; }
            #aarBookingCalendarFormActions { display:flex; gap: 10px; justify-content:flex-end; margin-top: 12px; flex-wrap:wrap; }
            #aarBookingCalendarFormError { display:none; margin-top: 10px; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(239,68,68,0.25); background: rgba(239,68,68,0.10); color:#b91c1c; font-weight: 900; }
            @media (max-width: 980px) {
                #aarBookingCalendarMain { grid-template-columns: 1fr; }
                #aarBookingCalendarGrid { min-width: 860px; }
            }
            @media (max-width: 640px) {
                #aarBookingCalendarModal { padding: 12px; }
                #aarBookingCalendarLabel { min-width: 0; text-align:left; }
                #aarBookingCalendarGrid { min-width: 840px; }
            }
        `;
        document.head.appendChild(style);

        modal = document.createElement('div');
        modal.id = 'aarBookingCalendarModal';
        modal.innerHTML = `
            <div id="aarBookingCalendarBackdrop" style="position:absolute; inset:0;"></div>
            <div id="aarBookingCalendarPanel" role="dialog" aria-modal="true" aria-label="Booking calendar">
                <div id="aarBookingCalendarHeader">
                    <div id="aarBookingCalendarTitle"></div>
                    <div id="aarBookingCalendarHeaderRight">
                        <button type="button" id="aarBookingCalendarAdd" class="aarcal-btn aarcal-btn-primary" style="display:none;">Add Owner Booking</button>
                        <button type="button" id="aarBookingCalendarToday" class="aarcal-btn">Today</button>
                        <button type="button" id="aarBookingCalendarPrev" class="aarcal-btn">Prev</button>
                        <button type="button" id="aarBookingCalendarNext" class="aarcal-btn">Next</button>
                        <button type="button" id="aarBookingCalendarClose" class="aarcal-btn aarcal-btn-danger">Close</button>
                    </div>
                </div>
                <div id="aarBookingCalendarBody">
                    <div id="aarBookingCalendarToolbar">
                        <div id="aarBookingCalendarToolbarLeft">
                            <button type="button" id="aarBookingCalendarMonth" class="aarcal-btn aarcal-btn-primary">Month</button>
                            <button type="button" id="aarBookingCalendarWeek" class="aarcal-btn">Week</button>
                        </div>
                        <div id="aarBookingCalendarLabel"></div>
                    </div>
                    <div id="aarBookingCalendarError"></div>
                    <div id="aarBookingCalendarMain">
                        <div id="aarBookingCalendarGridWrap"><div id="aarBookingCalendarGrid"></div></div>
                        <div id="aarBookingCalendarDetails">
                            <div id="aarBookingCalendarDetailsHead">Select a date</div>
                            <div id="aarBookingCalendarDetailsBody" style="color:#64748b; font-weight: 800;">Tap any day to see bookings.</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        titleEl = modal.querySelector('#aarBookingCalendarTitle');
        labelEl = modal.querySelector('#aarBookingCalendarLabel');
        gridEl = modal.querySelector('#aarBookingCalendarGrid');
        detailsEl = modal.querySelector('#aarBookingCalendarDetails');
        monthBtn = modal.querySelector('#aarBookingCalendarMonth');
        weekBtn = modal.querySelector('#aarBookingCalendarWeek');
        prevBtn = modal.querySelector('#aarBookingCalendarPrev');
        nextBtn = modal.querySelector('#aarBookingCalendarNext');
        todayBtn = modal.querySelector('#aarBookingCalendarToday');
        closeBtn = modal.querySelector('#aarBookingCalendarClose');
        addBtn = modal.querySelector('#aarBookingCalendarAdd');
        errorEl = modal.querySelector('#aarBookingCalendarError');

        formModal = document.createElement('div');
        formModal.id = 'aarBookingCalendarForm';
        formModal.innerHTML = `
            <div id="aarBookingCalendarFormBackdrop" style="position:absolute; inset:0;"></div>
            <div id="aarBookingCalendarFormPanel" role="dialog" aria-modal="true" aria-label="Create owner booking">
                <div id="aarBookingCalendarFormHead">
                    <div>Owner Booking</div>
                    <button type="button" id="aarBookingCalendarFormClose" class="aarcal-btn aarcal-btn-danger" style="padding: 6px 10px;">Close</button>
                </div>
                <div id="aarBookingCalendarFormBody">
                    <div>
                        <label for="aarBookingCalendarFormFrom">Start</label>
                        <input id="aarBookingCalendarFormFrom" type="datetime-local">
                    </div>
                    <div style="margin-top: 12px;">
                        <label for="aarBookingCalendarFormTo">End</label>
                        <input id="aarBookingCalendarFormTo" type="datetime-local">
                    </div>
                    <div style="margin-top: 12px;">
                        <label for="aarBookingCalendarFormNote">Note (optional)</label>
                        <textarea id="aarBookingCalendarFormNote" rows="3" maxlength="5000"></textarea>
                    </div>
                    <div id="aarBookingCalendarFormError"></div>
                    <div id="aarBookingCalendarFormActions">
                        <button type="button" id="aarBookingCalendarFormCancel" class="aarcal-btn">Cancel</button>
                        <button type="button" id="aarBookingCalendarFormSubmit" class="aarcal-btn aarcal-btn-primary">Create</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(formModal);

        formFrom = formModal.querySelector('#aarBookingCalendarFormFrom');
        formTo = formModal.querySelector('#aarBookingCalendarFormTo');
        formNote = formModal.querySelector('#aarBookingCalendarFormNote');
        formSubmit = formModal.querySelector('#aarBookingCalendarFormSubmit');
        formCancel = formModal.querySelector('#aarBookingCalendarFormCancel');
        formErr = formModal.querySelector('#aarBookingCalendarFormError');

        modal.querySelector('#aarBookingCalendarBackdrop')?.addEventListener('click', close);
        closeBtn?.addEventListener('click', close);

        monthBtn?.addEventListener('click', () => setMode('month'));
        weekBtn?.addEventListener('click', () => setMode('week'));
        prevBtn?.addEventListener('click', () => shift(-1));
        nextBtn?.addEventListener('click', () => shift(1));
        todayBtn?.addEventListener('click', () => {
            state.cursor = new Date();
            refresh();
        });

        addBtn?.addEventListener('click', () => openForm(state.activeDate || toDateStr(new Date())));

        formModal.querySelector('#aarBookingCalendarFormBackdrop')?.addEventListener('click', closeForm);
        formModal.querySelector('#aarBookingCalendarFormClose')?.addEventListener('click', closeForm);
        formCancel?.addEventListener('click', closeForm);
        formSubmit?.addEventListener('click', submitOwnerBooking);

        document.addEventListener('keydown', (e) => {
            if (!state.isOpen) return;
            if (e.key === 'Escape') {
                if (formModal.classList.contains('aar-open')) closeForm();
                else close();
            }
        });
    }

    function setMode(m) {
        state.mode = m;
        if (monthBtn && weekBtn) {
            monthBtn.classList.toggle('aarcal-btn-primary', state.mode === 'month');
            weekBtn.classList.toggle('aarcal-btn-primary', state.mode === 'week');
        }
        refresh();
    }

    function shift(dir) {
        if (state.mode === 'week') {
            state.cursor.setDate(state.cursor.getDate() + dir * 7);
        } else {
            state.cursor = new Date(state.cursor.getFullYear(), state.cursor.getMonth() + dir, 1);
        }
        refresh();
    }

    function getViewRange() {
        if (state.mode === 'week') {
            const start = new Date(state.cursor.getFullYear(), state.cursor.getMonth(), state.cursor.getDate());
            start.setDate(start.getDate() - start.getDay());
            const end = new Date(start.getFullYear(), start.getMonth(), start.getDate());
            end.setDate(end.getDate() + 6);
            return { start, end, label: `${toDateStr(start)} → ${toDateStr(end)}` };
        }

        const first = new Date(state.cursor.getFullYear(), state.cursor.getMonth(), 1);
        const last = new Date(state.cursor.getFullYear(), state.cursor.getMonth() + 1, 0);
        const start = new Date(first.getFullYear(), first.getMonth(), first.getDate());
        start.setDate(start.getDate() - start.getDay());
        const end = new Date(last.getFullYear(), last.getMonth(), last.getDate());
        end.setDate(end.getDate() + (6 - end.getDay()));
        return { start, end, label: `${monthNames[state.cursor.getMonth()]} ${state.cursor.getFullYear()}` };
    }

    async function fetchEvents() {
        setError('');
        if (!state.eventsUrl) {
            state.items = [];
            return;
        }

        const range = getViewRange();
        const params = new URLSearchParams({
            start: toDateStr(range.start),
            end: toDateStr(range.end),
        });

        try {
            const res = await fetch(state.eventsUrl + (state.eventsUrl.includes('?') ? '&' : '?') + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-AAR-No-Loader': '1' }
            });
            if (!res.ok) throw new Error('Failed to load events');
            const data = await res.json();
            const all = Array.isArray(data.items) ? data.items : [];
            const allowed = new Set(['pending','confirmed','completed','owner booking']);
            state.items = all.filter((it) => {
                const s = String(it.status || '').toLowerCase();
                const isOwnerTag = String(it.tag || '') === 'owner_booking';
                return allowed.has(s) || isOwnerTag;
            });
        } catch (e) {
            state.items = [];
            setError('Unable to load calendar data.');
        }
    }

    function expandDaysForItem(it) {
        const s = parseDateStr(dayKey(it.from));
        const e = parseDateStr(dayKey(it.to));
        if (!s || !e) return [];
        const out = [];
        const c = new Date(s.getFullYear(), s.getMonth(), s.getDate());
        const end = new Date(e.getFullYear(), e.getMonth(), e.getDate());
        while (c <= end) {
            out.push(toDateStr(c));
            c.setDate(c.getDate() + 1);
        }
        return out;
    }

    function buildByDay() {
        const map = {};
        state.items.forEach((it) => {
            expandDaysForItem(it).forEach((d) => {
                if (!map[d]) map[d] = [];
                map[d].push(it);
            });
        });
        return map;
    }

    function render() {
        if (!gridEl || !labelEl || !titleEl || !detailsEl) return;
        const range = getViewRange();
        labelEl.textContent = range.label;
        titleEl.textContent = String(state.title || 'Calendar');

        if (addBtn) {
            addBtn.style.display = state.canOwnerBook ? 'inline-flex' : 'none';
        }

        const byDay = buildByDay();
        gridEl.innerHTML = '';

        weekday.forEach((w) => {
            const h = document.createElement('div');
            h.textContent = w;
            h.style.fontWeight = '900';
            h.style.color = '#64748b';
            h.style.textAlign = 'center';
            h.style.padding = '8px 0';
            gridEl.appendChild(h);
        });

        const cur = new Date(range.start.getFullYear(), range.start.getMonth(), range.start.getDate());
        const end = new Date(range.end.getFullYear(), range.end.getMonth(), range.end.getDate());
        while (cur <= end) {
            const ds = toDateStr(cur);
            const list = byDay[ds] || [];

            const cell = document.createElement('button');
            cell.type = 'button';
            cell.style.border = '1px solid #e2e8f0';
            cell.style.borderRadius = '12px';
            cell.style.background = ds === state.activeDate ? 'rgba(245,158,11,0.12)' : '#ffffff';
            cell.style.padding = '10px';
            cell.style.minHeight = state.mode === 'week' ? '120px' : '110px';
            cell.style.cursor = 'pointer';
            cell.style.textAlign = 'left';
            cell.style.display = 'flex';
            cell.style.flexDirection = 'column';
            cell.style.gap = '8px';

            const head = document.createElement('div');
            head.style.display = 'flex';
            head.style.justifyContent = 'space-between';
            head.style.alignItems = 'center';

            const num = document.createElement('div');
            num.textContent = String(cur.getDate());
            num.style.fontWeight = '900';
            num.style.color = '#0f172a';

            const count = document.createElement('div');
            count.textContent = list.length > 0 ? `${list.length}` : '';
            count.style.fontWeight = '900';
            count.style.color = list.length > 0 ? '#f59e0b' : '#94a3b8';
            count.style.fontSize = '0.9rem';

            head.appendChild(num);
            head.appendChild(count);

            const body = document.createElement('div');
            body.style.display = 'flex';
            body.style.flexDirection = 'column';
            body.style.gap = '6px';

            list.slice(0, 3).forEach((it) => {
                const c = statusColor(it.status, it.tag);
                const pill = document.createElement('div');
                const base = it.tag === 'owner_booking' ? 'Owner Booking' : (it.status || 'Booked');
                pill.textContent = base;
                pill.className = 'aarcal-pill';
                pill.style.background = c.bg;
                pill.style.border = `1px solid ${c.bd}`;
                pill.style.color = c.tx;
                body.appendChild(pill);
            });

            if (list.length > 3) {
                const more = document.createElement('div');
                more.textContent = `+${list.length - 3} more`;
                more.style.color = '#64748b';
                more.style.fontWeight = '900';
                more.style.fontSize = '0.85rem';
                body.appendChild(more);
            }

            cell.appendChild(head);
            cell.appendChild(body);
            cell.addEventListener('click', () => {
                state.activeDate = ds;
                renderDetails(ds, list);
                render();
            });

            gridEl.appendChild(cell);
            cur.setDate(cur.getDate() + 1);
        }
    }

    function renderDetails(dateStr, list) {
        const head = detailsEl?.querySelector('#aarBookingCalendarDetailsHead');
        const body = detailsEl?.querySelector('#aarBookingCalendarDetailsBody');
        if (!head || !body) return;

        head.textContent = `Bookings on ${dateStr}`;
        body.innerHTML = '';

        if (!list || list.length === 0) {
            const empty = document.createElement('div');
            empty.textContent = 'No bookings.';
            empty.style.color = '#64748b';
            empty.style.fontWeight = '800';
            body.appendChild(empty);
            return;
        }

        list.forEach((it) => {
            const c = statusColor(it.status, it.tag);
            const card = document.createElement('div');
            card.style.border = '1px solid #e2e8f0';
            card.style.borderRadius = '12px';
            card.style.padding = '12px';
            card.style.background = '#ffffff';
            card.style.marginBottom = '10px';

            if (String(it.tag || '') === 'owner_booking' || String(it.status || '').toLowerCase() === 'owner booking') {
                const title = document.createElement('div');
                title.textContent = it.vehicle_name || state.title || 'Vehicle';
                title.style.fontWeight = '900';
                title.style.color = '#0f172a';

                const meta = document.createElement('div');
                meta.style.marginTop = '10px';
                meta.style.color = '#0f172a';
                meta.style.fontWeight = '700';
                meta.style.lineHeight = '1.55';
                const from = it.from ? new Date(it.from) : null;
                const to = it.to ? new Date(it.to) : null;
                meta.textContent = `From: ${from ? from.toLocaleString() : '—'} | To: ${to ? to.toLocaleString() : '—'}`;

                card.appendChild(title);
                card.appendChild(meta);

                const note = String(it.note || '').trim();
                if (note) {
                    const noteBox = document.createElement('div');
                    noteBox.style.marginTop = '10px';
                    noteBox.style.border = '1px solid #e2e8f0';
                    noteBox.style.borderRadius = '12px';
                    noteBox.style.padding = '12px';
                    noteBox.style.background = '#ffffff';
                    noteBox.style.color = '#0f172a';
                    noteBox.style.fontWeight = '800';
                    noteBox.style.lineHeight = '1.6';
                    noteBox.innerHTML = `<div><span style="color:#64748b; font-weight:900;">Note:</span> ${note}</div>`;
                    card.appendChild(noteBox);
                }

                body.appendChild(card);
                return;
            }

            const top = document.createElement('div');
            top.style.display = 'flex';
            top.style.justifyContent = 'space-between';
            top.style.gap = '10px';
            top.style.flexWrap = 'wrap';

            const left = document.createElement('div');
            const title = document.createElement('div');
            title.textContent = it.tag === 'owner_booking' ? 'Owner Booking' : (it.status || 'Booking');
            title.style.fontWeight = '900';
            title.style.color = '#0f172a';
            const ref = document.createElement('div');
            ref.textContent = `Reference: ${it.reference || it.id || '—'}`;
            ref.style.marginTop = '4px';
            ref.style.color = '#64748b';
            ref.style.fontWeight = '800';
            left.appendChild(title);
            left.appendChild(ref);

            const badge = document.createElement('div');
            badge.textContent = it.tag === 'owner_booking' ? 'Owner' : (it.status || '—');
            badge.style.padding = '6px 10px';
            badge.style.borderRadius = '999px';
            badge.style.background = c.bg;
            badge.style.border = `1px solid ${c.bd}`;
            badge.style.color = c.tx;
            badge.style.fontWeight = '900';
            badge.style.textTransform = 'capitalize';

            top.appendChild(left);
            top.appendChild(badge);

            const meta = document.createElement('div');
            meta.style.marginTop = '10px';
            meta.style.color = '#0f172a';
            meta.style.fontWeight = '700';
            meta.style.lineHeight = '1.55';
            const from = it.from ? new Date(it.from) : null;
            const to = it.to ? new Date(it.to) : null;
            meta.textContent = `From: ${from ? from.toLocaleString() : '—'} | To: ${to ? to.toLocaleString() : '—'}`;

            card.appendChild(top);
            card.appendChild(meta);
            body.appendChild(card);
        });
    }

    async function refresh() {
        await fetchEvents();
        if (!state.activeDate) state.activeDate = '';
        render();
        if (state.activeDate) {
            const byDay = buildByDay();
            renderDetails(state.activeDate, byDay[state.activeDate] || []);
        }
    }

    function openForm(dateStr) {
        if (!state.canOwnerBook || !state.ownerBookingUrl || !formModal) return;

        formErr.style.display = 'none';
        formErr.textContent = '';

        const d = parseDateStr(dateStr);
        const now = new Date();
        const base = d ? new Date(d.getFullYear(), d.getMonth(), d.getDate()) : new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const from = new Date(base.getFullYear(), base.getMonth(), base.getDate(), 8, 0);
        const to = new Date(base.getFullYear(), base.getMonth(), base.getDate(), 17, 0);

        const toLocalInput = (dt) => {
            const y = dt.getFullYear();
            const m = pad(dt.getMonth() + 1);
            const d2 = pad(dt.getDate());
            const h = pad(dt.getHours());
            const mi = pad(dt.getMinutes());
            return `${y}-${m}-${d2}T${h}:${mi}`;
        };

        formFrom.value = toLocalInput(from);
        formTo.value = toLocalInput(to);
        formNote.value = '';
        formSubmit.disabled = false;

        formModal.classList.add('aar-open');
        document.body.style.overflow = 'hidden';
    }

    function closeForm() {
        if (!formModal) return;
        formModal.classList.remove('aar-open');
        if (modal?.classList.contains('aar-open')) document.body.style.overflow = 'hidden';
        else document.body.style.overflow = 'auto';
    }

    async function submitOwnerBooking() {
        if (!state.canOwnerBook || !state.ownerBookingUrl) return;
        if (!formFrom.value || !formTo.value) {
            formErr.textContent = 'Start and end date/time are required.';
            formErr.style.display = 'block';
            return;
        }

        formErr.style.display = 'none';
        formErr.textContent = '';
        formSubmit.disabled = true;

        try {
            const res = await fetch(state.ownerBookingUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                },
                body: JSON.stringify({
                    vehicle_id: state.vehicleId,
                    datetime_from: formFrom.value,
                    datetime_to: formTo.value,
                    note: formNote.value || null,
                }),
            });

            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.ok) {
                const msg = (data && data.message) ? data.message : 'Failed to create owner booking.';
                const errs = data && data.errors ? data.errors : null;
                let first = '';
                if (errs && typeof errs === 'object') {
                    const k = Object.keys(errs)[0];
                    if (k && Array.isArray(errs[k]) && errs[k][0]) first = errs[k][0];
                }
                formErr.textContent = first || msg;
                formErr.style.display = 'block';
                return;
            }

            closeForm();
            await refresh();
        } catch (e) {
            formErr.textContent = 'Failed to create owner booking.';
            formErr.style.display = 'block';
        } finally {
            formSubmit.disabled = false;
        }
    }

    function open(opts) {
        ensure();
        state.title = String(opts?.title || 'Calendar');
        state.eventsUrl = String(opts?.eventsUrl || '');
        state.ownerBookingUrl = String(opts?.ownerBookingUrl || '');
        state.canOwnerBook = !!opts?.canOwnerBook;
        state.vehicleId = Number(opts?.vehicleId || 0);
        state.items = [];
        state.activeDate = '';
        state.cursor = new Date();

        if (window.innerWidth < 640) {
            state.mode = 'week';
        } else {
            state.mode = 'month';
        }

        if (monthBtn && weekBtn) {
            monthBtn.classList.toggle('aarcal-btn-primary', state.mode === 'month');
            weekBtn.classList.toggle('aarcal-btn-primary', state.mode === 'week');
        }

        state.isOpen = true;
        modal.classList.add('aar-open');
        document.body.style.overflow = 'hidden';
        refresh();
    }

    function close() {
        if (!modal) return;
        closeForm();
        state.isOpen = false;
        modal.classList.remove('aar-open');
        document.body.style.overflow = 'auto';
        setError('');
    }

    document.addEventListener('click', (e) => {
        const el = e.target?.closest?.('[data-aar-calendar-open]');
        if (!el) return;
        const eventsUrl = el.getAttribute('data-events-url') || '';
        if (!eventsUrl) return;
        e.preventDefault();
        open({
            title: el.getAttribute('data-title') || 'Calendar',
            eventsUrl,
            vehicleId: Number(el.getAttribute('data-vehicle-id') || 0),
            canOwnerBook: el.getAttribute('data-can-owner-book') === '1',
            ownerBookingUrl: el.getAttribute('data-owner-booking-url') || '',
        });
    });

    window.AARBookingCalendarModal = { open, close };
})();
