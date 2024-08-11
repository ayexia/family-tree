<x-guest-layout>
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<style>
        .custom-log-out-button {
            background-color: #587353;
            color: #EDECD7;
            text-decoration: none;
            font-family: 'Inika', serif;
            font-weight: bold;
            padding: 3px 10px;
            border-radius: 20px;
            transition: background-color 0.3s, color 0.3s;
        }

        .custom-log-out-button:hover {
            background-color: #B1CDAF;
            color: #587353;
            font-weight: normal;
        }

        .custom-log-out-button:focus {
            outline: none;
        }
    </style>
<div class="p-6 bg-green-100 border border-green-300 rounded-lg shadow-md" style="background-color: #9BB08C; border-radius:20px">
<div class="mb-4 text-sm text-gray-600 dark:text-gray-400" style="color: #EDECD7; font-family: Inika, serif">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-700" style="font-family: Inika, serif">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button style="background-color: #587353; color: #EDECD7; border-radius: 20px; outline: 2px solid #B1CDAF; font-family: Inika, serif; text-transform: none">
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="custom-log-out-button">
                {{ __('Logout') }}
            </button>
        </form>
    </div>
</x-guest-layout>
