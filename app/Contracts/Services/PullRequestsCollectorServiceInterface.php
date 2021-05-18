<?php

namespace App\Contracts\Services;

use Http\Client\Exception;

interface PullRequestsCollectorServiceInterface
{
    /**
     * @return array
     * @throws Exception
     */
    public function fetchAllActivePullRequests(): array;

    /**
     * @return array
     * @throws Exception
     */
    public function fetchAllPullRequests(): array;
}
