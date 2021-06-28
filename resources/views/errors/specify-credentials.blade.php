<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Credentials have to be specified') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg box-border p-4 border-4 border-gray-400 bg-gray-200">
                Your administrator should set BitBucket secrets.
            </div>
        </div>
    </div>
</x-app-layout>
