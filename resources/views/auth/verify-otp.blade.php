<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Verify Your Email</h2>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('We have sent a secure 6-digit One Time Password (OTP) to your inbox. Please enter it below to complete your registration.') }}
        </div>
    </div>

    <!-- Auth Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('otp.verify.post') }}">
        @csrf

        <!-- OTP Code -->
        <div>
            <x-input-label for="otp" :value="__('6-Digit Verification Code')" />
            <x-text-input id="otp" class="block mt-2 w-full text-center tracking-[0.5em] text-3xl font-bold font-mono py-3" 
                            type="text" 
                            name="otp" 
                            maxlength="6"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;"
                            required autofocus autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-8">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('register') }}">
                {{ __('Try registering again?') }}
            </a>

            <x-primary-button class="ms-4 px-8 py-3 text-lg bg-amber-500 hover:bg-amber-600 border-none transition-colors">
                {{ __('Verify Account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
