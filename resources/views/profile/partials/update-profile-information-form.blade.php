<section>
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<style>
@media (prefers-color-scheme: dark) {
    .dark\:bg-gray-800 {
        background-color: #004d40 !important;
        color: #EDECD7 !important;
        font-family: 'Inika', serif !important;
    }

    #name:focus, #email:focus, button[form="send-verification"]:focus {
        border-color: #004d40;
        box-shadow: 0 0 0 4px rgba(0, 121, 107, 0.5);
        outline: none;
    }
}

.custom-button {
    background-color: #00796b;
    color: #EDECD7;
    border-radius: 20px;
    outline: 2px solid #004d40;
    font-family: Inika, serif;
    text-transform: none;
    transition: background-color 0.3s;
}

.custom-button:hover {
    background-color: #4D8279;
}
</style>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" style="color: #EDECD7; font-weight: bold; font-family: Inika, serif">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" style="color: #EDECD7; font-family: Inika, serif">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" style="color: #EDECD7; font-family: Inika, serif"/>
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name"  style="color: #EDECD7; background-color: #00796b; border: 2px solid #004d40; padding: 15px; border-radius: 20px; outline: none; font-family: Inika, serif" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" style="color: #FC0000; font-family: Inika, serif"/>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" style="color: #EDECD7; font-family: Inika, serif"/>
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" style="color: #EDECD7; background-color: #00796b; border: 2px solid #004d40; padding: 15px; border-radius: 20px; outline: none; font-family: Inika, serif" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" style="color: #FC0000; font-family: Inika, serif"/>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200" style="color: #EDECD7; font-family: Inika, serif">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" style="color: #EDECD7; font-family: Inika, serif">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400" style="color: #EDECD7; font-family: Inika, serif">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
        <x-primary-button class="custom-button">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                    style="color: #EDECD7; font-family: Inika, serif"
                >{{ __('Profile saved.') }}</p>
            @endif
        </div>
    </form>
</section>
