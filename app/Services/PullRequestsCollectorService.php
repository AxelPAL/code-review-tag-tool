<?php

namespace App\Services;

use App\Contracts\Services\BitbucketServiceInterface;
use App\Contracts\Services\PullRequestsCollectorServiceInterface;
use App\Contracts\Services\SettingsServiceInterface;
use App\Models\Repository;
use App\Repositories\RepositoriesRepository;
use Http\Client\Exception;
use Illuminate\Database\Eloquent\Collection;

class PullRequestsCollectorService implements PullRequestsCollectorServiceInterface
{
    public function __construct(
        private BitbucketServiceInterface $bitbucketService,
        private RepositoriesRepository $repositoriesRepository,
        private SettingsServiceInterface $settingsService
    ) {
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchAllActivePullRequests(): array
    {
        $this->bitbucketService->init($this->settingsService->getBitbucketRequestsUserId());
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
        $this->bitbucketService->init($this->settingsService->getBitbucketRequestsUserId());
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
