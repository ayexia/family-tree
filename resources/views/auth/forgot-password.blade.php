<x-guest-layout>
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<style>
    #email:focus {
        border-color: #004d40;
        box-shadow: 0 0 0 4px rgba(0, 121, 107, 0.5);
        outline: none;
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

<div class="mb-4 text-sm text-gray-600 dark:text-gray-400" style="color: #004d40; font-family: Inika, serif">
    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
</div>

<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" style="font-family: Inika, serif"/>

<div class="p-6 bg-green-100 border border-green-300 rounded-lg shadow-md" style="background-color: #004d40; border-radius:20px">
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" style="color: #EDECD7; font-family: Inika, serif"/>
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus style="color: #EDECD7; background-color: #00796b; border: 2px solid #004d40; padding: 15px; border-radius: 20px; outline: none; font-family: Inika, serif"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2" style="color: #FC0000; font-family: Inika, serif" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="custom-button">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</div>
</x-guest-layout>