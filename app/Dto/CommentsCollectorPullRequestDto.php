<?php

namespace App\Dto;

use App\Models\PullRequest;
use App\Models\Repository;

class CommentsCollectorPullRequestDto
{
    public PullRequest $pullRequest;
    public Repository $repository;
}
