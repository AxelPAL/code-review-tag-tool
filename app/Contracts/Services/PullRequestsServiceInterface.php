<?php

namespace App\Contracts\Services;

use App\Models\PullRequest;

interface PullRequestsServiceInterface
{
    public function checkAllCommentsWereDownloadedForPullRequest(PullRequest $pullRequest): bool;
}
