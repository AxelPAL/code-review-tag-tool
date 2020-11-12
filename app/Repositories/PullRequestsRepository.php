<?php

namespace App\Repositories;

use App\Models\PullRequest;
use Illuminate\Database\Eloquent\Collection;

class PullRequestsRepository
{
    public function save(PullRequest $pullRequest): bool
    {
        return $pullRequest->save();
    }

    /**
     * @param int $repositoryId
     * @return PullRequest[]|Collection
     */
    public function findAllByRepositoryId(int $repositoryId)
    {
        return PullRequest::whereRepositoryId($repositoryId)->getModels();
    }

    public function findByRemoteId(int $remoteId): ?PullRequest
    {
        return PullRequest::whereRemoteId($remoteId)->first();
    }
}
