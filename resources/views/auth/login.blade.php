<x-guest-layout>
    <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
    <style>
        .custom-link {
            color: #EDECD7;
            position: absolute;
            left: 37.5%;
            bottom: 14%;
            font-family: "Inika", serif;
        }

        .custom-link:hover {
            color: #00796b;
        }

        .custom-link-2 {
            color: #EDECD7;
            position: absolute;
            left: 50%;
            bottom: 31%;
            font-family: "Inika", serif;
        }

        .custom-link-2:hover {
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

        .custom-link, .custom-link-2 {
            outline: none;
            box-shadow: none;
        }

        .custom-link:focus, .custom-link-2:focus {
            outline: none;
            box-shadow: none;
        }

        #remember_me {
            outline: none;
            box-shadow: none;
        }

        #remember_me:focus {
            outline: none;
            box-shadow: none;
        }

        #remember_me:checked {
            background-color: #00796b;
            border: 2px solid #00796b;
        }
    </style>

    <form method="POST" action="{{ route('login') }}" class="form-container">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="custom-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="custom-input block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center text-EDECD7 font-Inika">
                <input id="remember_me" type="checkbox" class="rounded bg-00796b border-0 text-EDECD7 shadow-sm focus:ring-004d40" name="remember">
                <span class="ms-2 text-sm text-EDECD7 font-Inika">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="custom-link-2 underline text-sm hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="custom-link underline text-sm hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __("Don't have an account?") }}
                </a>

                <x-primary-button class="custom-button ms-3">
                    {{ __('Login') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>
