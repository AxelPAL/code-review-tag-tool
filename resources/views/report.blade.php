<?php
/**
 * @var Report $report
 * @var Carbon $fromDate
 * @var Carbon $toDate
 * @var int $userId
 */

use App\Models\Report;
use Carbon\Carbon;
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Report') }}
        </h2>
    </x-slot>

    @livewire('report')
</x-app-layout>
