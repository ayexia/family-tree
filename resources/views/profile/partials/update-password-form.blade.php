<section>
<style>
    #update_password_current_password:focus,
    #update_password_password:focus,
    #update_password_password_confirmation:focus {
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

<header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" style="color: #EDECD7; font-weight: bold; font-family: Inika, serif">
        {{ __('Update Password') }}
    </h2>
    
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" style="color: #EDECD7; font-family: Inika, serif">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>
</header>

<form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
    @csrf
    @method('put')
    
    <div>
        <x-input-label for="update_password_current_password" :value="__('Current Password')" style="color: #EDECD7; font-family: Inika, serif"/>
        <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" style="color: #EDECD7; background-color: #00796b; border: 2px solid #004d40; padding: 15px; border-radius: 20px; outline: none; font-family: Inika, serif"/>
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" style="color: #FC0000; font-family: Inika, serif"/>
    </div>
    
    <div>
        <x-input-label for="update_password_password" :value="__('New Password')" style="color: #EDECD7; font-family: Inika, serif"/>
        <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" style="color: #EDECD7; background-color: #00796b; border: 2px solid #004d40; padding: 15px; border-radius: 20px; outline: none; font-family: Inika, serif"/>
        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" style="color: #FC0000; font-family: Inika, serif"/>
    </div>
    
    <div>
        <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" style="color: #EDECD7; font-family: Inika, serif"/>
        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" style="color: #EDECD7; background-color: #00796b; border: 2px solid #004d40; padding: 15px; border-radius: 20px; outline: none; font-family: Inika, serif"/>
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" style="color: #FC0000; font-family: Inika, serif"/>
    </div>
    
    <div class="flex items-center gap-4">
        <x-primary-button class="custom-button">{{ __('Save') }}</x-primary-button>
        
        @if (session('status') === 'password-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600 dark:text-gray-400"
                style="color: #EDECD7; font-family: Inika, serif"
            >{{ __('Password saved.') }}</p>
        @endif
    </div>
</form>
</section>
