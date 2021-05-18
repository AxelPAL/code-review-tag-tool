<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\BitbucketServiceInterface;
use App\Factories\EntitiesFromBitbucketFactory;
use App\Models\PullRequest;
use App\Models\UserBitbucketToken;
use App\Repositories\UserBitbucketSecretsRepository;
use App\Repositories\UserBitbucketTokenRepository;
use Bitbucket\Api\ApiInterface;
use Bitbucket\Client;
use Bitbucket\ResultPager;
use Generator;
use GrahamCampbell\Bitbucket\BitbucketManager;
use Http;
use Http\Client\Exception;
use Illuminate\Support\Carbon;
use LogicException;
use RuntimeException;
use Bitbucket\Exception\RuntimeException as BitbucketRuntimeException;

class BitbucketService implements BitbucketServiceInterface
{
    public function __construct(
        private BitbucketManager $bitbucket,
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
        $client = $this->getBitbucketClient();
        $paginator = new ResultPager($client);
        return $paginator->fetchAllLazy($query, 'list', [$params]);
    }

    protected function getBitbucketClient(): Client
    {
        return $this->bitbucket->connection($this->bitbucket->getDefaultConnection());
    }

    /**
     * @return array<string, string>
     * @throws Exception
     */
    public function getAvailableWorkspaces(): array
    {
        $workspaces = [];
        foreach ($this->bitbucket->currentUser()->listWorkspaces()['values'] as $workspace) {
            $workspaces[(string)$workspace['slug']] = $workspace['name'];
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
            $redirectUrl = route('specify-credentials');
        } else {
            $redirectUrl = sprintf(
                "https://bitbucket.org/site/oauth2/authorize?client_id=%s&response_type=code",
                $userSecrets->client_id
            );
        }
        return $redirectUrl;
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

    /**
     * @throws Exception
     */
    public function getUsersInfo(): array
    {
        $users = [];
        foreach ($this->getAvailableWorkspaces() as $availableWorkspace => $name) {
            $query = $this->bitbucket->workspaces($availableWorkspace)->members();
            try {
                foreach ($this->getAllListPages($query) as $userInfo) {
                    $users[] = $userInfo['user'];
                }
            } catch (BitbucketRuntimeException) {
            }
        }

        return $users;
    }

    public function updateRemoteUser(array $userInfo): void
    {
        $this->entitiesFromBitbucketFactory->createOrUpdateRemoteUser($userInfo);
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

    public function init(?int $userId): void
    {
        if ($userId !== null) {
            $client = $this->getBitbucketClient();
            $userBitbucketToken = $this->bitbucketTokenRepository->findByUserId($userId);
            if ($userBitbucketToken === null) {
                throw new RuntimeException('Token was not present!');
            }
            $client->authenticate(
                Client::AUTH_OAUTH_TOKEN,
                $userBitbucketToken->access_token
            );
        }
    }
}
