<?php

/**
 * @var Report $report
 * @var Carbon $fromDate
 * @var Carbon $toDate
 * @var int $remoteUserId
 * @var array $tags
 * @var array $users
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
                    @foreach($tags as $tag => $count)
                        <th class="border border-purple-300 px-4 py-2 text-grey-900 font-medium font-bold">{{$tag}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                <tr class="bg-gray-200">
                    @foreach($tags as $tag => $count)
                        <td class="border border-purple-200 px-4 py-2 text-grey-800 font-medium">{{$count}}</td>
                    @endforeach
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>