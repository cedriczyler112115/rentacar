<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Edit User') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Update user details and AARACC access.</p>
    </x-slot>

    <div class="container" style="padding: 0 0 40px 0; margin-left: 20px; margin-right: 20px; width: calc(100% - 40px);">
        <div class="admin-layout">
            <div class="admin-sidebar">
                @include('admin.partials.nav')
            </div>

            <div class="admin-content" style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: var(--shadow-sm);">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data" style="max-width: 720px;">
                    @csrf
                    @method('PUT')

                    <div class="admin-form-grid-2">
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                        <div>
                            <x-input-label for="contact_number" :value="__('Contact Number')" />
                            <x-text-input id="contact_number" name="contact_number" type="text" class="mt-1 block w-full" :value="old('contact_number', $user->contact_number)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
                        </div>
                        <div>
                            <x-input-label for="address" :value="__('Address')" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                        <div>
                            <x-input-label for="password" :value="__('New Password (Optional)')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div style="margin-top: 16px;">
                        <x-input-label for="profile_photo" :value="__('Profile Photo (Optional)')" />
                        <div style="display:flex; align-items:center; gap:12px; margin-top: 8px;">
                            @if($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Profile Photo" style="width: 52px; height: 52px; border-radius: 999px; object-fit: cover; border: 2px solid #e2e8f0;">
                            @else
                                <div style="width: 52px; height: 52px; border-radius: 999px; background: #f1f5f9; border: 2px solid #e2e8f0; display:flex; align-items:center; justify-content:center; color:#0f172a; font-weight: 900;">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="block w-full" />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
                    </div>

                    <div style="margin-top: 16px;">
                        <label style="display:flex; align-items:center; gap:10px; font-weight: 800; color: #0f172a;">
                            <input type="checkbox" name="is_aaracc" value="1" {{ old('is_aaracc', $user->is_aaracc) ? 'checked' : '' }}>
                            AARACC User (Admin Access)
                        </label>
                        <x-input-error class="mt-2" :messages="$errors->get('is_aaracc')" />
                    </div>

                    <div style="display:flex; gap:10px; margin-top: 18px;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 16px; font-size: 0.95rem;">Save Changes</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline" style="padding: 10px 16px; font-size: 0.95rem;">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-member-layout>
