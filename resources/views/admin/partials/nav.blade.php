<div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; box-shadow: var(--shadow-sm);">
    <div style="font-weight: 900; color: var(--primary); margin-bottom: 12px; display:flex; align-items:center; gap:10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l9 6v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/><path d="M9 22V12h6v10"/></svg>
        Admin Panel
    </div>
    <div style="display:flex; flex-direction:column; gap:8px;">
        <a href="{{ route('admin.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.index') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.index') ? 'var(--accent)' : 'var(--primary)' }};">
            Dashboard
        </a>
        <a href="{{ route('admin.users.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.users.*') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.users.*') ? 'var(--accent)' : 'var(--primary)' }};">
            List of Users
        </a>
        <a href="{{ route('admin.owner-ratings.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.owner-ratings.*') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.owner-ratings.*') ? 'var(--accent)' : 'var(--primary)' }};">
            Owners Rating
        </a>
        <a href="{{ route('admin.dispatching.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.dispatching.*') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.dispatching.*') ? 'var(--accent)' : 'var(--primary)' }};">
            Dispatching
        </a>
        <a href="{{ route('admin.service-fee-payments.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.service-fee-payments.*') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.service-fee-payments.*') ? 'var(--accent)' : 'var(--primary)' }};">
            Service Fee Payments
        </a>
        <a href="{{ route('admin.carwash-service-payments.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.carwash-service-payments.*') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.carwash-service-payments.*') ? 'var(--accent)' : 'var(--primary)' }};">
            Carwash Service Fee
        </a>
        <a href="{{ route('admin.banned-renters.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.banned-renters.*') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.banned-renters.*') ? 'var(--accent)' : 'var(--primary)' }};">
            Banned Renter
        </a>
        <a href="{{ route('admin.faqs.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.faqs.*') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.faqs.*') ? 'var(--accent)' : 'var(--primary)' }};">
            FAQs
        </a>
    </div>
</div>
