<x-guest-layout>
    <div class="mb-6 rounded-2xl border border-gray-200/80 bg-gray-50/80 p-4 text-sm leading-6 text-gray-600 dark:border-gray-800 dark:bg-gray-800/60 dark:text-gray-400">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 rounded-2xl border border-success-200/80 bg-success-50/80 p-4 text-sm font-semibold text-success-700 dark:border-success-800/60 dark:bg-success-900/20 dark:text-success-300">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-ui.button variant="primary" type="submit">
                    {{ __('Resend Verification Email') }}
                </x-ui.button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
