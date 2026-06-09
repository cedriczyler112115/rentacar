<x-member-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap: 12px; flex-wrap: wrap;">
            <div>
                <h2>{{ __('Edit Banned Renter') }}</h2>
                <p style="color: #64748b; margin-top: 5px;">Update banned renter record details.</p>
            </div>
            <a href="{{ route('admin.banned-renters.index') }}" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;">← Back</a>
        </div>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <form method="POST" action="{{ route('admin.banned-renters.update', $bannedRenter) }}" enctype="multipart/form-data" style="display:grid; gap: 14px;">
                    @csrf
                    @method('PUT')

                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Fullname</label>
                        <input type="text" name="fullname" value="{{ old('fullname', $bannedRenter->fullname) }}" style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px;" required>
                        @error('fullname') <div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Banned Details & Reason</label>
                        <textarea name="banned_details" rows="6" style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; resize: vertical;" required>{{ old('banned_details', $bannedRenter->banned_details) }}</textarea>
                        @error('banned_details') <div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label style="display:block; font-weight: 900; color:#0f172a; margin-bottom: 6px;">Replace ID Presented (Optional)</label>
                        @if($bannedRenter->id_presented)
                            <div style="margin-bottom: 10px;">
                                <img src="{{ Storage::url($bannedRenter->id_presented) }}" alt="Current ID" style="height: 100px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            </div>
                        @endif
                        <input type="file" name="id_presented" accept="image/*" style="width:100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; background: white;">
                        <div style="font-size: 0.85rem; color:#64748b; margin-top:4px;">Leave empty to keep the current ID photo.</div>
                        @error('id_presented') <div style="margin-top:6px; font-weight:800; color:#b91c1c;">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:flex; gap: 10px; justify-content:flex-end; flex-wrap:wrap; margin-top: 10px;">
                        <a href="{{ route('admin.banned-renters.index') }}" class="btn btn-outline" style="padding: 10px 14px;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 14px;">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-member-layout>
