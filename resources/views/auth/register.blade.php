<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="full_name" :value="__('messages.auth.full_name')" />
            <x-text-input id="full_name" class="block mt-1 w-full" type="text" name="full_name" :value="old('full_name')" required autofocus />
            <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="university_id" :value="__('messages.auth.university_id')" />
            <x-text-input id="university_id" class="block mt-1 w-full" type="text" name="university_id" :value="old('university_id')" required />
            <x-input-error :messages="$errors->get('university_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('messages.auth.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('messages.auth.phone_number')" />
            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('messages.auth.password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('messages.auth.confirm_password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('messages.auth.have_account') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('messages.auth.register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
