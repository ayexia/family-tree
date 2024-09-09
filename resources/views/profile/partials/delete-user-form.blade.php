<section class="space-y-6">
<style>
    #password:focus, #secondary-button:focus {
        border-color: #004d40;
        box-shadow: 0 0 0 4px rgba(0, 121, 107, 0.5);
        outline: none;
    }
    
    #password::placeholder {
        color: #EDECD7;
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
    
    .danger-button {
        background-color: #d32f2f;
        color: #EDECD7;
        border-radius: 20px;
        outline: 2px solid #b71c1c;
        font-family: Inika, serif;
        text-transform: none;
        transition: background-color 0.3s;
    }
    
    .danger-button:hover {
        background-color: #f44336;
    }
</style>

<header>
    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" style="color: #EDECD7; font-weight: bold; font-family: Inika, serif">
        {{ __('Delete Account') }}
    </h2>
    
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" style="color: #EDECD7; font-family: Inika, serif">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>
</header>

<x-danger-button
    x-data=""
    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    class="danger-button"
>{{ __('Delete Account') }}</x-danger-button>

<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('profile.destroy') }}" class="p-6" style="background-color: #004d40; border-radius: 20px;">
        @csrf
        @method('delete')

        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" style="color: #EDECD7; font-weight: bold; font-family: Inika, serif">
            {{ __('Are you sure you want to delete your account?') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" style="color: #EDECD7; font-family: Inika, serif">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
        </p>

        <div class="mt-6">
            <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
            
            <x-text-input
                id="password"
                name="password"
                type="password"
                class="mt-1 block w-3/4"
                placeholder="{{ __('Password') }}"
                style="color: #EDECD7; background-color: #00796b; border: 2px solid #004d40; padding: 15px; border-radius: 20px; outline: none; font-family: Inika, serif"
            />
            
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" style="color: #FC0000; font-family: Inika, serif"/>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')" class="custom-button" style="margin-right: 10px;">
                {{ __('Cancel') }}
            </x-secondary-button>
            
            <x-danger-button class="danger-button">
                {{ __('Delete Account') }}
            </x-danger-button>
        </div>
    </form>
</x-modal>
</section>
