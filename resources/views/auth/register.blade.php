<x-guest-layout>
<link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
<style>
    .custom-link {
        color: #EDECD7;
        position: absolute;
        left: 37.5%;
        font-family: "Inika", serif;
    }

        .custom-link:hover {
            color: #00796b;
        }
        
        .custom-input {
            background-color: #00796b;
            border: 2px solid #004d40;
            border-radius: 2rem;
            padding: 0.5rem 1rem;
            width: 100%;
            color: #EDECD7;
            font-family: "Inika", serif;
        }

        .custom-input:focus {
            border-color: #004d40;
            box-shadow: 0 0 0 4px rgba(0, 121, 107, 0.5);
            outline: none;
        }

        .custom-button {
            background-color: #00796b;
            color: #EDECD7;
            font-weight: bold;
            padding: 0.5rem 3rem;
            border-radius: 2rem;
            border: 2px solid #004d40;
            transition: background-color 0.3s, box-shadow 0.3s;
            font-family: "Inika", serif;
            text-transform: none;
        }

        .custom-button:hover {
            background-color: #4D8279;
            border-color: #4D8279;
        }

        .custom-button:focus {
            outline: 2px solid #004d40;
            box-shadow: 0 0 0 4px rgba(0, 121, 107, 0.7);
        }

        .form-container {
            background-color: #004d40;
            padding: 2rem;
            border-radius: 2.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-container label {
            color: #EDECD7;
            font-family: "Inika", serif;
        }
</style>
<form method="POST" action="{{ route('register') }}" class="form-container">
    @csrf

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" class="custom-input" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="custom-input" type="email" name="email" :value="old('email')" required autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" class="custom-input" type="password" name="password" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" class="custom-input" type="password" name="password_confirmation" required autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end mt-4">
        <a class="custom-link underline text-sm hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
            {{ __('Already registered?') }}
        </a>

        <x-primary-button class="custom-button ms-4">
            {{ __('Register') }}
        </x-primary-button>
    </div>
</form>
</x-guest-layout>
