<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Edit Member Capital') }} - {{ $memberCapital->user->name }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Update an existing capital investment.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('partials.loan-nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <div style="font-weight: 900; color: var(--primary); font-size: 1.1rem; margin-bottom: 14px;">Edit Member Capital</div>

                @if ($errors->any())
                    <div style="margin-bottom: 14px; background: rgba(239,68,68,0.12); color:#991b1b; border: 1px solid rgba(239,68,68,0.35); padding: 10px 14px; border-radius: 8px; font-weight: 800;">
                        <ul style="margin:0; padding-left:20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.member-capitals.update', $memberCapital) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">AARACC Member</label>
                            <input type="text" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #e2e8f0; color: #64748b;" value="{{ $memberCapital->user->name }}" disabled>
                        </div>

                        <div>
                            <label for="current_capital" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Current Capital (₱)</label>
                            <input type="number" name="current_capital" id="current_capital" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" value="{{ old('current_capital', $memberCapital->current_capital) }}" required min="0">
                        </div>

                        <div>
                            <label for="date_invested" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Date Invested</label>
                            <input type="date" name="date_invested" id="date_invested" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" value="{{ old('date_invested', $memberCapital->date_invested) }}" required>
                        </div>

                        <div class="md:col-span-2">
                            <label for="status" style="display:block; font-size: 0.9rem; font-weight: 800; color: #475569; margin-bottom: 6px;">Status</label>
                            <select name="status" id="status" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: #f8fafc; color: #0f172a;" required>
                                <option value="active" {{ (old('status', $memberCapital->status) == 'active') ? 'selected' : '' }}>Active</option>
                                <option value="withdrawn" {{ (old('status', $memberCapital->status) == 'withdrawn') ? 'selected' : '' }}>Withdrawn</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                        <a href="{{ route('admin.member-capitals.index') }}" class="btn btn-outline" style="padding: 10px 20px;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                            Update Capital Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-member-layout>
