<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('Admin Dashboard') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Real-time overview and quick access to core modules.</p>
            </div>
            <div style="display:flex; gap: 10px; align-items:center; flex-wrap: wrap;">
                <button type="button" id="adminThemeToggle" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem;">
                    Theme
                </button>
                <button type="button" id="adminRefreshBtn" class="btn btn-primary" style="padding: 10px 14px; font-size: 0.95rem;">
                    Refresh
                </button>
            </div>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" id="adminDashboard" data-user-id="{{ (int) auth()->id() }}" data-refresh-seconds="{{ (int) $refreshIntervalSeconds }}" data-theme="light" style="background: var(--dash-bg); border: 1px solid var(--dash-border); border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <style>
                    #adminDashboard {
                        --dash-bg: #ffffff;
                        --dash-border: #e2e8f0;
                        --dash-muted: #64748b;
                        --dash-card: #ffffff;
                        --dash-card-border: #e2e8f0;
                        --dash-card-soft: #f8fafc;
                        --dash-title: #0f172a;
                        --dash-pill: rgba(15, 23, 42, 0.06);
                        --dash-pill-border: rgba(15, 23, 42, 0.12);
                        --dash-warn-bg: #fffbeb;
                        --dash-warn-border: #fef3c7;
                        --dash-warn-text: #d97706;
                        --dash-ok-bg: #f0fdf4;
                        --dash-ok-border: #dcfce7;
                        --dash-ok-text: #16a34a;
                        --dash-bad-bg: #fef2f2;
                        --dash-bad-border: #fee2e2;
                        --dash-bad-text: #dc2626;
                        --dash-neutral-bg: rgba(148, 163, 184, 0.18);
                        --dash-neutral-border: rgba(148, 163, 184, 0.35);
                        --dash-neutral-text: #475569;
                    }
                    #adminDashboard[data-theme="dark"] {
                        --dash-bg: #0b1220;
                        --dash-border: #1e293b;
                        --dash-muted: #94a3b8;
                        --dash-card: #0f172a;
                        --dash-card-border: #1e293b;
                        --dash-card-soft: rgba(255,255,255,0.04);
                        --dash-title: #f8fafc;
                        --dash-pill: rgba(255, 255, 255, 0.08);
                        --dash-pill-border: rgba(255, 255, 255, 0.14);
                        --dash-warn-bg: rgba(245, 158, 11, 0.12);
                        --dash-warn-border: rgba(245, 158, 11, 0.25);
                        --dash-warn-text: #fbbf24;
                        --dash-ok-bg: rgba(22, 163, 74, 0.12);
                        --dash-ok-border: rgba(22, 163, 74, 0.25);
                        --dash-ok-text: #4ade80;
                        --dash-bad-bg: rgba(220, 38, 38, 0.12);
                        --dash-bad-border: rgba(220, 38, 38, 0.25);
                        --dash-bad-text: #f87171;
                        --dash-neutral-bg: rgba(148, 163, 184, 0.12);
                        --dash-neutral-border: rgba(148, 163, 184, 0.25);
                        --dash-neutral-text: #e2e8f0;
                    }
                    #adminDashboard .dash-topbar { display:flex; justify-content:space-between; align-items:center; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
                    #adminDashboard .dash-topbar .meta { display:flex; align-items:center; gap: 10px; flex-wrap: wrap; }
                    #adminDashboard .dash-pill { display:inline-flex; align-items:center; gap:8px; padding: 6px 12px; border-radius: 999px; background: var(--dash-pill); border: 1px solid var(--dash-pill-border); font-weight: 900; color: var(--dash-title); font-size: 0.85rem; }
                    #adminDashboard .dash-section { background: var(--dash-card); border: 1px solid var(--dash-card-border); border-radius: 14px; overflow: hidden; }
                    #adminDashboard .dash-section-h { display:flex; align-items:center; justify-content:space-between; gap: 10px; padding: 14px 16px; background: var(--dash-card-soft); border-bottom: 1px solid var(--dash-card-border); }
                    #adminDashboard .dash-section-t { font-weight: 900; color: var(--dash-title); letter-spacing: 0.2px; }
                    #adminDashboard .dash-section-b { padding: 16px; }
                    #adminDashboard .dash-muted { color: var(--dash-muted); font-weight: 800; }
                    #adminDashboard .dash-loading { opacity: 0.6; }
                    #adminDashboard .metric-grid { display:grid; grid-template-columns: repeat(1, 1fr); gap: 12px; }
                    @media (min-width: 520px) { #adminDashboard .metric-grid { grid-template-columns: repeat(2, 1fr); } }
                    @media (min-width: 768px) { #adminDashboard .metric-grid { grid-template-columns: repeat(3, 1fr); } }
                    @media (min-width: 1200px) { #adminDashboard .metric-grid { grid-template-columns: repeat(5, 1fr); } }
                    #adminDashboard .metric-card { position:relative; border-radius: 14px; padding: 14px; color: #ffffff; overflow:hidden; border: 1px solid rgba(255,255,255,0.14); min-height: 92px; transition: transform .15s ease, box-shadow .15s ease; }
                    #adminDashboard .metric-card:hover { transform: translateY(-2px); box-shadow: 0 18px 30px rgba(0,0,0,0.12); }
                    #adminDashboard[data-theme="dark"] .metric-card:hover { box-shadow: 0 18px 30px rgba(0,0,0,0.35); }
                    #adminDashboard .metric-top { display:flex; align-items:flex-start; justify-content:space-between; gap: 10px; }
                    #adminDashboard .metric-label { font-weight: 900; font-size: 0.82rem; letter-spacing: 0.45px; text-transform: uppercase; opacity: 0.95; }
                    #adminDashboard .metric-value { margin-top: 10px; font-weight: 900; font-size: 1.45rem; letter-spacing: 0.2px; }
                    #adminDashboard .metric-sub { margin-top: 4px; font-weight: 800; font-size: 0.86rem; opacity: 0.95; }
                    #adminDashboard .metric-icon { width: 42px; height: 42px; border-radius: 14px; display:inline-flex; align-items:center; justify-content:center; background: rgba(255,255,255,0.16); border: 1px solid rgba(255,255,255,0.18); flex: 0 0 auto; }
                    #adminDashboard .metric-icon svg { width: 22px; height: 22px; stroke: #ffffff; }
                    #adminDashboard .m-total-veh { background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #f59e0b 140%); }
                    #adminDashboard .m-rented-veh { background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 60%, #0ea5e9 160%); }
                    #adminDashboard .m-pending-veh { background: linear-gradient(135deg, #f59e0b 0%, #d97706 60%, #fb7185 160%); }
                    #adminDashboard .m-rejected { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 60%, #fb7185 160%); }
                    #adminDashboard .m-cancelled { background: linear-gradient(135deg, #64748b 0%, #475569 60%, #0f172a 160%); }
                    #adminDashboard .m-users { background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 60%, #7c3aed 160%); }
                    #adminDashboard .m-members { background: linear-gradient(135deg, #f59e0b 0%, #f97316 60%, #ef4444 160%); }
                    #adminDashboard .m-low-rating { background: linear-gradient(135deg, #ef4444 0%, #f97316 60%, #f59e0b 160%); }
                    #adminDashboard .m-carwash { background: linear-gradient(135deg, #10b981 0%, #059669 60%, #0ea5e9 160%); }
                    #adminDashboard .m-servicefee { background: linear-gradient(135deg, #22c55e 0%, #16a34a 60%, #f59e0b 160%); }
                    #adminDashboard .analytics-grid { display:grid; grid-template-columns: 1fr; gap: 14px; }
                    @media (min-width: 992px) { #adminDashboard .analytics-grid { grid-template-columns: 1fr 1fr; } }
                    #adminDashboard .ops-grid { display:grid; grid-template-columns: 1fr; gap: 14px; }
                    @media (min-width: 992px) { #adminDashboard .ops-grid { grid-template-columns: 1fr 1fr 1fr; } }
                    #adminDashboard .panel-list { display:flex; flex-direction:column; gap: 10px; }
                    #adminDashboard .panel-item { border: 1px solid var(--dash-card-border); border-radius: 14px; padding: 12px; background: var(--dash-card); display:flex; justify-content:space-between; gap: 10px; align-items:flex-start; }
                    #adminDashboard .panel-item .a { font-weight: 900; color: var(--dash-title); }
                    #adminDashboard .panel-item .b { margin-top: 4px; color: var(--dash-muted); font-weight: 800; font-size: 0.85rem; }
                    #adminDashboard .panel-item .right { text-align:right; color: var(--dash-muted); font-weight: 800; font-size: 0.85rem; }
                    #adminDashboard .tag { display:inline-flex; align-items:center; gap:8px; padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 0.82rem; border: 1px solid transparent; }
                    #adminDashboard .tag.ok { background: var(--dash-ok-bg); border-color: var(--dash-ok-border); color: var(--dash-ok-text); }
                    #adminDashboard .tag.warn { background: var(--dash-warn-bg); border-color: var(--dash-warn-border); color: var(--dash-warn-text); }
                    #adminDashboard .tag.bad { background: var(--dash-bad-bg); border-color: var(--dash-bad-border); color: var(--dash-bad-text); }
                    #adminDashboard .tag.neutral { background: var(--dash-neutral-bg); border-color: var(--dash-neutral-border); color: var(--dash-neutral-text); }
                    #adminDashboard .chart-wrap { border: 1px solid var(--dash-card-border); border-radius: 14px; padding: 12px; background: var(--dash-card); }
                    #adminDashboard .chart-title { font-weight: 900; color: var(--dash-title); display:flex; justify-content:space-between; gap: 10px; align-items:flex-end; flex-wrap: wrap; }
                    #adminDashboard .chart-sub { margin-top: 6px; color: var(--dash-muted); font-weight: 800; font-size: 0.85rem; }
                    #adminDashboard .svg-chart svg { width: 100%; height: 220px; display:block; }
                    #adminDashboard .hbar { margin-top: 12px; border: 1px solid var(--dash-card-border); border-radius: 14px; padding: 10px; background: var(--dash-card); }
                    #adminDashboard .hbar-rows { display:flex; flex-direction:column; gap: 10px; }
                    #adminDashboard .hbar-row { display:grid; grid-template-columns: 1.6fr 3fr auto; gap: 10px; align-items:center; }
                    #adminDashboard .hbar-label { min-width: 0; font-weight: 500; color: var(--dash-title); font-size: 0.9rem; overflow:hidden; text-overflow: ellipsis; white-space: nowrap; }
                    #adminDashboard .hbar-axis { display:flex; justify-content:space-between; gap: 10px; color: var(--dash-muted); font-weight: 900; font-size: 0.82rem; margin-bottom: 10px; }
                    #adminDashboard .hbar-bar { height: 12px; border-radius: 999px; background: repeating-linear-gradient(90deg, var(--dash-card-soft) 0, var(--dash-card-soft) 16%, rgba(148,163,184,0.18) 16%, rgba(148,163,184,0.18) 16.8%); border: 1px solid var(--dash-card-border); overflow:hidden; position:relative; }
                    #adminDashboard .hbar-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #f59e0b 0%, #f97316 55%, #ef4444 120%); }
                    #adminDashboard .hbar-val { font-weight: 900; color: var(--dash-muted); font-size: 0.9rem; }
                </style>

                <div class="dash-topbar">
                    <div class="meta">
                        <span class="dash-pill">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                            <span id="dashStatusText">Live</span>
                        </span>
                        <span class="dash-pill">
                            Updated: <span id="dashUpdatedAt">—</span>
                        </span>
                        <span class="dash-pill">
                            Refresh: <span id="dashRefreshSec">{{ (int) $refreshIntervalSeconds }}s</span>
                        </span>
                    </div>
                    <div class="meta">
                        <button type="button" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem;" id="toggleAutoRefreshBtn">Pause Auto Refresh</button>
                    </div>
                </div>

                <div class="dash-section" style="margin-bottom: 14px;">
                    <div class="dash-section-h">
                        <div class="dash-section-t">Summary</div>
                        <div class="dash-muted">Metrics update automatically</div>
                    </div>
                    <div class="dash-section-b">
                        <div class="metric-grid">
                            <div class="metric-card m-total-veh">
                                <div class="metric-top">
                                    <div class="metric-label">Total Vehicles</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M3 13l2-5a4 4 0 0 1 3.7-2.5h6.6A4 4 0 0 1 19 8l2 5"/><path d="M5 13h14"/><path d="M7 18a2 2 0 1 0 0.001 0"/><path d="M17 18a2 2 0 1 0 0.001 0"/><path d="M3 13v5h2"/><path d="M21 13v5h-2"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mTotalVehicles">—</div>
                                <div class="metric-sub">All vehicles</div>
                            </div>
                            <div class="metric-card m-rented-veh">
                                <div class="metric-top">
                                    <div class="metric-label">Rented Vehicles</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M3 7h18"/><path d="M6 7v14"/><path d="M18 7v14"/><path d="M6 11h12"/><path d="M9 15h6"/><path d="M9 19h6"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mRentedVehicles">—</div>
                                <div class="metric-sub">Currently rented</div>
                            </div>
                            <div class="metric-card m-pending-veh">
                                <div class="metric-top">
                                    <div class="metric-label">Pending Vehicles</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 8v5l3 3"/><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mPendingVehicles">—</div>
                                <div class="metric-sub">Waiting availability</div>
                            </div>
                            <div class="metric-card m-rejected">
                                <div class="metric-top">
                                    <div class="metric-label">Rejected Bookings</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mRejectedBookings">—</div>
                                <div class="metric-sub">All rejected</div>
                            </div>
                            <div class="metric-card m-cancelled">
                                <div class="metric-top">
                                    <div class="metric-label">Cancelled Bookings</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10"/><path d="M12 6v6l4 2"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mCancelledBookings">—</div>
                                <div class="metric-sub">All cancelled</div>
                            </div>
                            <div class="metric-card m-users">
                                <div class="metric-top">
                                    <div class="metric-label">Total Users</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><path d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mTotalUsers">—</div>
                                <div class="metric-sub">All accounts</div>
                            </div>
                            <div class="metric-card m-members">
                                <div class="metric-top">
                                    <div class="metric-label">AARACC Members</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 2l3 7 7 .5-5.5 4.5 2 7-6.5-4-6.5 4 2-7L2 9.5 9 9z"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mAaraccMembers">—</div>
                                <div class="metric-sub">is_aaracc users</div>
                            </div>
                            <div class="metric-card m-low-rating">
                                <div class="metric-top">
                                    <div class="metric-label">Owners Below 3★</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 2l3 7 7 .5-5.5 4.5 2 7-6.5-4-6.5 4 2-7L2 9.5 9 9z"/><path d="M7 17l10-10"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mOwnersBelow3">—</div>
                                <div class="metric-sub">Average rating</div>
                            </div>
                            <div class="metric-card m-carwash">
                                <div class="metric-top">
                                    <div class="metric-label">Carwash Charge</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M7 16a4 4 0 0 1 0-8h10a4 4 0 1 1 0 8"/><path d="M6 20h12"/><path d="M8 20v-4"/><path d="M16 20v-4"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mCarwashTotal">—</div>
                                <div class="metric-sub">Total collected</div>
                            </div>
                            <div class="metric-card m-servicefee">
                                <div class="metric-top">
                                    <div class="metric-label">Monthly Service Fee</div>
                                    <div class="metric-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    </div>
                                </div>
                                <div class="metric-value" id="mServiceFeeMonth">—</div>
                                <div class="metric-sub">This month</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dash-section" style="margin-bottom: 14px;">
                    <div class="dash-section-h">
                        <div class="dash-section-t">Analytics</div>
                        <div class="dash-muted">Last 30 days</div>
                    </div>
                    <div class="dash-section-b">
                        <div class="analytics-grid">
                            <div class="chart-wrap">
                                <div class="chart-title">
                                    <span>Daily booking trends</span>
                                    <span class="tag ok">Total: <span id="mBookingsTotalInline">—</span></span>
                                </div>
                                <div class="chart-sub">Bookings created per day with tooltips and scaling.</div>
                                <div class="svg-chart" id="chartBookingsPerDay">Loading…</div>
                            </div>
                            <div class="chart-wrap">
                                <div class="chart-title">
                                    <span>Top 10 most rented items</span>
                                    <span class="dash-muted" id="topCarsRange"></span>
                                </div>
                                <div class="chart-sub">Descending by confirmed/completed bookings.</div>
                                <div class="hbar" id="chartTopItems">Loading…</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dash-section">
                    <div class="dash-section-h">
                        <div class="dash-section-t">Operations</div>
                        <div class="dash-muted">Quick actions and activity</div>
                    </div>
                    <div class="dash-section-b">
                        <div class="ops-grid">
                            <div class="chart-wrap">
                                <div class="chart-title"><span>Quick Access</span><span class="tag neutral">Shortcuts</span></div>
                                <div class="chart-sub">Frequently used admin functions.</div>
                                <div class="panel-list" style="margin-top: 12px;">
                                    @foreach($modules as $m)
                                        @if($m['enabled'] && $m['key'] !== 'dashboard')
                                            <a href="{{ $m['route'] }}" style="text-decoration:none;">
                                                <div class="panel-item">
                                                    <div>
                                                        <div class="a">{{ $m['title'] }}</div>
                                                        <div class="b">{{ $m['description'] }}</div>
                                                    </div>
                                                    <div class="right">
                                                        <span class="tag ok">Open</span>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                    @endforeach
                                    <div style="display:flex; gap: 10px; flex-wrap: wrap;">
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="padding: 10px 14px; font-size: 0.95rem;">Add User</a>
                                        <a href="{{ route('admin.service-fee-payments.index') }}" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem;">Service Fee</a>
                                        <a href="{{ route('admin.carwash-service-payments.index') }}" class="btn btn-outline" style="padding: 10px 14px; font-size: 0.95rem;">Carwash Fee</a>
                                    </div>
                                </div>
                            </div>

                            <div class="chart-wrap">
                                <div class="chart-title"><span>Pending Tasks</span><span class="tag warn">Needs attention</span></div>
                                <div class="chart-sub">Items requiring action.</div>
                                <div class="panel-list" style="margin-top: 12px;">
                                    <div class="panel-item">
                                        <div>
                                            <div class="a">Pending Bookings</div>
                                            <div class="b">Review pending bookings and proceed.</div>
                                        </div>
                                        <div class="right">
                                            <span class="tag warn"><span id="pendingBookingsCount">—</span></span>
                                            <div style="margin-top: 10px;">
                                                <a href="{{ route('admin.dispatching.index') }}" style="text-decoration:none; font-weight: 900; color: var(--accent);">Open Dispatching</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-item">
                                        <div>
                                            <div class="a">Vehicles Status</div>
                                            <div class="b">Availability summary snapshot.</div>
                                        </div>
                                        <div class="right" style="display:flex; flex-direction:column; gap:8px; align-items:flex-end;">
                                            <span class="tag ok">Available: <span id="vehAvailCount">—</span></span>
                                            <span class="tag warn">Pending: <span id="vehPendingCount">—</span></span>
                                            <span class="tag bad">Rented: <span id="vehRentedCount">—</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="chart-wrap">
                                <div class="chart-title"><span>Recent Activity</span><span class="tag neutral">Latest</span></div>
                                <div class="chart-sub">System actions with timestamps.</div>
                                <div class="panel-list" style="margin-top: 12px;" id="recentList">
                                    <div class="dash-muted">Loading…</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const root = document.getElementById('adminDashboard');
            if (!root) return;

            const userId = root.getAttribute('data-user-id') || '0';
            const refreshSeconds = parseInt(root.getAttribute('data-refresh-seconds') || '30', 10);

            const themeKey = 'admin_dash_theme_' + userId;
            const autoKey = 'admin_dash_auto_' + userId;

            const fmtInt = (n) => (typeof n === 'number' ? n.toLocaleString() : '—');
            const fmtMoney = (n) => {
                if (typeof n !== 'number' || Number.isNaN(n)) return '—';
                return '₱' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            function setTheme(theme) {
                root.setAttribute('data-theme', theme);
                localStorage.setItem(themeKey, theme);
            }

            function loadTheme() {
                const theme = localStorage.getItem(themeKey) || 'light';
                setTheme(theme);
            }

            function setLoading(loading) {
                root.classList.toggle('dash-loading', loading);
                const st = document.getElementById('dashStatusText');
                if (st) st.textContent = loading ? 'Loading…' : 'Live';
            }

            function setError(msg) {
                const st = document.getElementById('dashStatusText');
                if (st) st.textContent = msg || 'Offline';
            }

            function renderBookingsChart(points) {
                const el = document.getElementById('chartBookingsPerDay');
                if (!el) return;
                el.innerHTML = '';
                if (!Array.isArray(points) || points.length === 0) {
                    el.innerHTML = '<div class="dash-muted">No data.</div>';
                    return;
                }

                const max = Math.max(1, ...points.map(p => Number(p.count || 0)));
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('viewBox', '0 0 120 60');
                svg.setAttribute('preserveAspectRatio', 'none');

                const padL = 10;
                const padR = 4;
                const padT = 6;
                const padB = 16;
                const w = 120;
                const h = 60;
                const chartW = w - padL - padR;
                const chartH = h - padT - padB;

                const gridLines = 4;
                for (let i = 0; i <= gridLines; i++) {
                    const y = padT + (chartH * i) / gridLines;
                    const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                    line.setAttribute('x1', padL);
                    line.setAttribute('x2', w - padR);
                    line.setAttribute('y1', y);
                    line.setAttribute('y2', y);
                    line.setAttribute('stroke', 'rgba(148,163,184,0.35)');
                    line.setAttribute('stroke-width', '0.4');
                    svg.appendChild(line);

                    const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    label.setAttribute('x', 1.5);
                    label.setAttribute('y', y + 1.5);
                    label.setAttribute('fill', 'rgba(100,116,139,0.9)');
                    label.setAttribute('font-size', '3.2');
                    label.textContent = Math.round(max - (max * i) / gridLines).toString();
                    svg.appendChild(label);
                }

                const gap = chartW / points.length;
                points.forEach((p, i) => {
                    const v = Number(p.count || 0);
                    const barH = (v / max) * chartH;
                    const x = padL + i * gap + gap * 0.15;
                    const bw = gap * 0.7;
                    const y = padT + chartH - barH;
                    const r = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    r.setAttribute('x', x.toFixed(2));
                    r.setAttribute('y', y.toFixed(2));
                    r.setAttribute('width', bw.toFixed(2));
                    r.setAttribute('height', barH.toFixed(2));
                    r.setAttribute('rx', '1.2');
                    r.setAttribute('fill', '#f59e0b');
                    r.setAttribute('opacity', '0.92');
                    const t = document.createElementNS('http://www.w3.org/2000/svg', 'title');
                    t.textContent = (p.date || '') + ': ' + v + ' bookings';
                    r.appendChild(t);
                    svg.appendChild(r);

                    if (i % 5 === 0 || i === points.length - 1) {
                        const tx = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                        tx.setAttribute('x', (x + bw / 2).toFixed(2));
                        tx.setAttribute('y', (padT + chartH + 6).toFixed(2));
                        tx.setAttribute('text-anchor', 'middle');
                        tx.setAttribute('fill', 'rgba(100,116,139,0.9)');
                        tx.setAttribute('font-size', '3.2');
                        tx.textContent = (p.date || '').slice(5);
                        svg.appendChild(tx);
                    }
                });

                el.appendChild(svg);
            }

            function renderTopItems(items) {
                const el = document.getElementById('chartTopItems');
                if (!el) return;
                el.innerHTML = '';

                if (!Array.isArray(items) || items.length === 0) {
                    el.innerHTML = '<div class="dash-muted">No data.</div>';
                    return;
                }

                const max = Math.max(1, ...items.map(i => Number(i.count || 0)));
                const axis = document.createElement('div');
                axis.className = 'hbar-axis';
                axis.innerHTML = '<span>0</span><span>Max: ' + fmtInt(max) + '</span>';
                const wrap = document.createElement('div');
                wrap.className = 'hbar-rows';
                items.forEach((it, idx) => {
                    const row = document.createElement('div');
                    row.className = 'hbar-row';

                    const label = document.createElement('div');
                    label.className = 'hbar-label';
                    const plate = it.license_plate ? (' • ' + it.license_plate) : '';
                    label.textContent = (idx + 1) + '. ' + (it.name || 'N/A') + plate;
                    label.title = (it.name || 'N/A') + plate;

                    const bar = document.createElement('div');
                    bar.className = 'hbar-bar';
                    bar.title = (it.count || 0) + ' bookings';
                    const fill = document.createElement('div');
                    fill.className = 'hbar-fill';
                    fill.style.width = ((Number(it.count || 0) / max) * 100).toFixed(2) + '%';
                    bar.appendChild(fill);

                    const val = document.createElement('div');
                    val.className = 'hbar-val';
                    val.textContent = fmtInt(Number(it.count || 0));

                    row.appendChild(label);
                    row.appendChild(bar);
                    row.appendChild(val);
                    wrap.appendChild(row);
                });

                el.appendChild(axis);
                el.appendChild(wrap);
            }

            async function fetchData() {
                setLoading(true);
                try {
                    const res = await fetch('{{ route('admin.dashboard.data') }}', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const data = await res.json();

                    const up = document.getElementById('dashUpdatedAt');
                    if (up) up.textContent = new Date(data?.meta?.generated_at || Date.now()).toLocaleString();

                    document.getElementById('mTotalVehicles').textContent = fmtInt(data?.kpis?.vehicles_total);
                    document.getElementById('mRentedVehicles').textContent = fmtInt(data?.kpis?.vehicles_rented);
                    document.getElementById('mPendingVehicles').textContent = fmtInt(data?.kpis?.vehicles_pending);
                    document.getElementById('mRejectedBookings').textContent = fmtInt(data?.kpis?.bookings_rejected);
                    document.getElementById('mCancelledBookings').textContent = fmtInt(data?.kpis?.bookings_cancelled);
                    document.getElementById('mTotalUsers').textContent = fmtInt(data?.kpis?.users_total);
                    document.getElementById('mAaraccMembers').textContent = fmtInt(data?.kpis?.members_total);
                    document.getElementById('mOwnersBelow3').textContent = fmtInt(data?.kpis?.owners_below_3);
                    document.getElementById('mCarwashTotal').textContent = fmtMoney(data?.kpis?.carwash_total_amount);
                    document.getElementById('mServiceFeeMonth').textContent = fmtMoney(data?.kpis?.service_fee_this_month);
                    document.getElementById('mBookingsTotalInline').textContent = fmtInt(data?.kpis?.rentals_total);

                    document.getElementById('pendingBookingsCount').textContent = fmtInt(data?.pending?.pending_bookings);

                    const vs = data?.vehicles_status || {};
                    const va = document.getElementById('vehAvailCount');
                    const vp = document.getElementById('vehPendingCount');
                    const vr = document.getElementById('vehRentedCount');
                    if (va) va.textContent = fmtInt(vs.available || 0);
                    if (vp) vp.textContent = fmtInt(vs.pending || 0);
                    if (vr) vr.textContent = fmtInt(vs.rented || 0);

                    const topWrap = document.getElementById('topCarsList');
                    const topRange = document.getElementById('topCarsRange');
                    const top = data?.top_cars || {};
                    if (topRange) topRange.textContent = top.range_days ? ('(Last ' + top.range_days + ' days)') : '';
                    renderTopItems(Array.isArray(top?.items) ? top.items : []);

                    const recentWrap = document.getElementById('recentList');
                    if (recentWrap) {
                        const items = Array.isArray(data?.recent) ? data.recent : [];
                        if (items.length === 0) {
                            recentWrap.innerHTML = '<div class="dash-muted">No recent activity.</div>';
                        } else {
                            recentWrap.innerHTML = '';
                            items.forEach(it => {
                                const row = document.createElement('div');
                                row.className = 'panel-item';
                                const when = it.when ? new Date(it.when).toLocaleString() : '—';
                                const left = document.createElement('div');
                                const a = document.createElement('div');
                                a.className = 'a';
                                a.textContent = (it.user || 'System') + ' • ' + (it.action || 'updated');
                                const b = document.createElement('div');
                                b.className = 'b';
                                b.textContent = [it.booking_ref, it.vehicle].filter(Boolean).join(' • ') || '—';
                                left.appendChild(a);
                                left.appendChild(b);
                                const right = document.createElement('div');
                                right.className = 'right';
                                right.textContent = when;
                                row.appendChild(left);
                                row.appendChild(right);
                                recentWrap.appendChild(row);
                            });
                        }
                    }
                    renderBookingsChart(data?.charts?.rentals_by_day || []);

                    setLoading(false);
                } catch (e) {
                    setLoading(false);
                    setError('Offline');
                }
            }

            let auto = (localStorage.getItem(autoKey) || 'on') === 'on';
            let timer = null;
            function startAuto() {
                if (!auto) return;
                if (timer) clearInterval(timer);
                timer = setInterval(fetchData, refreshSeconds * 1000);
            }
            function stopAuto() {
                if (timer) clearInterval(timer);
                timer = null;
            }
            function updateAutoBtn() {
                const btn = document.getElementById('toggleAutoRefreshBtn');
                if (!btn) return;
                btn.textContent = auto ? 'Pause Auto Refresh' : 'Resume Auto Refresh';
            }

            document.getElementById('toggleAutoRefreshBtn')?.addEventListener('click', () => {
                auto = !auto;
                localStorage.setItem(autoKey, auto ? 'on' : 'off');
                if (auto) startAuto();
                else stopAuto();
                updateAutoBtn();
            });

            document.getElementById('adminRefreshBtn')?.addEventListener('click', fetchData);
            document.getElementById('adminThemeToggle')?.addEventListener('click', () => {
                const cur = root.getAttribute('data-theme') || 'light';
                setTheme(cur === 'dark' ? 'light' : 'dark');
            });

            loadTheme();
            updateAutoBtn();
            fetchData();
            startAuto();
        })();
    </script>
</x-member-layout>
