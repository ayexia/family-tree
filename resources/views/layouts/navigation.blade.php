<style>
    @media (prefers-color-scheme: dark) {
        .dark\:bg-gray-700 {
            --tw-bg-opacity: 0 !important;
            background-color: rgb(55 65 81 / var(--tw-bg-opacity));
        }
        .shadow-lg {
            box-shadow: none !important;
        }
        .ring-opacity-5 {
            --tw-ring-opacity: 0;
        }
    }

    nav {
        height: 120px;
    }

    .max-w-7xl {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .h-9 {
        height: 3rem;
    }

    .text-sm {
        font-size: 1rem;
    }

    .h-4 {
        height: 1.25rem;
    }

    .w-4 {
        width: 1.25rem;
    }

    .h-6 {
        height: 2rem;
    }

    .w-6 {
        width: 2rem;
    }

    .px-3 {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .py-2 {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .ms-6 {
        margin-left: 2rem;
    }

    .logo-container {
        width: 200px;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: -10px;
    }

    .logo-tooltip {
    position: relative;
    display: inline-block;
    }

    .logo-tooltip .tooltip-text {
        visibility: hidden;
        width: 120px;
        background-color: #004d40;
        color: #EDECD7;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1000;
        bottom: -25px;
        left: 33%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        font-family: "Inika", serif;
        font-size: 0.8rem;
    }

    .logo-tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    .logo-tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        bottom: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: transparent transparent #004d40 transparent;
    }
</style>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex-shrink-0 logo-container">
                <a href="{{ route('home') }}" class="logo-tooltip">
                    <x-application-logo class="block w-full h-full fill-current text-gray-800 dark:text-gray-200" />
                    <span class="tooltip-text">Return to Home</span>
                </a>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" style="color: #EDECD7; background-color: #00796b; border-radius: 20px; padding: 0.5rem 1rem; font-size: 0.875rem;">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                    this.closest('form').submit();" class="text-sm py-2">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>