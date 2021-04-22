<?php

/**
 * @var Report $report
 * @var Carbon $fromDate
 * @var Carbon $toDate
 * @var int $remoteUserId
 * @var array $tags
 * @var array $users
 * @var Comment[] $comments
 */

use App\Models\Report;
use Carbon\Carbon;

?>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <form wire:submit.prevent="prepare">
                <table class="table-auto report-table">
                    <tbody>
                    <tr class="bg-gray-200">
                        <th class="border border-purple-300 px-4 py-2 text-grey-900 font-medium font-bold">
                            <label for="date">From Date</label>
                        </th>
                        <td class="border border-purple-200 px-4 py-2 text-grey-800 font-medium">
                            <x-date-picker wire:model="fromDate" id="fromDate"/>
                        </td>
                        <th class="border border-purple-300 px-4 py-2 text-grey-900 font-medium font-bold">
                            <label for="date">To Date</label>
                        </th>
                        <td class="border border-purple-200 px-4 py-2 text-grey-800 font-medium">
                            <x-date-picker wire:model="toDate" id="toDate"/>
                        </td>
                        <th class="border border-purple-300 px-4 py-2 text-grey-900 font-medium font-bold">
                            <label for="date">userId</label>
                        </th>
                        <td class="border border-purple-200 px-4 py-2 text-grey-800 font-medium">
                            <select name="remoteUserId" id="remoteUserId" wire:model="remoteUserId">
                            @foreach($users as $userId => $userName)
                                <option selected value="{{$userId}}">{{$userName}}</option>
                            @endforeach
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>

            <table class="table-auto report-table">
                <thead>
                <tr>
                    <th class="border border-purple-300 px-4 py-2 text-grey-900 font-medium font-bold bg-gray-100">
                        category
                    </th>
                    @foreach($tags as $tag => $count)
                        <th class="border border-purple-300 px-4 py-2 text-grey-900 font-medium font-bold">
                            <a href="#" wire:click.prevent="$emit('showTagData', '{{$tag}}')">{{$tag}}</a>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr class="bg-gray-200">
                    <th class="border border-purple-300 px-4 py-2 text-grey-900 font-medium font-bold bg-gray-100">
                        count
                    </th>
                    @foreach($tags as $tag => $count)
                        <td class="border border-purple-200 px-4 py-2 text-grey-800 font-medium">
                            <a href="#" wire:click.prevent="$emit('showTagData', '{{$tag}}')">{{$count}}</a>
                        </td>
                    @endforeach
                </tr>
                </tbody>
            </table>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="loading-block" wire:loading>
                    Loading comments...
                </div>
                <div class="comments-block" wire:loading.remove>
                    @foreach($comments as $parentId => $commentsArray)
                        @foreach($commentsArray as $comment)
                            <div class="comment-block">
                                {{$parentId}}
                                <div class="comment-block-image">
                                    <img class="profile-avatar-image" src="{{$comment->remoteUser->avatar}}" alt="{{$comment->remoteUser->display_name}}">
                                    <span class="profile-avatar-name">{{$comment->remoteUser->display_name}}</span>
                                </div>
                                <div class="comment-block-content">{!! $comment->content->html !!}</div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>