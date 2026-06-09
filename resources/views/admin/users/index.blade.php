<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('List of Users') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Manage user accounts and AARACC access.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px; margin-bottom: 14px;">
                    <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem;">Users</div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Add User</a>
                </div>

                <div class="table-scroll" style="border: 1px solid #e2e8f0; border-radius: 12px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align:left; background: #0f172a; color: white;">
                                <th style="padding: 12px 14px;">User</th>
                                <th style="padding: 12px 14px;">Contact</th>
                                <th style="padding: 12px 14px;">Address</th>
                                <th style="padding: 12px 14px;">AARACC</th>
                                <th style="padding: 12px 14px; text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px 14px;">
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            @if($user->profile_photo_path)
                                                <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Profile Photo" style="width: 34px; height: 34px; border-radius: 999px; object-fit: cover; border: 2px solid rgba(245, 158, 11, 0.5);">
                                            @else
                                                <div style="width: 34px; height: 34px; border-radius: 999px; background: rgba(245, 158, 11, 0.15); border: 2px solid rgba(245, 158, 11, 0.5); display:flex; align-items:center; justify-content:center; font-weight: 900; color: var(--accent);">
                                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div style="font-weight: 900; color:#0f172a;">{{ $user->name }}</div>
                                                <div style="color:#64748b; font-weight: 700; font-size: 0.85rem;">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 12px 14px; font-weight: 800; color:#0f172a;">{{ $user->contact_number ?? '—' }}</td>
                                    <td style="padding: 12px 14px; font-weight: 800; color:#0f172a;">{{ $user->address ?? '—' }}</td>
                                    <td style="padding: 12px 14px;">
                                        @if($user->is_aaracc)
                                            <span style="background: rgba(16,185,129,0.12); color:#065f46; border: 1px solid rgba(16,185,129,0.35); padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 0.8rem;">YES</span>
                                        @else
                                            <span style="background: rgba(148,163,184,0.18); color:#334155; border: 1px solid rgba(148,163,184,0.4); padding: 6px 10px; border-radius: 999px; font-weight: 900; font-size: 0.8rem;">NO</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 14px; text-align:right; white-space: nowrap;">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline" style="padding: 8px 12px; font-size: 0.9rem;">Edit</a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-outline delete-user-btn" data-user-name="{{ $user->name }}" style="padding: 8px 12px; font-size: 0.9rem; border-color:#ef4444; color:#ef4444;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 20px; text-align:center; color:#64748b; font-weight: 800;">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 14px;">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.delete-user-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const form = btn.closest('form');
                    const name = btn.dataset.userName || 'this user';
                    const doDelete = () => form.submit();

                    if (window.$ && $.confirm) {
                        $.confirm({
                            title: 'Delete User',
                            content: 'Delete ' + name + '? This action cannot be undone.',
                            type: 'red',
                            buttons: {
                                Delete: { btnClass: 'btn-red', action: doDelete },
                                Close: function () {},
                            }
                        });
                        return;
                    }

                    if (confirm('Delete ' + name + '? This action cannot be undone.')) {
                        doDelete();
                    }
                });
            });
        });
    </script>
</x-member-layout>
