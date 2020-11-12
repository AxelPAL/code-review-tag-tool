<?php

namespace App\Services;

use App\Models\Repository;
use App\Repositories\RepositoriesRepository;
use Http\Client\Exception;
use Illuminate\Database\Eloquent\Collection;

class PullRequestsCollectorService
{
    private BitbucketService $bitbucketService;
    /**
     * @var RepositoriesRepository
     */
    private RepositoriesRepository $repositoriesRepository;

    public function __construct(
        BitbucketService $bitbucketService,
        RepositoriesRepository $repositoriesRepository
    ) {
        $this->bitbucketService = $bitbucketService;
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
            $pullRequests = $this->bitbucketService->getPullRequests($repository->workspace, $repository->slug);
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
            $pullRequests = $this->bitbucketService->getActivePullRequests($repository->workspace, $repository->slug);
        }

        return $pullRequests;
    }

    /**
     * @return Repository[]|Collection
     */
    protected function getAllRepositories()
    {
        return $this->repositoriesRepository->getAll();
    }
}