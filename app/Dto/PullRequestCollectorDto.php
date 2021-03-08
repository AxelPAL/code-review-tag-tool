<?php

namespace App\Dto;

use App\Models\PullRequest;
use App\Models\Repository;

class PullRequestCollectorDto
{
    public PullRequest $pullRequest;
    public Repository $repository;
}
