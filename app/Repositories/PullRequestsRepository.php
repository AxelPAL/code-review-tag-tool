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
    public function findAllByRepositoryId(int $repositoryId): Collection|array
    {
        return PullRequest::whereRepositoryId($repositoryId)->getModels();
    }

    /**
     * @param int $repositoryId
     * @return PullRequest[]|Collection
     */
    public function findAllActiveByRepositoryId(int $repositoryId): Collection|array
    {
        return PullRequest::whereRepositoryId($repositoryId)->whereState(PullRequest::OPEN_STATE)->getModels();
    }

    public function findByWebLink(string $webLink): ?PullRequest
    {
        return PullRequest::whereWebLink($webLink)->first();
    }
}
