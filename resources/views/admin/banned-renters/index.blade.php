<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('Banned Renters') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Manage users who are banned from renting.</p>
            </div>
            <a href="{{ route('admin.banned-renters.create') }}" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Add Banned Renter</a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                
                <form method="GET" action="{{ route('admin.banned-renters.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom: 14px;">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by fullname..." style="flex:1; min-width: 260px; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 10px;">
                    <button type="submit" class="btn btn-outline" style="padding: 10px 14px; border: 1px solid #e2e8f0; background: white; border-radius: 10px;">Search</button>
                    @if(request('q'))
                        <a href="{{ route('admin.banned-renters.index') }}" class="btn btn-outline" style="padding: 10px 14px; border: 1px solid #e2e8f0; background: white; border-radius: 10px;">Clear</a>
                    @endif
                </form>

                @if(session('success'))
                    <div style="padding: 16px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; color: #065f46; font-weight: 800; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif
                <div style="overflow-x:auto; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align:left; background: #0f172a; color: white;">
                                <th style="padding: 12px 14px; font-weight: 800;">Fullname</th>
                                <th style="padding: 12px 14px; font-weight: 800;">Banned Details & Reason</th>
                                <th style="padding: 12px 14px; font-weight: 800;">ID Presented</th>
                                <th style="padding: 12px 14px; font-weight: 800;">Created By</th>
                                <th style="padding: 12px 14px; font-weight: 800;">Created At</th>
                                <th style="padding: 12px 14px; font-weight: 800; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bannedRenters as $b)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px 14px; font-weight: 900; color:#0f172a;">{{ $b->fullname }}</td>
                                    <td style="padding: 12px 14px; color:#0f172a; font-weight: 700; max-width: 300px;">
                                        {{ $b->banned_details }}
                                    </td>
                                    <td style="padding: 12px 14px;">
                                        @if($b->id_presented)
                                            <a href="javascript:void(0)" onclick="document.getElementById('photoModal_{{ $b->id }}').showModal()" style="display:inline-block;">
                                                <img src="{{ Storage::url($b->id_presented) }}" alt="ID" style="height: 60px; width: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0;">
                                            </a>
                                            <dialog id="photoModal_{{ $b->id }}" style="border:none; border-radius:12px; box-shadow:0 0 20px rgba(0,0,0,0.5); padding:0; max-width: 90vw; max-height: 90vh;">
                                                <div style="padding: 12px 16px; background: white; display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid #e2e8f0;">
                                                    <h3 style="margin:0; font-size:1.1rem; color:#0f172a;">ID Presented for {{ $b->fullname }}</h3>
                                                    <button type="button" onclick="document.getElementById('photoModal_{{ $b->id }}').close()" style="border:none; background:none; font-size:1.8rem; cursor:pointer; color:#64748b; line-height:1;">&times;</button>
                                                </div>
                                                <div style="padding: 16px; display:flex; justify-content:center; background: #f8fafc;">
                                                    <img src="{{ Storage::url($b->id_presented) }}" alt="ID" style="max-height: 75vh; max-width: 100%; object-fit: contain;">
                                                </div>
                                            </dialog>
                                        @else
                                            <span style="color:#64748b;">N/A</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 800; color:#0f172a;">{{ optional($b->creator)->name ?? 'Unknown' }}</td>
                                    <td style="padding: 12px 14px; font-weight: 800; color:#0f172a;">{{ $b->created_at->format('Y-m-d H:i') }}</td>
                                    <td style="padding: 12px 14px; text-align:right;">
                                        <div style="display:inline-flex; gap: 8px; align-items:center;">
                                            <a href="{{ route('admin.banned-renters.edit', $b) }}" class="btn btn-outline" style="padding: 8px 12px;">Edit</a>
                                            <form method="POST" action="{{ route('admin.banned-renters.destroy', $b) }}" class="confirm-delete" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline" style="padding: 8px 12px; border-color:#fecaca; color:#991b1b;">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 40px 14px; text-align:center; color:#64748b; font-weight:700;">No banned renters found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 18px;">
                    {{ $bannedRenters->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </div>
</x-member-layout>
