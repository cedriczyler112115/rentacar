<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('Booking Calendar') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">View booking schedules for your vehicles.</p>
            </div>
            <div style="display:flex; gap: 10px; flex-wrap: wrap; align-items:center;">
                <button type="button" id="bcAddOwnerBookingBtn" class="btn btn-primary" style="padding: 10px 12px;">Add Owner Booking</button>
            </div>
        </div>
    </x-slot>

    <div class="container" style="padding: 10px 0 40px 0;">
        <style>
            #bcGridWrap { margin-top: 12px; overflow: auto; border: 1px solid #e2e8f0; border-radius: 14px; background: white; }
            #bcGrid { padding: 10px; min-width: 860px; }
            @media (max-width: 980px) {
                #bcGrid { min-width: 860px; }
            }
            @media (max-width: 640px) {
                #bcGrid { min-width: 840px; }
            }
        </style>
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; box-shadow: var(--shadow-sm);">
            <div style="display:flex; justify-content:space-between; gap: 10px; align-items:flex-end; flex-wrap: wrap;">
                <div style="display:flex; gap: 10px; flex-wrap: wrap; align-items:flex-end;">
                    <div style="min-width: 240px;">
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">Vehicle Filter</div>
                        <select id="bcVehicle" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                            <option value="0">All Vehicles</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}{{ $v->license_plate ? ' ('.$v->license_plate.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="min-width: 180px;">
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">View</div>
                        <div style="display:flex; gap: 8px;">
                            <button type="button" id="bcMonthBtn" class="btn btn-primary" style="padding: 10px 12px;">Month</button>
                            <button type="button" id="bcWeekBtn" class="btn btn-outline" style="padding: 10px 12px;">Week</button>
                        </div>
                    </div>

                    <div style="min-width: 160px;">
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">From</div>
                        <input id="bcFrom" type="date" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div style="min-width: 160px;">
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">To</div>
                        <input id="bcTo" type="date" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    </div>
                </div>

                <div style="display:flex; gap: 8px; flex-wrap: wrap; align-items:center;">
                    <button type="button" id="bcTodayBtn" class="btn btn-outline" style="padding: 10px 12px;">Today</button>
                    <button type="button" id="bcPrevBtn" class="btn btn-outline" style="padding: 10px 12px;">Prev</button>
                    <div id="bcLabel" style="min-width: 200px; text-align:center; font-weight: 900; color:#0f172a;"></div>
                    <button type="button" id="bcNextBtn" class="btn btn-outline" style="padding: 10px 12px;">Next</button>
                </div>
            </div>

            <div style="margin-top: 14px; display:flex; gap: 10px; flex-wrap: wrap; align-items:center;">
                <div style="display:inline-flex; align-items:center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 4px; background: #3b82f6; display:inline-block;"></span>
                    <span style="font-weight: 900; color:#475569; font-size: 0.9rem;">Pending</span>
                </div>
                <div style="display:inline-flex; align-items:center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 4px; background: #10b981; display:inline-block;"></span>
                    <span style="font-weight: 900; color:#475569; font-size: 0.9rem;">Confirmed</span>
                </div>
                <div style="display:inline-flex; align-items:center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 4px; background: #a78bfa; display:inline-block;"></span>
                    <span style="font-weight: 900; color:#475569; font-size: 0.9rem;">Completed</span>
                </div>
                <div style="display:inline-flex; align-items:center; gap: 8px;">
                    <span style="width: 12px; height: 12px; border-radius: 4px; background: rgba(15,23,42,0.8); display:inline-block;"></span>
                    <span style="font-weight: 900; color:#475569; font-size: 0.9rem;">Owner Booking</span>
                </div>
                <div id="bcError" style="display:none; margin-left:auto; color:#b91c1c; font-weight: 900;"></div>
            </div>

            <div id="bcGridWrap">
                <div id="bcGrid" style="display:grid; grid-template-columns: repeat(7, minmax(120px, 1fr)); gap: 8px;"></div>
            </div>
        </div>
    </div>

    <div id="bcDetailsModal" style="display:none; position:fixed; inset:0; z-index:200800; background: rgba(2,6,23,0.82); align-items:center; justify-content:center; padding: 20px;">
        <div onclick="document.getElementById('bcDetailsModal').style.display='none'; document.body.style.overflow='auto';" style="position:absolute; inset:0;"></div>
        <div style="position:relative; z-index:1; width:min(920px, calc(100vw - 40px)); max-height: 85vh; overflow:hidden; background:white; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); display:flex; flex-direction:column;">
            <div style="padding: 14px 16px; background:#0f172a; color:white; display:flex; justify-content:space-between; gap: 10px; align-items:center;">
                <div id="bcDetailsTitle" style="font-weight: 900;">Booking Details</div>
                <div style="display:flex; gap: 10px; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                    <button type="button" id="bcQuickOwnerBookingBtn" class="btn btn-outline" style="padding: 8px 10px; border-color: rgba(245,158,11,0.35); color: #f59e0b;">Add Owner Booking</button>
                    <button type="button" onclick="document.getElementById('bcDetailsModal').style.display='none'; document.body.style.overflow='auto';" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
                </div>
            </div>
            <div id="bcDetailsBody" style="padding: 16px; background:#f8fafc; overflow:auto;"></div>
        </div>
    </div>

    <div id="bcOwnerBookingModal" style="display:none; position:fixed; inset:0; z-index:200850; background: rgba(2,6,23,0.82); align-items:center; justify-content:center; padding: 20px;">
        <div onclick="document.getElementById('bcOwnerBookingModal').style.display='none'; document.body.style.overflow='auto';" style="position:absolute; inset:0;"></div>
        <div style="position:relative; z-index:1; width:min(640px, calc(100vw - 40px)); max-height: 85vh; overflow:hidden; background:white; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 25px 60px rgba(0,0,0,0.35); display:flex; flex-direction:column;">
            <div style="padding: 14px 16px; background:#0f172a; color:white; display:flex; justify-content:space-between; gap: 10px; align-items:center;">
                <div style="font-weight: 900;">Owner Booking</div>
                <button type="button" onclick="document.getElementById('bcOwnerBookingModal').style.display='none'; document.body.style.overflow='auto';" style="background:none; border:none; color:white; font-size: 2rem; line-height: 1; cursor:pointer; opacity:0.85;">&times;</button>
            </div>
            <div style="padding: 16px; background:#f8fafc; overflow:auto;">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="grid-column: 1 / -1;">
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">Vehicle</div>
                        <select id="bcOwnerVehicle" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}{{ $v->license_plate ? ' ('.$v->license_plate.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">Start</div>
                        <input id="bcOwnerFrom" type="datetime-local" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div>
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">End</div>
                        <input id="bcOwnerTo" type="datetime-local" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div>
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">Estimated Price</div>
                        <input id="bcOwnerEstimatedPrice" type="number" min="0" step="0.01" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div>
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">Actual Price</div>
                        <input id="bcOwnerActualPrice" type="number" min="0" step="0.01" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    </div>
                    <div style="grid-column: 1 / -1; display:flex; align-items:center;">
                        <label style="display:flex; align-items:center; gap:8px; font-weight:700; color:#475569; font-size: 0.85rem; cursor:pointer;">
                            <input type="checkbox" id="bcOwnerSamePriceCheck" style="width: 16px; height: 16px;">
                            Actual price is the same as estimated price
                        </label>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <div style="font-size: 0.78rem; color:#64748b; font-weight: 900; letter-spacing: .06em; text-transform: uppercase;">Note (optional)</div>
                        <textarea id="bcOwnerNote" rows="3" maxlength="5000" style="width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;"></textarea>
                        <div id="bcOwnerErr" style="display:none; margin-top: 10px; color:#b91c1c; font-weight: 900;"></div>
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; gap: 10px; margin-top: 14px; flex-wrap:wrap;">
                    <button type="button" id="bcOwnerCancelBtn" class="btn btn-outline" style="padding: 10px 12px;">Cancel</button>
                    <button type="button" id="bcOwnerSaveBtn" class="btn btn-primary" style="padding: 10px 12px;">Create</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const grid = document.getElementById('bcGrid');
            const label = document.getElementById('bcLabel');
            const prevBtn = document.getElementById('bcPrevBtn');
            const nextBtn = document.getElementById('bcNextBtn');
            const todayBtn = document.getElementById('bcTodayBtn');
            const vehicleSel = document.getElementById('bcVehicle');
            const monthBtn = document.getElementById('bcMonthBtn');
            const weekBtn = document.getElementById('bcWeekBtn');
            const fromEl = document.getElementById('bcFrom');
            const toEl = document.getElementById('bcTo');
            const err = document.getElementById('bcError');

            const modal = document.getElementById('bcDetailsModal');
            const modalTitle = document.getElementById('bcDetailsTitle');
            const modalBody = document.getElementById('bcDetailsBody');
            const quickOwnerBtn = document.getElementById('bcQuickOwnerBookingBtn');

            const ownerModal = document.getElementById('bcOwnerBookingModal');
            const ownerVehicleEl = document.getElementById('bcOwnerVehicle');
            const ownerFromEl = document.getElementById('bcOwnerFrom');
            const ownerToEl = document.getElementById('bcOwnerTo');
            const ownerNoteEl = document.getElementById('bcOwnerNote');
            const ownerErrEl = document.getElementById('bcOwnerErr');
            const ownerCancelBtn = document.getElementById('bcOwnerCancelBtn');
            const ownerSaveBtn = document.getElementById('bcOwnerSaveBtn');
            const addOwnerBtn = document.getElementById('bcAddOwnerBookingBtn');

            const estEl = document.getElementById('bcOwnerEstimatedPrice');
            const actEl = document.getElementById('bcOwnerActualPrice');
            const sameEl = document.getElementById('bcOwnerSamePriceCheck');
            if (sameEl) {
                sameEl.addEventListener('change', () => {
                    if (sameEl.checked) {
                        actEl.value = estEl.value;
                        actEl.readOnly = true;
                    } else {
                        actEl.readOnly = false;
                    }
                });
                estEl.addEventListener('input', () => {
                    if (sameEl.checked) actEl.value = estEl.value;
                });
            }

            let editingOwnerBookingId = null;

            const deleteOwnerBooking = async (id) => {
                if (!confirm('Are you sure you want to delete this owner booking?')) return;
                try {
                    const endpoint = @json(route('booking.calendar.owner-bookings.destroy', ['rental' => '__ID__'])).replace('__ID__', id);
                    const res = await fetch(endpoint, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        }
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || !data.ok) {
                        alert('Failed to delete owner booking.');
                        return;
                    }
                    if (modal) { modal.style.display = 'none'; document.body.style.overflow = 'auto'; }
                    await refresh();
                } catch (e) {
                    alert('Failed to delete owner booking.');
                }
            };

            if (!grid || !label || !prevBtn || !nextBtn || !todayBtn || !vehicleSel || !monthBtn || !weekBtn || !fromEl || !toEl) return;

            const pad = (n) => String(n).padStart(2, '0');
            const toDateStr = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
            const parseDateStr = (s) => {
                if (!s) return null;
                const m = String(s).match(/^(\d{4})-(\d{2})-(\d{2})/);
                if (!m) return null;
                const dt = new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]));
                return Number.isNaN(dt.getTime()) ? null : dt;
            };

            const statusColor = (status) => {
                const s = String(status || '').toLowerCase();
                if (s === 'owner booking') return { bg: 'rgba(15,23,42,0.08)', bd: 'rgba(15,23,42,0.18)', tx: '#0f172a' };
                if (s === 'pending') return { bg: 'rgba(59,130,246,0.15)', bd: 'rgba(59,130,246,0.25)', tx: '#1d4ed8' };
                if (s === 'confirmed') return { bg: 'rgba(16,185,129,0.15)', bd: 'rgba(16,185,129,0.25)', tx: '#047857' };
                if (s === 'completed') return { bg: 'rgba(167,139,250,0.18)', bd: 'rgba(167,139,250,0.30)', tx: '#6d28d9' };
                if (s === 'rejected' || s === 'cancelled' || s === 'canceled') return { bg: 'rgba(239,68,68,0.14)', bd: 'rgba(239,68,68,0.25)', tx: '#b91c1c' };
                return { bg: 'rgba(148,163,184,0.16)', bd: 'rgba(148,163,184,0.30)', tx: '#475569' };
            };

            const setError = (msg) => {
                if (!err) return;
                if (!msg) { err.style.display = 'none'; err.textContent = ''; return; }
                err.textContent = msg;
                err.style.display = 'block';
            };

            const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            let mode = 'month';
            let cursor = new Date();
            let items = [];
            let poll = null;
            let activeDayForOwnerBooking = '';

            const getViewRange = () => {
                const manualFrom = parseDateStr(fromEl.value);
                const manualTo = parseDateStr(toEl.value);
                if (manualFrom && manualTo && toDateStr(manualTo) >= toDateStr(manualFrom)) {
                    return { start: manualFrom, end: manualTo, label: `${toDateStr(manualFrom)} → ${toDateStr(manualTo)}` };
                }

                if (mode === 'week') {
                    const start = new Date(cursor.getFullYear(), cursor.getMonth(), cursor.getDate());
                    start.setDate(start.getDate() - start.getDay());
                    const end = new Date(start.getFullYear(), start.getMonth(), start.getDate());
                    end.setDate(end.getDate() + 6);
                    return { start, end, label: `${toDateStr(start)} → ${toDateStr(end)}` };
                }

                const first = new Date(cursor.getFullYear(), cursor.getMonth(), 1);
                const last = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0);
                const start = new Date(first.getFullYear(), first.getMonth(), first.getDate());
                start.setDate(start.getDate() - start.getDay());
                const end = new Date(last.getFullYear(), last.getMonth(), last.getDate());
                end.setDate(end.getDate() + (6 - end.getDay()));
                return { start, end, label: `${monthNames[cursor.getMonth()]} ${cursor.getFullYear()}` };
            };

            const fetchEvents = async () => {
                setError('');
                const range = getViewRange();
                const vehicleId = Number(vehicleSel.value || 0);
                const params = new URLSearchParams({
                    start: toDateStr(range.start),
                    end: toDateStr(range.end),
                });
                if (vehicleId > 0) params.set('vehicle_id', String(vehicleId));

                try {
                    const res = await fetch(@json(route('booking.calendar.events')) + '?' + params.toString(), {
                        headers: { 'Accept': 'application/json', 'X-AAR-No-Loader': '1' }
                    });
                    if (!res.ok) throw new Error('Failed to load events');
                    const data = await res.json();
                    const all = Array.isArray(data.items) ? data.items : [];
                    const allowed = new Set(['pending','confirmed','completed','owner booking']);
                    items = all.filter((it) => {
                        const s = String(it.status || '').toLowerCase();
                        const isOwnerTag = String(it.tag || '') === 'owner_booking';
                        return allowed.has(s) || isOwnerTag;
                    });
                } catch (e) {
                    items = [];
                    setError('Unable to load booking calendar data.');
                }
            };

            const dayKey = (iso) => {
                if (!iso) return '';
                return String(iso).split('T')[0];
            };

            const expandDaysForItem = (it) => {
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
            };

            const buildByDay = () => {
                const map = {};
                items.forEach((it) => {
                    expandDaysForItem(it).forEach((d) => {
                        if (!map[d]) map[d] = [];
                        map[d].push(it);
                    });
                });
                return map;
            };

            const openDetails = (dateStr, list) => {
                if (!modal || !modalTitle || !modalBody) return;
                modalTitle.textContent = `Bookings on ${dateStr}`;
                modalBody.innerHTML = '';
                activeDayForOwnerBooking = dateStr;

                if (!list || list.length === 0) {
                    const empty = document.createElement('div');
                    empty.textContent = 'No bookings.';
                    empty.style.color = '#64748b';
                    empty.style.fontWeight = '800';
                    modalBody.appendChild(empty);
                } else {
                    list.forEach((it) => {
                        const isOwnerBooking = String(it.tag || '') === 'owner_booking' || String(it.status || '').toLowerCase() === 'owner booking';
                        const c = statusColor(it.status);
                        const card = document.createElement('div');
                        card.style.background = 'white';
                        card.style.border = '1px solid #e2e8f0';
                        card.style.borderRadius = '12px';
                        card.style.padding = '14px';
                        card.style.marginBottom = '10px';

                        if (isOwnerBooking) {
                            const title = document.createElement('div');
                            title.textContent = it.vehicle_name || 'Vehicle';
                            title.style.fontWeight = '900';
                            title.style.color = '#0f172a';

                            const meta = document.createElement('div');
                            meta.style.marginTop = '10px';
                            meta.style.color = '#0f172a';
                            meta.style.fontWeight = '700';
                            meta.style.lineHeight = '1.55';
                            const from = it.from ? new Date(it.from) : null;
                            const to = it.to ? new Date(it.to) : null;
                            const fromStr = from ? from.toLocaleString() : '—';
                            const toStr = to ? to.toLocaleString() : '—';
                            meta.textContent = `From: ${fromStr} | To: ${toStr}`;

                            const note = String(it.note || '').trim();
                            const estTxt = it.estimated_service_fee !== null ? new Intl.NumberFormat().format(it.estimated_service_fee) : '—';
                            const actTxt = it.actual_service_fee !== null ? new Intl.NumberFormat().format(it.actual_service_fee) : '—';
                            
                            const priceBox = document.createElement('div');
                            priceBox.style.marginTop = '6px';
                            priceBox.style.color = '#334155';
                            priceBox.style.fontWeight = '700';
                            priceBox.innerHTML = `<div><span style="color:#64748b; font-weight:900;">Estimated Price:</span> ₱${estTxt}</div><div><span style="color:#64748b; font-weight:900;">Actual Price:</span> ₱${actTxt}</div>`;
                            card.appendChild(title);
                            card.appendChild(meta);
                            card.appendChild(priceBox);

                            if (note) {
                                const noteBox = document.createElement('div');
                                noteBox.style.marginTop = '10px';
                                noteBox.style.background = '#ffffff';
                                noteBox.style.border = '1px solid #e2e8f0';
                                noteBox.style.borderRadius = '12px';
                                noteBox.style.padding = '12px';
                                noteBox.style.color = '#0f172a';
                                noteBox.style.fontWeight = '800';
                                noteBox.style.lineHeight = '1.6';
                                noteBox.innerHTML = `<div><span style="color:#64748b; font-weight:900;">Note:</span> ${note}</div>`;
                                card.appendChild(noteBox);
                            }

                            const btnWrap = document.createElement('div');
                            btnWrap.style.marginTop = '12px';
                            btnWrap.style.display = 'flex';
                            btnWrap.style.gap = '8px';
                            
                            const editBtn = document.createElement('button');
                            editBtn.textContent = 'Edit';
                            editBtn.className = 'btn btn-outline';
                            editBtn.style.padding = '6px 12px';
                            editBtn.onclick = () => {
                                if (modal) { modal.style.display = 'none'; document.body.style.overflow = 'auto'; }
                                openOwnerBooking('', true, it);
                            };

                            const delBtn = document.createElement('button');
                            delBtn.textContent = 'Delete';
                            delBtn.style.padding = '6px 12px';
                            delBtn.style.background = '#fee2e2';
                            delBtn.style.color = '#991b1b';
                            delBtn.style.border = '1px solid #fecaca';
                            delBtn.style.borderRadius = '10px';
                            delBtn.style.fontWeight = '800';
                            delBtn.style.cursor = 'pointer';
                            delBtn.onclick = () => deleteOwnerBooking(it.id);

                            btnWrap.appendChild(editBtn);
                            btnWrap.appendChild(delBtn);
                            card.appendChild(btnWrap);

                            modalBody.appendChild(card);
                            return;
                        }

                        const top = document.createElement('div');
                        top.style.display = 'flex';
                        top.style.justifyContent = 'space-between';
                        top.style.gap = '10px';
                        top.style.flexWrap = 'wrap';

                        const left = document.createElement('div');
                        const title = document.createElement('div');
                        title.textContent = it.vehicle_name || 'Vehicle';
                        title.style.fontWeight = '900';
                        title.style.color = '#0f172a';
                        const ref = document.createElement('div');
                        ref.textContent = `Reference: ${it.reference || it.id}`;
                        ref.style.marginTop = '4px';
                        ref.style.color = '#64748b';
                        ref.style.fontWeight = '800';
                        left.appendChild(title);
                        left.appendChild(ref);

                        const badge = document.createElement('div');
                        badge.textContent = it.status || '—';
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
                        const fromStr = from ? from.toLocaleString() : '—';
                        const toStr = to ? to.toLocaleString() : '—';
                        meta.textContent = `From: ${fromStr} | To: ${toStr}`;

                        const meta2 = document.createElement('div');
                        meta2.style.marginTop = '6px';
                        meta2.style.color = '#334155';
                        meta2.style.fontWeight = '700';
                        meta2.textContent = `Pickup: ${it.pickup_location || '—'} | Destination: ${(it.municipality || '—') + ', ' + (it.province || '—')}`;

                        const meta3 = document.createElement('div');
                        meta3.style.marginTop = '10px';
                        meta3.style.background = '#ffffff';
                        meta3.style.border = '1px solid #e2e8f0';
                        meta3.style.borderRadius = '12px';
                        meta3.style.padding = '12px';
                        meta3.style.color = '#0f172a';
                        meta3.style.fontWeight = '800';
                        meta3.style.lineHeight = '1.6';

                        const days = Number(it.days || 0);
                        const fee = Number(it.estimated_service_fee || 0);
                        const feeText = new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(fee);
                        const renterAddress = (it.renter_address || '').trim();
                        meta3.innerHTML = `
                            <div style="display:grid; gap: 6px;">
                                <div><span style="color:#64748b; font-weight:900;">Renter:</span> ${it.renter_name || '—'}</div>
                                <div><span style="color:#64748b; font-weight:900;">Address:</span> ${renterAddress ? renterAddress : '—'}</div>
                                <div><span style="color:#64748b; font-weight:900;">Estimated Service Fee:</span> ₱${feeText}</div>
                                <div><span style="color:#64748b; font-weight:900;">Number of Days:</span> ${days > 0 ? days : '—'}</div>
                            </div>
                        `;

                        card.appendChild(top);
                        card.appendChild(meta);
                        card.appendChild(meta2);
                        card.appendChild(meta3);

                        modalBody.appendChild(card);
                    });
                }

                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            };

            const toLocalInput = (dt) => {
                const y = dt.getFullYear();
                const m = pad(dt.getMonth() + 1);
                const d = pad(dt.getDate());
                const h = pad(dt.getHours());
                const mi = pad(dt.getMinutes());
                return `${y}-${m}-${d}T${h}:${mi}`;
            };

            const openOwnerBooking = (dateStr, isEdit = false, editingData = null) => {
                if (!ownerModal || !ownerVehicleEl || !ownerFromEl || !ownerToEl) return;
                
                if (isEdit && editingData) {
                    editingOwnerBookingId = editingData.id;
                    ownerVehicleEl.value = String(editingData.vehicle_id);
                    ownerFromEl.value = editingData.from ? editingData.from.slice(0, 16) : '';
                    ownerToEl.value = editingData.to ? editingData.to.slice(0, 16) : '';
                    if (ownerNoteEl) ownerNoteEl.value = editingData.note || '';
                    if (estEl) estEl.value = editingData.estimated_service_fee || '';
                    if (actEl) actEl.value = editingData.actual_service_fee || '';
                    if (sameEl) sameEl.checked = false;
                    if (actEl) actEl.readOnly = false;
                    if (ownerSaveBtn) ownerSaveBtn.textContent = 'Save Changes';
                } else {
                    editingOwnerBookingId = null;
                    const activeVehicleId = Number(vehicleSel.value || 0);
                    if (activeVehicleId > 0) ownerVehicleEl.value = String(activeVehicleId);

                    const d = parseDateStr(dateStr || toDateStr(new Date()));
                    const base = d ? new Date(d.getFullYear(), d.getMonth(), d.getDate()) : new Date();
                    const from = new Date(base.getFullYear(), base.getMonth(), base.getDate(), 8, 0);
                    const to = new Date(base.getFullYear(), base.getMonth(), base.getDate(), 17, 0);
                    ownerFromEl.value = toLocalInput(from);
                    ownerToEl.value = toLocalInput(to);
                    if (ownerNoteEl) ownerNoteEl.value = '';
                    if (estEl) estEl.value = '';
                    if (actEl) actEl.value = '';
                    if (sameEl) sameEl.checked = false;
                    if (actEl) actEl.readOnly = false;
                    if (ownerSaveBtn) ownerSaveBtn.textContent = 'Create';
                }

                if (ownerErrEl) { ownerErrEl.style.display = 'none'; ownerErrEl.textContent = ''; }
                ownerModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            };

            const closeOwnerBooking = () => {
                if (!ownerModal) return;
                ownerModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            };

            const saveOwnerBooking = async () => {
                if (!ownerVehicleEl || !ownerFromEl || !ownerToEl || !ownerSaveBtn) return;
                const vehicleId = Number(ownerVehicleEl.value || 0);
                if (vehicleId <= 0) return;
                const fromVal = ownerFromEl.value;
                const toVal = ownerToEl.value;
                if (!fromVal || !toVal) {
                    if (ownerErrEl) { ownerErrEl.textContent = 'Start and end date/time are required.'; ownerErrEl.style.display = 'block'; }
                    return;
                }

                ownerSaveBtn.disabled = true;
                if (ownerErrEl) { ownerErrEl.style.display = 'none'; ownerErrEl.textContent = ''; }

                const isEdit = editingOwnerBookingId !== null;
                const endpoint = isEdit 
                    ? @json(route('booking.calendar.owner-bookings.update', ['rental' => '__ID__'])).replace('__ID__', editingOwnerBookingId)
                    : @json(route('booking.calendar.owner-bookings.store'));

                try {
                    const res = await fetch(endpoint, {
                        method: isEdit ? 'PUT' : 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        },
                        body: JSON.stringify({
                            vehicle_id: vehicleId,
                            datetime_from: fromVal,
                            datetime_to: toVal,
                            estimated_price: estEl && estEl.value ? estEl.value : null,
                            actual_price: actEl && actEl.value ? actEl.value : null,
                            note: ownerNoteEl ? (ownerNoteEl.value || null) : null,
                        }),
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || !data.ok) {
                        const errs = data && data.errors ? data.errors : null;
                        let first = '';
                        if (errs && typeof errs === 'object') {
                            const k = Object.keys(errs)[0];
                            if (k && Array.isArray(errs[k]) && errs[k][0]) first = errs[k][0];
                        }
                        if (ownerErrEl) { ownerErrEl.textContent = first || (data.message || 'Failed to create owner booking.'); ownerErrEl.style.display = 'block'; }
                        return;
                    }
                    closeOwnerBooking();
                    await refresh();
                } catch (e) {
                    if (ownerErrEl) { ownerErrEl.textContent = 'Failed to create owner booking.'; ownerErrEl.style.display = 'block'; }
                } finally {
                    ownerSaveBtn.disabled = false;
                }
            };

            const render = () => {
                const range = getViewRange();
                label.textContent = range.label;

                const byDay = buildByDay();

                grid.innerHTML = '';
                const weekday = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                weekday.forEach((w) => {
                    const h = document.createElement('div');
                    h.textContent = w;
                    h.style.fontWeight = '900';
                    h.style.color = '#64748b';
                    h.style.textAlign = 'center';
                    h.style.padding = '8px 0';
                    grid.appendChild(h);
                });

                const cur = new Date(range.start.getFullYear(), range.start.getMonth(), range.start.getDate());
                const end = new Date(range.end.getFullYear(), range.end.getMonth(), range.end.getDate());
                while (cur <= end) {
                    const ds = toDateStr(cur);
                    const cell = document.createElement('button');
                    cell.type = 'button';
                    cell.style.border = '1px solid #e2e8f0';
                    cell.style.borderRadius = '12px';
                    cell.style.background = '#ffffff';
                    cell.style.padding = '10px';
                    cell.style.minHeight = mode === 'week' ? '120px' : '110px';
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
                    const list = byDay[ds] || [];
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

                    const shown = list.slice(0, 3);
                    shown.forEach((it) => {
                        const c = statusColor(it.status);
                        const pill = document.createElement('div');
                        pill.textContent = (it.vehicle_name || 'Vehicle') + ' • ' + (it.status || '—');
                        pill.style.padding = '6px 8px';
                        pill.style.borderRadius = '10px';
                        pill.style.background = c.bg;
                        pill.style.border = `1px solid ${c.bd}`;
                        pill.style.color = c.tx;
                        pill.style.fontWeight = '900';
                        pill.style.fontSize = '0.78rem';
                        pill.style.whiteSpace = 'nowrap';
                        pill.style.overflow = 'hidden';
                        pill.style.textOverflow = 'ellipsis';
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
                    cell.addEventListener('click', () => openDetails(ds, list));

                    grid.appendChild(cell);
                    cur.setDate(cur.getDate() + 1);
                }
            };

            const refresh = async () => {
                await fetchEvents();
                render();
            };

            const setMode = (m) => {
                mode = m;
                if (mode === 'month') {
                    monthBtn.className = 'btn btn-primary';
                    weekBtn.className = 'btn btn-outline';
                } else {
                    weekBtn.className = 'btn btn-primary';
                    monthBtn.className = 'btn btn-outline';
                }
                refresh();
            };

            monthBtn.addEventListener('click', () => setMode('month'));
            weekBtn.addEventListener('click', () => setMode('week'));

            prevBtn.addEventListener('click', () => {
                if (mode === 'week') cursor.setDate(cursor.getDate() - 7);
                else cursor = new Date(cursor.getFullYear(), cursor.getMonth() - 1, 1);
                refresh();
            });
            nextBtn.addEventListener('click', () => {
                if (mode === 'week') cursor.setDate(cursor.getDate() + 7);
                else cursor = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 1);
                refresh();
            });
            todayBtn.addEventListener('click', () => {
                cursor = new Date();
                fromEl.value = '';
                toEl.value = '';
                refresh();
            });
            vehicleSel.addEventListener('change', refresh);
            fromEl.addEventListener('change', refresh);
            toEl.addEventListener('change', refresh);

            const startPolling = () => {
                if (poll) clearInterval(poll);
                poll = setInterval(() => {
                    if (document.hidden) return;
                    refresh();
                }, 30000);
            };

            if (addOwnerBtn) addOwnerBtn.addEventListener('click', () => openOwnerBooking(toDateStr(new Date())));
            if (quickOwnerBtn) quickOwnerBtn.addEventListener('click', () => openOwnerBooking(activeDayForOwnerBooking || toDateStr(new Date())));
            if (ownerCancelBtn) ownerCancelBtn.addEventListener('click', closeOwnerBooking);
            if (ownerSaveBtn) ownerSaveBtn.addEventListener('click', saveOwnerBooking);

            setMode('month');
            startPolling();
        })();
    </script>
</x-member-layout>
