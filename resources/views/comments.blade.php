<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <table class="common-list table-auto">
                    <thead>
                    <tr>
                        <th class="px-4 py-2">id</th>
                        <th class="px-4 py-2">date</th>
                        <th class="px-4 py-2">author</th>
                        <th class="px-4 py-2">content</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($comments as $key => $comment)
                        <tr class="{{$key % 2 ? 'bg-gray-100' : ''}}">
                            <td class="border px-4 py-2">{{$comment['id']}}</td>
                            <td class="border px-4 py-2">{{$comment['created_on']}}</td>
                            <td class="border px-4 py-2">{{$comment['user']['display_name']}}</td>
                            <td class="border px-4 py-2">{!! $comment['content']['html'] !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
