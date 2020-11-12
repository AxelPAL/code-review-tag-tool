<?php
declare(strict_types=1);

namespace App\Services;

use App\Factories\EntitiesFromBitbucketFactory;
use App\Models\PullRequest;
use Bitbucket\Api\ApiInterface;
use Bitbucket\ResultPager;
use Cache;
use Generator;
use GrahamCampbell\Bitbucket\BitbucketManager;
use Http;
use Http\Client\Exception;
use Log;

class BitbucketService
{

    private BitbucketManager $bitbucket;
    private Cache $cache;
    private EntitiesFromBitbucketFactory $entitiesFromBitbucketFactory;

    public function __construct(
        BitbucketManager $bitbucket,
        Cache $cache,
        EntitiesFromBitbucketFactory $entitiesFromBitbucketFactory
    ) {
        $this->bitbucket = $bitbucket;
        $this->cache = $cache;
        $this->entitiesFromBitbucketFactory = $entitiesFromBitbucketFactory;
    }

    /**
     * @param string $workspace
     * @return array
     * @throws Exception
     */
    public function getAvailableRepositories(string $workspace): array
    {
        $repositories = [];
        $query = $this->bitbucket->repositories()->workspaces($workspace);
        foreach ($this->getAllListPages($query) as $repository) {
            $this->entitiesFromBitbucketFactory->createRepositoryIfNotExists($repository);
            $repositories[$repository['slug']] = $repository['name'];
        }

        return $repositories;
    }

    /**
     * @param ApiInterface $query
     * @param array $params
     * @return Generator
     * @throws Exception
     */
    protected function getAllListPages(ApiInterface $query, array $params = []): Generator
    {
        $client = $this->bitbucket->connection($this->bitbucket->getDefaultConnection());
        $paginator = new ResultPager($client);
        return $paginator->fetchAllLazy($query, 'list', [$params]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAvailableWorkspaces(): array
    {
        $workspaces = [];
        foreach ($this->bitbucket->currentUser()->listWorkspaces()['values'] as $workspace) {
            $workspaces[$workspace['slug']] = $workspace['name'];
        }

        return $workspaces;
    }

    /**
     * @param string $workspace
     * @param string $repository
     * @return array
     * @throws Exception
     */
    public function getPullRequests(string $workspace, string $repository): array
    {
        $states = [
            PullRequest::OPEN_STATE,
            PullRequest::MERGED_STATE,
        ];
        return $this->getPullRequestsByStates($workspace, $repository, $states);
    }

    /**
     * @param string $workspace
     * @param string $repository
     * @return array
     * @throws Exception
     */
    public function getActivePullRequests(string $workspace, string $repository): array
    {
        return $this->getPullRequestsByStates($workspace, $repository, [PullRequest::OPEN_STATE]);
    }

    /**
     * @param string $workspace
     * @param string $repository
     * @param array $states
     * @return array
     * @throws Exception
     */
    protected function getPullRequestsByStates(string $workspace, string $repository, array $states): array
    {
        $pullRequests = [];
        foreach ($states as $state) {
            $query = $this->bitbucket->repositories()
                ->workspaces($workspace)
                ->pullRequests($repository);
            foreach ($this->getAllListPages($query, ['state' => $state]) as $pullRequest) {
                $this->entitiesFromBitbucketFactory->createPullRequestIfNotExists($pullRequest);
                $pullRequests[] = $pullRequest;
            }
        }

        return $pullRequests;
    }

    /**
     * @param string $workspace
     * @param string $repository
     * @param int $pullRequestId
     * @return array
     * @throws Exception
     */
    public function getPullRequestData(string $workspace, string $repository, int $pullRequestId): array
    {
        return $this->bitbucket->repositories()
            ->workspaces($workspace)
            ->pullRequests($repository)
            ->show((string)$pullRequestId);
    }

    /**
     * @param string $workspace
     * @param string $repository
     * @param int $pullRequestId
     * @return Generator
     * @throws Exception
     */
    public function getAllCommentsOfPullRequest(string $workspace, string $repository, int $pullRequestId): Generator
    {
        $commentsClient = $this->bitbucket->repositories()
            ->workspaces($workspace)
            ->pullRequests($repository)
            ->comments((string)$pullRequestId);
        return $this->getAllListPages($commentsClient);
    }

    public function getOAuthCodeUrl(): string
    {
        return sprintf(
            "https://bitbucket.org/site/oauth2/authorize?client_id=%s&response_type=code",
            env('BITBUCKET_CLIENT_ID')
        );
    }

    /**
     * @param string $code
     * @return bool
     */
    public function getAndSaveOAuthAccessToken(string $code): bool
    {
        $result = false;
        $response = Http::withBasicAuth(env('BITBUCKET_CLIENT_ID'), env('BITBUCKET_CLIENT_SECRET'))
            ->asForm()
            ->post(
                'https://bitbucket.org/site/oauth2/access_token',
                [
                    'grant_type' => 'authorization_code',
                    'code'       => $code,
                ]
            );
        $data = $response->json();
        if (isset($data['access_token'])) {
            config(['bitbucket.connections.main.token' => $data['access_token']]);
            try {
                $result = $this->cache::set('BITBUCKET_TOKEN', $data['access_token'], $data['expires_in']);
            } catch (\Psr\SimpleCache\InvalidArgumentException $e) {
                Log::warning($e->getMessage());
            }
        }
        return $result;
    }

}