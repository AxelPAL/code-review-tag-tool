<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\BitbucketServiceInterface;
use App\Exceptions\NoBitbucketSecretsException;
use App\Exceptions\NoBitbucketTokenException;
use App\Factories\EntitiesFromBitbucketFactory;
use App\Models\PullRequest;
use App\Models\UserBitbucketToken;
use App\Repositories\UserBitbucketTokenRepository;
use Bitbucket\Api\ApiInterface;
use Bitbucket\Api\Repositories\Workspaces;
use Bitbucket\Client;
use Bitbucket\Exception\RuntimeException as BitbucketRuntimeException;
use Bitbucket\ResultPager;
use Generator;
use GrahamCampbell\Bitbucket\BitbucketManager;
use Http;
use Http\Client\Exception;
use Illuminate\Support\Carbon;
use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;

class BitbucketService implements BitbucketServiceInterface
{
    public function __construct(
        public BitbucketManager $bitbucket,
        public EntitiesFromBitbucketFactory $entitiesFromBitbucketFactory,
        public UserBitbucketTokenRepository $bitbucketTokenRepository,
        public SettingsService $settingsService,
        public LoggerInterface $logger
    ) {
    }

    public function getAvailableRepositories(string $workspace): array
    {
        $repositories = [];
        $query        = $this->getQueryForGettingRepositories($workspace);
        try {
            foreach ($this->getAllListPages($query) as $repository) {
                $this->processRepository($repository);
                $repositories[$repository['slug']] = $repository['name'];
            }
        } catch (Exception $e) {
            $this->logger->warning($e->getMessage(), [
                'workspace' => $workspace,
            ]);
        }

        return $repositories;
    }

    protected function getQueryForGettingRepositories(string $workspace): Workspaces
    {
        return $this->bitbucket->repositories()->workspaces($workspace);
    }

    protected function processRepository(array $repository): void
    {
        $this->entitiesFromBitbucketFactory->createRepositoryIfNotExists($repository);
    }

    /**
     * @param ApiInterface $query
     * @param array $params
     * @return Generator
     * @throws Exception
     */
    protected function getAllListPages(ApiInterface $query, array $params = []): Generator
    {
        $client    = $this->getBitbucketClient();
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
        foreach ($this->getAllWorkspaces()['values'] as $workspace) {
            $workspaces[(string)$workspace['slug']] = $workspace['name'];
        }

        return $workspaces;
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getAllWorkspaces(): array
    {
        return $this->bitbucket->currentUser()->listWorkspaces();
    }

    public function getPullRequests(string $workspace, string $repository): array
    {
        $states = [
            PullRequest::OPEN_STATE,
            PullRequest::MERGED_STATE,
        ];
        return $this->getPullRequestsByStates($workspace, $repository, $states);
    }

    public function getActivePullRequests(string $workspace, string $repository): array
    {
        return $this->getPullRequestsByStates($workspace, $repository, [PullRequest::OPEN_STATE]);
    }

    protected function getPullRequestsByStates(string $workspace, string $repository, array $states): array
    {
        $pullRequests = [];
        foreach ($states as $state) {
            $query = $this->bitbucket->repositories()
                                     ->workspaces($workspace)
                                     ->pullRequests($repository);
            try {
                foreach ($this->getAllListPages($query, ['state' => $state]) as $pullRequest) {
                    $this->entitiesFromBitbucketFactory->createPullRequestIfNotExists($pullRequest);
                    $pullRequests[] = $pullRequest;
                }
            } catch (Exception $e) {
                $this->logger->warning($e->getMessage(), [
                    'workspace'  => $workspace,
                    'repository' => $repository,
                    'states'     => $states,
                ]);
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

    public function getAllCommentsOfPullRequest(
        string $workspace,
        string $repository,
        int $pullRequestId
    ): array|Generator {
        $commentsClient = $this->bitbucket->repositories()
                                          ->workspaces($workspace)
                                          ->pullRequests($repository)
                                          ->comments((string)$pullRequestId);
        $return = [];
        try {
            $return = $this->getAllListPages($commentsClient);
        } catch (Exception $e) {
            $this->logger->warning($e->getMessage(), [
                'workspace'     => $workspace,
                'repository'    => $repository,
                'pullRequestId' => $pullRequestId,
            ]);
        }
        return $return;
    }

    public function getOAuthCodeUrl(int $userId): string
    {
        $clientId = $this->settingsService->getBitbucketClientId();
        if ($clientId === null) {
            $redirectUrl = route('specify-credentials');
        } else {
            $redirectUrl = sprintf(
                "https://bitbucket.org/site/oauth2/authorize?client_id=%s&response_type=code",
                $clientId
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
        $clientId     = $this->settingsService->getBitbucketClientId();
        $clientSecret = $this->settingsService->getBitbucketClientSecret();
        if ($clientId === null || $clientSecret === null) {
            throw new LogicException('Bitbucket secrets are not specified!');
        }
        $response = Http::withBasicAuth($clientId, $clientSecret)
                        ->asForm()
                        ->post(
                            'https://bitbucket.org/site/oauth2/access_token',
                            [
                                'grant_type' => 'authorization_code',
                                'code'       => $code,
                            ]
                        );
        $data     = $response->json();
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
            $userBitbucketToken          = new UserBitbucketToken();
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
            $client             = $this->getBitbucketClient();
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

    public function refreshToken(): void
    {
        $clientId     = $this->settingsService->getBitbucketClientId();
        $clientSecret = $this->settingsService->getBitbucketClientSecret();
        if ($clientId === null || $clientSecret === null) {
            throw new NoBitbucketSecretsException(
                'No Bitbucket secrets were specified'
            );
        }
        $userBitbucketToken = $this->bitbucketTokenRepository->findByUserId(BitbucketServiceInterface::ADMIN_USER_ID);
        if ($userBitbucketToken === null) {
            throw new NoBitbucketTokenException(
                'There is no Bitbucket token saved in DB for user ' . BitbucketServiceInterface::ADMIN_USER_ID
            );
        }

        $response = Http::withBasicAuth($clientId, $clientSecret)
                        ->asForm()
                        ->post(
                            'https://bitbucket.org/site/oauth2/access_token',
                            [
                                'grant_type'    => 'refresh_token',
                                'refresh_token' => $userBitbucketToken->refresh_token,
                            ]
                        );
        $data     = $response->json();
        $this->saveBitbucketTokenData($data, BitbucketServiceInterface::ADMIN_USER_ID);
    }
}
