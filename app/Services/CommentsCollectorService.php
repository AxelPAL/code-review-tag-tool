<?php

namespace App\Services;

use App\Dto\CommentsCollectorDto;
use App\Dto\CommentsCollectorPullRequestDto;
use App\Factories\EntitiesFromBitbucketFactory;
use App\Models\Comment;
use App\Repositories\PullRequestsRepository;
use App\Repositories\RepositoriesRepository;
use Http\Client\Exception;
use JsonException;

class CommentsCollectorService
{
    /**
     * @var BitbucketService
     */
    private BitbucketService $bitbucketService;
    /**
     * @var RepositoriesRepository
     */
    private RepositoriesRepository $repositoriesRepository;
    /**
     * @var PullRequestsRepository
     */
    private PullRequestsRepository $pullRequestsRepository;
    /**
     * @var EntitiesFromBitbucketFactory
     */
    private EntitiesFromBitbucketFactory $entitiesFromBitbucketFactory;

    public function __construct(
        BitbucketService $bitbucketService,
        RepositoriesRepository $repositoriesRepository,
        PullRequestsRepository $pullRequestsRepository,
        EntitiesFromBitbucketFactory $entitiesFromBitbucketFactory
    ) {
        $this->bitbucketService = $bitbucketService;
        $this->repositoriesRepository = $repositoriesRepository;
        $this->pullRequestsRepository = $pullRequestsRepository;
        $this->entitiesFromBitbucketFactory = $entitiesFromBitbucketFactory;
    }

    public function collectAllCommentsFromPullRequests(): CommentsCollectorDto
    {
        $commentsCollectorDto = new CommentsCollectorDto();

        $repositories = $this->repositoriesRepository->getAll();
        foreach ($repositories as $repository) {
            $pullRequests = $this->pullRequestsRepository->findAllByRepositoryId($repository->id);
            foreach ($pullRequests as $pullRequest) {
                $commentsCollectorPullRequestDto = new CommentsCollectorPullRequestDto();
                $commentsCollectorPullRequestDto->pullRequest = $pullRequest;
                $commentsCollectorPullRequestDto->repository = $repository;
                $commentsCollectorDto->pullRequestData[] = $commentsCollectorPullRequestDto;
                $commentsCollectorDto->totalCount += $pullRequest->comment_count;
            }
        }

        return $commentsCollectorDto;
    }

    public function collectAllCommentsFromActivePullRequests(): CommentsCollectorDto
    {
        $commentsCollectorDto = new CommentsCollectorDto();

        $repositories = $this->repositoriesRepository->getAll();
        foreach ($repositories as $repository) {
            $pullRequests = $this->pullRequestsRepository->findAllActiveByRepositoryId($repository->id);
            foreach ($pullRequests as $pullRequest) {
                $commentsCollectorPullRequestDto = new CommentsCollectorPullRequestDto();
                $commentsCollectorPullRequestDto->pullRequest = $pullRequest;
                $commentsCollectorPullRequestDto->repository = $repository;
                $commentsCollectorDto->pullRequestData[] = $commentsCollectorPullRequestDto;
                $commentsCollectorDto->totalCount += $pullRequest->comment_count;
            }
        }

        return $commentsCollectorDto;
    }

    /**
     * @param CommentsCollectorPullRequestDto $commentsCollectorPullRequestDto
     * @return Comment[]
     * @throws Exception|JsonException
     */
    public function processPullRequest(CommentsCollectorPullRequestDto $commentsCollectorPullRequestDto): array
    {
        $processedComments = [];
        $comments = $this->bitbucketService->getAllCommentsOfPullRequest(
            $commentsCollectorPullRequestDto->repository->workspace,
            $commentsCollectorPullRequestDto->repository->slug,
            $commentsCollectorPullRequestDto->pullRequest->id
        );
        foreach ($comments as $commentData) {
            $comment = $this->entitiesFromBitbucketFactory->createCommentIfNotExists($commentData);
            $processedComments[] = $comment;
        }

        return $processedComments;
    }

}