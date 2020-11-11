<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pull Requests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <table class="common-list table-auto">
                    <thead>
                    <tr>
                        <th class="px-4 py-2">id</th>
                        <th class="px-4 py-2">title</th>
                        <th class="px-4 py-2">destination branch</th>
                        <th class="px-4 py-2">author</th>
                        <th class="px-4 py-2">state</th>
                        <th class="px-4 py-2">comments count</th>
                        <th class="px-4 py-2">description</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pullRequests as $key => $pullRequest)
                        <tr class="{{$key % 2 ? 'bg-gray-100' : ''}}">
                            <td class="border px-4 py-2"><a href="{{route('comments', ['pullRequestId' => $pullRequest['id'], 'workspace' => $workspace, 'repository' => $repository])}}">{{$pullRequest['id']}}</a></td>
                            <td class="border px-4 py-2"><a href="{{route('comments', ['pullRequestId' => $pullRequest['id'], 'workspace' => $workspace, 'repository' => $repository])}}">{{$pullRequest['title']}}</a></td>
                            <td class="border px-4 py-2">{{$pullRequest['destination']['branch']['name']}}</td>
                            <td class="border px-4 py-2">{{$pullRequest['author']['display_name']}}</td>
                            <td class="border px-4 py-2">{{$pullRequest['state']}}</td>
                            <td class="border px-4 py-2">{{$pullRequest['comment_count']}}</td>
                            <td class="border px-4 py-2">{{$pullRequest['description']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
