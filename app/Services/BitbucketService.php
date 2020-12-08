<?php
declare(strict_types=1);

namespace App\Services;

use App\Factories\EntitiesFromBitbucketFactory;
use App\Models\PullRequest;
use App\Models\UserBitbucketToken;
use App\Repositories\UserBitbucketSecretsRepository;
use App\Repositories\UserBitbucketTokenRepository;
use Bitbucket\Api\ApiInterface;
use Bitbucket\ResultPager;
use Cache;
use Carbon\Carbon;
use Generator;
use GrahamCampbell\Bitbucket\BitbucketManager;
use Http;
use Http\Client\Exception;
use LogicException;

class BitbucketService
{

    public function __construct(
        private BitbucketManager $bitbucket,
        private Cache $cache,
        private EntitiesFromBitbucketFactory $entitiesFromBitbucketFactory,
        public UserBitbucketTokenRepository $bitbucketTokenRepository,
        public UserBitbucketSecretsRepository $userBitbucketSecretsRepository,
    ) {
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

    public function getOAuthCodeUrl(int $userId): string
    {
        $userSecrets = $this->userBitbucketSecretsRepository->findByUserId($userId);
        if ($userSecrets === null) {
            throw new LogicException('No user secrets have been found for user' . $userId);
        }
        return sprintf(
            "https://bitbucket.org/site/oauth2/authorize?client_id=%s&response_type=code",
            $userSecrets->client_id
        );
    }

    /**
     * @param int $userId
     * @param string $code
     * @return bool
     */
    public function getAndSaveOAuthAccessToken(int $userId, string $code): bool
    {
        $userSecrets = $this->userBitbucketSecretsRepository->findByUserId($userId);
        if ($userSecrets === null) {
            throw new LogicException('No user secrets have been found for user' . $userId);
        }
        $response = Http::withBasicAuth($userSecrets->client_id, $userSecrets->client_secret)
            ->asForm()
            ->post(
                'https://bitbucket.org/site/oauth2/access_token',
                [
                    'grant_type' => 'authorization_code',
                    'code'       => $code,
                ]
            );
        $data = $response->json();
        return $this->saveBitbucketTokenData($data, $userId);
    }

    private function saveBitbucketTokenData(array $data, int $userId): bool
    {
        $userBitbucketToken = $this->bitbucketTokenRepository->findByUserId($userId);
        if ($userBitbucketToken === null) {
            $userBitbucketToken = new UserBitbucketToken();
            $userBitbucketToken->user_id = $userId;
        }
        $expiresIn = (new Carbon())->addSeconds($data['expires_in']);
        $userBitbucketToken->fill($data);
        $userBitbucketToken->expires_at = $expiresIn;
        return $this->bitbucketTokenRepository->save($userBitbucketToken);
    }

}