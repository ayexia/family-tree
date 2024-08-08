<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto mt-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Upload GEDCOM File</h3>
                @if (session('success'))
                    <div class="bg-green-500 text-white p-4 mb-4 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="bg-red-500 text-white p-4 mb-4 rounded">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="gedcom_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Choose GEDCOM file</label>
                        <input type="file" name="gedcom_file" id="gedcom_file" accept=".ged" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600 focus:outline-none focus:border-indigo-500">
                        @error('gedcom_file')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded block w-full sm:w-auto">
                            Upload GEDCOM File
                        </button>
                    </div>
                </form>
            </div>
        </div>
            <br><div><a href="{{ route('home') }}" class="btn btn-secondary"><u>Homepage</u></a></br>
             <br><div><a href="{{ route('family.tree') }}" class="btn btn-secondary"><u>View Family Tree</u></a></br>
        </div>
    </div>
</x-app-layout>
