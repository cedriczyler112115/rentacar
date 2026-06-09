<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('Add FAQ') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Create a new FAQ entry for the booking page.</p>
            </div>
            <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;">← Back</a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <form method="POST" action="{{ route('admin.faqs.store') }}" style="display:grid; gap: 14px;">
                    @csrf

                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Question</label>
                        <input type="text" name="question" value="{{ old('question') }}" style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px;">
                        @error('question') <div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Answer</label>
                        <textarea name="answer" rows="10" style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; resize: vertical;">{{ old('answer') }}</textarea>
                        @error('answer') <div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:flex; gap: 12px; flex-wrap: wrap; align-items:center;">
                        <div style="min-width: 180px;">
                            <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px;">
                            @error('sort_order') <div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div> @enderror
                        </div>
                        <label style="display:flex; gap: 10px; align-items:center; cursor:pointer; margin-top: 28px;">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--accent);">
                            <span style="font-weight: 900; color:#0f172a;">Active</span>
                        </label>
                    </div>

                    <div style="display:flex; gap: 10px; justify-content:flex-end; flex-wrap:wrap;">
                        <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline" style="padding: 10px 14px;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 14px;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-member-layout>

