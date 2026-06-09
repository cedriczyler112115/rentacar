<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('FAQs') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Manage frequently asked questions shown on the booking page.</p>
            </div>
            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Add FAQ</a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <form method="GET" action="{{ route('admin.faqs.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom: 14px;">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Search question / answer" style="flex:1; min-width: 260px; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    <button type="submit" class="btn btn-outline" style="padding: 10px 14px;">Search</button>
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline" style="padding: 10px 14px;">Clear</a>
                </form>

                <div style="overflow-x:auto; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align:left; background: #0f172a; color: white;">
                                <th style="padding: 12px 14px; font-weight: 800;">Order</th>
                                <th style="padding: 12px 14px; font-weight: 800;">Question</th>
                                <th style="padding: 12px 14px; font-weight: 800;">Status</th>
                                <th style="padding: 12px 14px; font-weight: 800; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $f)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px 14px; font-weight: 900; color:#0f172a; width: 90px;">{{ (int) $f->sort_order }}</td>
                                    <td style="padding: 12px 14px; color:#0f172a; font-weight: 800; min-width: 360px;">
                                        <div style="font-weight: 900;">{{ $f->question }}</div>
                                        <div style="margin-top: 6px; color:#64748b; font-weight: 700; max-width: 760px; white-space: nowrap; overflow:hidden; text-overflow: ellipsis;">
                                            {{ $f->answer }}
                                        </div>
                                    </td>
                                    <td style="padding: 12px 14px; width: 140px;">
                                        @if($f->is_active)
                                            <span style="display:inline-flex; align-items:center; gap:8px; padding: 6px 10px; border-radius: 999px; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.25); color:#10b981; font-weight: 900; font-size: 0.85rem;">Active</span>
                                        @else
                                            <span style="display:inline-flex; align-items:center; gap:8px; padding: 6px 10px; border-radius: 999px; background: rgba(148,163,184,0.16); border: 1px solid rgba(148,163,184,0.28); color:#64748b; font-weight: 900; font-size: 0.85rem;">Hidden</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 14px; text-align:right; width: 220px;">
                                        <div style="display:inline-flex; gap: 8px; align-items:center;">
                                            <a href="{{ route('admin.faqs.edit', $f) }}" class="btn btn-outline" style="padding: 10px 12px;">Edit</a>
                                            <form method="POST" action="{{ route('admin.faqs.destroy', $f) }}" class="confirm-delete" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline" style="padding: 10px 12px; border-color:#fecaca; color:#991b1b;">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="padding: 40px 14px; text-align:center; color:#64748b; font-weight:700;">No FAQs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 18px;">
                    {{ $faqs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-member-layout>

