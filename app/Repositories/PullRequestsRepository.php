<?php

namespace App\Repositories;

use App\Models\PullRequest;

class PullRequestsRepository
{
    public function save(PullRequest $pullRequest): bool
    {
        return $pullRequest->save();
    }
}
