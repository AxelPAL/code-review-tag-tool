<?php

namespace App\Contracts\Services;

use App\Models\Report;
use Carbon\Carbon;

interface ReportAggregatorServiceInterface
{
    public function prepareReport(Carbon $fromDate, Carbon $toDate, int $remoteUserId): Report;
}
