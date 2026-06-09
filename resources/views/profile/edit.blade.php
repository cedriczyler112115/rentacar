<x-member-layout>
    <x-slot name="header">
        <h2>{{ __('Profile') }}</h2>
        <p style="color: #64748b; margin-top: 5px;">Update your personal information, security settings, and manage your account.</p>
    </x-slot>

    <div class="container" style="padding: 0px 20px 40px 20px;">
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <div class="profile-cards-grid" style="display:grid; grid-template-columns: 1fr; gap: 20px;">
                <div class="profile-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="profile-card">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="profile-card">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

            <div style="width: 100%;">
                @include('profile.partials.owner-legitimacy-form')
            </div>

            <div class="profile-card">
                <div class="max-w-xl">
                    @include('profile.partials.cookie-preferences')
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (min-width: 1100px) {
            .profile-cards-grid { grid-template-columns: 1fr 1fr 1fr !important; }
        }
    </style>
</x-member-layout>
