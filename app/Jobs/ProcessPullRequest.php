<?php

namespace App\Jobs;

use App\Contracts\Services\CommentsCollectorServiceInterface;
use App\Dto\PullRequestCollectorDto;
use App\Models\PullRequest;
use App\Models\Repository;
use Http\Client\Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JsonException;

class ProcessPullRequest implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected PullRequest $pullRequest;
    protected Repository $repository;

    public function __construct(PullRequestCollectorDto $pullRequestCollectorDto)
    {
        $this->pullRequest = $pullRequestCollectorDto->pullRequest;
        $this->repository = $pullRequestCollectorDto->repository;
    }

    /**
     * Execute the job.
     *
     * @param CommentsCollectorServiceInterface $commentsCollector
     * @return void
     * @throws Exception
     * @throws JsonException
     */
    public function handle(CommentsCollectorServiceInterface $commentsCollector): void
    {
        $commentsCollectorPullRequestDto = new PullRequestCollectorDto();
        $commentsCollectorPullRequestDto->pullRequest = $this->pullRequest;
        $commentsCollectorPullRequestDto->repository = $this->repository;
        $commentsCollector->processPullRequest($commentsCollectorPullRequestDto);
    }
}
