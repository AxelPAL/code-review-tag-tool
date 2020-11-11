<?php
declare(strict_types=1);

namespace App\Domain;

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

    private string $workspace;
    private string $repo;
    private BitbucketManager $bitbucket;
    private Cache $cache;

    public function __construct(BitbucketManager $bitbucket, Cache $cache)
    {
        $this->bitbucket = $bitbucket;
        $this->cache = $cache;
    }

    public function init(string $workspace, string $repo): void
    {
        $this->workspace = $workspace;
        $this->repo = $repo;
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
            $repositories[$repository['slug']] = $repository['name'];
        }

        return $repositories;
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
        $pullRequests = [];
        $states = [
            'OPEN',
            'MERGED',
        ];
        foreach ($states as $state) {
            $query = $this->bitbucket->repositories()
                ->workspaces($workspace)
                ->pullRequests($repository);
            foreach ($this->getAllListPages($query, ['state' => $state]) as $repo) {
                $pullRequests[] = $repo;
            }
        }

        return $pullRequests;
    }

    /**
     * @param int $pullRequestId
     * @return array
     * @throws Exception
     */
    public function getPullRequestData(int $pullRequestId): array
    {
        return $this->bitbucket->repositories()
            ->workspaces($this->workspace)
            ->pullRequests($this->repo)
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