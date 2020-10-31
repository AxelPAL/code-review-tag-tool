<?php
declare(strict_types=1);

namespace App\Domain;

use Bitbucket\ResultPager;
use Cache;
use GrahamCampbell\Bitbucket\BitbucketManager;
use Http;
use Http\Client\Exception;
use Log;
use Psr\Log\InvalidArgumentException;

class BitbucketService
{

    private string $account;
    private string $repo;
    private BitbucketManager $bitbucket;
    private Cache $cache;

    public function __construct(BitbucketManager $bitbucket, Cache $cache)
    {
        $this->bitbucket = $bitbucket;
        $this->cache = $cache;
    }

    public function init(string $account, string $repo): void
    {
        $this->account = $account;
        $this->repo = $repo;
    }

    /**
     * @param int $pullRequestId
     * @return array
     * @throws Exception
     */
    public function getPullRequestData(int $pullRequestId): array
    {
        return $this->bitbucket->repositories()
            ->workspaces($this->account)
            ->pullRequests($this->repo)
            ->show((string)$pullRequestId);
    }

    /**
     * @param int $pullRequestId
     * @return array
     * @throws Exception
     */
    public function getAllCommentsOfPullRequest(int $pullRequestId): array
    {
        $client = $this->bitbucket->connection($this->bitbucket->getDefaultConnection());
        $paginator = new ResultPager($client);
        $commentsClient = $this->bitbucket->repositories()
            ->workspaces($this->account)
            ->pullRequests($this->repo)
            ->comments((string)$pullRequestId);
        return $paginator->fetchAll($commentsClient, 'list');
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAllActivePullRequests(): array
    {
        $client = $this->bitbucket->connection($this->bitbucket->getDefaultConnection());
        $paginator = new ResultPager($client);
        $pullRequests = $this->bitbucket->repositories()
            ->workspaces($this->account)
            ->pullRequests($this->repo);
        return $paginator->fetchAll($pullRequests, 'list');
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