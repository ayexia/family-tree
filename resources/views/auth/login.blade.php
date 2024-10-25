<x-guest-layout>
    <link href="https://fonts.googleapis.com/css2?family=Inika:wght@400;700&family=Tourney:ital,wght@0,100..900;1,100..900&family=Waiting+for+the+Sunrise&display=swap" rel="stylesheet">
    <style>
        .form-container {
            background-color: #004d40;
            padding: 2rem;
            border-radius: 2.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-family: "Inika", serif;
            color: #EDECD7;
        }

        .custom-input {
            background-color: #00796b;
            border: 2px solid #004d40;
            border-radius: 2rem;
            padding: 0.5rem 1rem;
            width: 100%;
            color: #EDECD7;
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

        .custom-link {
            color: #EDECD7;
            text-decoration: underline;
        }

        .custom-link:hover {
            color: #00796b;
        }

        .form-footer {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .form-footer {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }
    </style>

    <form method="POST" action="{{ route('login') }}" class="form-container">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="custom-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="custom-input block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mb-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded bg-00796b border-0 text-EDECD7 shadow-sm focus:ring-004d40" name="remember">
                <span class="ms-2 text-sm">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="form-footer">
            <div class="flex flex-col gap-2">
                @if (Route::has('password.request'))
                    <a class="custom-link text-sm" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
                <a class="custom-link text-sm" href="{{ route('register') }}">
                    {{ __("Don't have an account?") }}
                </a>
            </div>
            <x-primary-button class="custom-button">
                {{ __('Login') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>