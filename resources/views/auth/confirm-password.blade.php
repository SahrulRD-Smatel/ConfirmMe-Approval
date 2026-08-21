<x-guest-layout>
    <div class="mb-6 rounded-2xl border border-gray-200/80 bg-gray-50/80 p-4 text-sm leading-6 text-gray-600 dark:border-gray-800 dark:bg-gray-800/60 dark:text-gray-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6 flex flex-wrap justify-end gap-3">
            <x-ui.button variant="primary" type="submit">
                {{ __('Confirm') }}
            </x-ui.button>
        </div>
    </form>
</x-guest-layout>
