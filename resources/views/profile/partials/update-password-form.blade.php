<section>
    <style>
    #update_password_password:focus {
    border-color: #B1CDAF;
    box-shadow: 0 0 0 4px rgba(177, 205, 175, 0.6);
    outline: none;
    }

    #update_password_current_password:focus {
        border-color: #B1CDAF;
        box-shadow: 0 0 0 4px rgba(177, 205, 175, 0.6);
        outline: none;
    }

    #update_password_password_confirmation:focus {
        border-color: #B1CDAF;
        box-shadow: 0 0 0 4px rgba(177, 205, 175, 0.6);
        outline: none;
    }
    </style>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" style="color: #EDECD7; font-weight: bold">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" style="color: #EDECD7">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" style="color: #EDECD7"/>
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" style="color: #EDECD7; background-color: #678A5C; border: 2px solid transparent; padding: 15px; border-radius: 20px; outline: none;"/>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" style="color: #FC0000"/>
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" style="color: #EDECD7"/>
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" style="color: #EDECD7; background-color: #678A5C; border: 2px solid transparent; padding: 15px; border-radius: 20px; outline: none;"/>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" style="color: #FC0000"/>
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" style="color: #EDECD7"/>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" style="color: #EDECD7; background-color: #678A5C; border: 2px solid transparent; padding: 15px; border-radius: 20px; outline: none;"/>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" style="color: #FC0000"/>
        </div>

        <div class="flex items-center gap-4" >
            <x-primary-button style="background-color: #587353; color: #EDECD7; border-radius: 20px; outline: 2px solid #B1CDAF; text-transform: none;">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                    style="color: #EDECD7"
                >{{ __('Password saved.') }}</p>
            @endif
        </div>
    </form>
</section>
