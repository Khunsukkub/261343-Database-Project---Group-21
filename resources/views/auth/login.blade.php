<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        {{-- <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div> --}}

        <!-- Password -->
        <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />

        <div class="relative">
            <x-text-input
                id="password"
                class="block mt-1 w-full pr-10"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <button
                type="button"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700"
                data-toggle-password="password"
                aria-label="Show password"
                aria-pressed="false"
            >
                {{-- ไอคอนตา (eye) --}}
                <svg data-eye="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                {{-- ไอคอนตาปิด (eye-off) ซ่อนไว้ก่อน --}}
                <svg data-eye="hide" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 3l18 18"/>
                    <path d="M10.58 10.58A3 3 0 0 0 12 15a3 3 0 0 0 2.42-4.42M9.88 5.09A10.94 10.94 0 0 1 12 5c7 0 11 7 11 7a17.4 17.4 0 0 1-4.23 4.98"/>
                    <path d="M6.1 6.1A17.6 17.6 0 0 0 1 12s4 7 11 7a10.8 10.8 0 0 0 4.1-.8"/>
                </svg>
            </button>
        </div>

        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>