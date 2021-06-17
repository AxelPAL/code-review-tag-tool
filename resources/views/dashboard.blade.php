<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 rounded">
            <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl rounded">
                <div class="md:flex mx-auto px-4 rounded">
                    <div class="p-2">
                        <a href="#" class="block mt-1 text-lg leading-tight font-medium text-black hover:underline">Welcome to Code Review Tag Tool project</a>
                        <p class="mt-2 text-gray-500">In order to get access to tag report, contact your administrator.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
