<?php

namespace App\Services;

use App\Contracts\Services\BitbucketServiceInterface;
use App\Contracts\Services\CommentsCollectorServiceInterface;
use App\Dto\PullRequestCollectorDto;
use App\Dto\PullRequestsCollectorDto;
use App\Factories\EntitiesFromBitbucketFactory;
use App\Models\Comment;
use App\Repositories\PullRequestsRepository;
use App\Repositories\RepositoriesRepository;
use Http\Client\Exception;
use JsonException;

class CommentsCollectorService implements CommentsCollectorServiceInterface
{
    /**
     * @var BitbucketServiceInterface
     */
    private BitbucketServiceInterface $bitbucketService;
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
        BitbucketServiceInterface $bitbucketService,
        RepositoriesRepository $repositoriesRepository,
        PullRequestsRepository $pullRequestsRepository,
        EntitiesFromBitbucketFactory $entitiesFromBitbucketFactory
    ) {
        $this->bitbucketService = $bitbucketService;
        $this->repositoriesRepository = $repositoriesRepository;
        $this->pullRequestsRepository = $pullRequestsRepository;
        $this->entitiesFromBitbucketFactory = $entitiesFromBitbucketFactory;
    }

    public function collectAllPullRequestsForProcessing(): PullRequestsCollectorDto
    {
        return $this->collectChosenPullRequestsForProcessing();
    }

    public function collectAllActivePullRequestsForProcessing(): PullRequestsCollectorDto
    {
        return $this->collectChosenPullRequestsForProcessing(true);
    }

    protected function collectChosenPullRequestsForProcessing(bool $onlyActive = false): PullRequestsCollectorDto
    {
        $pullRequestsCollectorDto = new PullRequestsCollectorDto();

        $repositories = $this->repositoriesRepository->getAll();
        foreach ($repositories as $repository) {
            if ($onlyActive) {
                $pullRequests = $this->pullRequestsRepository->findAllActiveByRepositoryId($repository->id);
            } else {
                $pullRequests = $this->pullRequestsRepository->findAllByRepositoryId($repository->id);
            }
            foreach ($pullRequests as $pullRequest) {
                $pullRequestCollectorDto = new PullRequestCollectorDto();
                $pullRequestCollectorDto->pullRequest = $pullRequest;
                $pullRequestCollectorDto->repository = $repository;
                $pullRequestsCollectorDto->pullRequests[] = $pullRequestCollectorDto;
            }
        }

        return $pullRequestsCollectorDto;
    }

    /**
     * @param PullRequestCollectorDto $commentsCollectorPullRequestDto
     * @return Comment[]
     * @throws Exception|JsonException
     */
    public function processPullRequest(PullRequestCollectorDto $commentsCollectorPullRequestDto): array
    {
        $this->bitbucketService->init(BitbucketServiceInterface::ADMIN_USER_ID);
        $processedComments = [];
        $comments = $this->bitbucketService->getAllCommentsOfPullRequest(
            $commentsCollectorPullRequestDto->repository->workspace,
            $commentsCollectorPullRequestDto->repository->slug,
            $commentsCollectorPullRequestDto->pullRequest->remote_id
        );
        foreach ($comments as $commentData) {
            $comment = $this->entitiesFromBitbucketFactory->createCommentIfNotExists($commentData);
            $processedComments[] = $comment;
        }

        return $processedComments;
    }
}
