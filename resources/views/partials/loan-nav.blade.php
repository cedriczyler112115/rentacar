<div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; box-shadow: var(--shadow-sm);">
    <div style="font-weight: 900; color: var(--primary); margin-bottom: 12px; display:flex; align-items:center; gap:10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        AARACC Loan Hub
    </div>
    
    <div style="display:flex; flex-direction:column; gap:8px; margin-bottom: 16px;">
        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: #64748b; margin-left: 4px; margin-top: 8px;">My Profile</div>
        <a href="{{ route('loans.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('loans.index') || request()->routeIs('loans.show') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('loans.index') || request()->routeIs('loans.show') ? 'var(--accent)' : 'var(--primary)' }};">
            My Loans
        </a>
        <a href="{{ route('loans.create') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('loans.create') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('loans.create') ? 'var(--accent)' : 'var(--primary)' }};">
            Apply for a Loan
        </a>
    </div>

    @if(auth()->check() && auth()->user()->is_aaracc)
        <div style="display:flex; flex-direction:column; gap:8px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
            <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: #64748b; margin-left: 4px;">Management</div>
            
            <a href="{{ route('admin.loans.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.loans.index') || request()->routeIs('admin.loans.show') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.loans.index') || request()->routeIs('admin.loans.show') ? 'var(--accent)' : 'var(--primary)' }};">
                Loans Dashboard
            </a>
            <a href="{{ route('admin.loan-collections.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.loan-collections.*') || request()->routeIs('admin.loans.payments.*') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.loan-collections.*') || request()->routeIs('admin.loans.payments.*') ? 'var(--accent)' : 'var(--primary)' }};">
                Payment Collection
            </a>
            <a href="{{ route('admin.member-capitals.index') }}" style="display:flex; align-items:center; justify-content:flex-start; padding: 10px 12px; border-radius: 10px; font-weight: 900; border: 1px solid #e2e8f0; background: {{ request()->routeIs('admin.member-capitals.*') ? 'rgba(245,158,11,0.15)' : 'white' }}; color: {{ request()->routeIs('admin.member-capitals.*') ? 'var(--accent)' : 'var(--primary)' }};">
                Member Capitals
            </a>
        </div>
    @endif
</div>
