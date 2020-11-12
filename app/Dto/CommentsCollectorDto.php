<?php

namespace App\Dto;

class CommentsCollectorDto
{
    public int $totalCount = 0;

    /** @var CommentsCollectorPullRequestDto[] */
    public array $pullRequestData = [];
}
