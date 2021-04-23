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

            @if (!empty($tags))
            <table class="table-auto report-table">
                <thead>
                <tr>
                    <th class="border border-purple-300 px-4 py-2 text-grey-900 font-medium font-bold bg-gray-100">
                        category
                    </th>
                    @foreach($tags as $tag => $count)
                        <th class="border border-purple-300 px-4 py-2 text-grey-900 font-medium font-bold">
                            <a href="#" wire:click="loadingComments" wire:click.prevent="$emit('showTagData', '{{$tag}}')">{{$tag}}</a>
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
                            <a href="#" wire:click="loadingComments" wire:click.prevent="$emit('showTagData', '{{$tag}}')">{{$count}}</a>
                        </td>
                    @endforeach
                </tr>
                </tbody>
            </table>
            @else
                <div class="p-5 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    No comments for selected time period
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-5 m-5 loading-block" wire:loading wire:target="loadingComments">
                    Loading comments...
                </div>
                <div class="comments-block" wire:loading.remove>
                    @foreach($comments as $parentId => $commentsArray)
                        @foreach($commentsArray as $comment)
                            <div class="comment-block p-5 m-5">
                                @if ($comment['remote_user'] !== null)
                                    <div class="comment-block-image">
                                        <img class="profile-avatar-image" src="{{$comment['remote_user']['avatar']}}" alt="{{$comment['remote_user']['display_name']}}">
                                        <span class="profile-avatar-name">{{$comment['remote_user']['display_name']}}</span>
                                    </div>
                                @endif
                                <div class="comment-block-content">{!! $comment['content']['html'] !!}</div>
                                @foreach($comment['children'] as $childComment)
                                    <div class="comment-block-children m-0 pl-2">
                                        <div class="comment-block-image">
                                            <img class="profile-avatar-image" src="{{$childComment['remote_user']['avatar']}}" alt="{{$childComment['remote_user']['display_name']}}">
                                            <span class="profile-avatar-name">{{$childComment['remote_user']['display_name']}}</span>
                                        </div>
                                        <div class="comment-block-content">{!! $childComment['content']['html'] !!}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>