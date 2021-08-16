<?php

namespace App\Services;

use App\Contracts\Services\PullRequestsServiceInterface;
use App\Models\PullRequest;
use App\Repositories\CommentsRepository;

class PullRequestsService implements PullRequestsServiceInterface
{
    private CommentsRepository $commentsRepository;

    public function __construct(
        CommentsRepository $commentsRepository,
    ) {
        $this->commentsRepository = $commentsRepository;
    }

    public function checkAllCommentsWereDownloadedForPullRequest(PullRequest $pullRequest): bool
    {
        return $this->commentsRepository->getCountByPullRequest($pullRequest->id) >= $pullRequest->comment_count;
    }
}
