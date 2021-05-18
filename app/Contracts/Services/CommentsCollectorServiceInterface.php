<?php

namespace App\Contracts\Services;

use App\Dto\PullRequestCollectorDto;
use App\Dto\PullRequestsCollectorDto;
use App\Models\Comment;
use Http\Client\Exception;
use JsonException;

interface CommentsCollectorServiceInterface
{
    public function collectAllPullRequestsForProcessing(): PullRequestsCollectorDto;

    public function collectAllActivePullRequestsForProcessing(): PullRequestsCollectorDto;

    /**
     * @param PullRequestCollectorDto $commentsCollectorPullRequestDto
     * @return Comment[]
     * @throws Exception|JsonException
     */
    public function processPullRequest(PullRequestCollectorDto $commentsCollectorPullRequestDto): array;
}
