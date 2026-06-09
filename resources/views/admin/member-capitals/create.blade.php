<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Add Member Capital') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Record a new capital investment.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('partials.loan-nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem; margin-bottom: 14px;">Add Member Capital</div>

                @if ($errors->any())
                    <div style="margin-bottom: 14px; background: rgba(239,68,68,0.12); color:#991b1b; border: 1px solid rgba(239,68,68,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        <ul style="margin:0; padding-left:20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.member-capitals.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label for="user_id" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">AARACC Member</label>
                            <select name="user_id" id="user_id" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" required>
                                <option value="">Select Member...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="amount_added" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Capital Amount Added (₱)</label>
                            <input type="number" name="amount_added" id="amount_added" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" value="{{ old('amount_added') }}" required min="0">
                        </div>

                        <div>
                            <label for="date_invested" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Date Invested</label>
                            <input type="date" name="date_invested" id="date_invested" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" value="{{ old('date_invested', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                        <a href="{{ route('admin.member-capitals.index') }}" class="btn btn-outline" style="padding: 10px 20px;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                            Save Capital Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-member-layout>
