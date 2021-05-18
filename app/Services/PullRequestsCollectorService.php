<?php

namespace App\Services;

use App\Contracts\Services\BitbucketServiceInterface;
use App\Contracts\Services\PullRequestsCollectorServiceInterface;
use App\Models\Repository;
use App\Repositories\RepositoriesRepository;
use Http\Client\Exception;
use Illuminate\Database\Eloquent\Collection;

class PullRequestsCollectorService implements PullRequestsCollectorServiceInterface
{
    private BitbucketServiceInterface $bitbucketService;
    /**
     * @var RepositoriesRepository
     */
    private RepositoriesRepository $repositoriesRepository;

    public function __construct(
        BitbucketServiceInterface $bitbucketService,
        RepositoriesRepository $repositoriesRepository
    ) {
        $this->bitbucketService = $bitbucketService;
        $this->bitbucketService->init(BitbucketServiceInterface::ADMIN_USER_ID);
        $this->repositoriesRepository = $repositoriesRepository;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchAllActivePullRequests(): array
    {
        $pullRequests = [];
        $repositories = $this->getAllRepositories();
        foreach ($repositories as $repository) {
            $pullRequests = $this->bitbucketService->getActivePullRequests($repository->workspace, $repository->slug);
        }

        return $pullRequests;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchAllPullRequests(): array
    {
        $pullRequests = [];
        $repositories = $this->getAllRepositories();
        foreach ($repositories as $repository) {
            $pullRequests[] = $this->bitbucketService->getPullRequests($repository->workspace, $repository->slug);
        }

        return $pullRequests;
    }

    /**
     * @return Repository[]|Collection
     */
    protected function getAllRepositories(): Collection|array
    {
        return $this->repositoriesRepository->getAll();
    }
}
