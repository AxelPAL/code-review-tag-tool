<?php

namespace App\Contracts\Services;

use Generator;
use Http\Client\Exception;

interface BitbucketServiceInterface
{
    public const ADMIN_USER_ID = 1;

    /**
     * @param string $workspace
     * @return array
     * @throws Exception
     */
    public function getAvailableRepositories(string $workspace): array;

    /**
     * @return array<string, string>
     * @throws Exception
     */
    public function getAvailableWorkspaces(): array;

    /**
     * @param string $workspace
     * @param string $repository
     * @return array
     * @throws Exception
     */
    public function getPullRequests(string $workspace, string $repository): array;

    /**
     * @param string $workspace
     * @param string $repository
     * @return array
     * @throws Exception
     */
    public function getActivePullRequests(string $workspace, string $repository): array;

    /**
     * @param string $workspace
     * @param string $repository
     * @param int $pullRequestId
     * @return array
     * @throws Exception
     */
    public function getPullRequestData(string $workspace, string $repository, int $pullRequestId): array;

    /**
     * @param string $workspace
     * @param string $repository
     * @param int $pullRequestId
     * @return array|Generator
     * @throws Exception
     */
    public function getAllCommentsOfPullRequest(
        string $workspace,
        string $repository,
        int $pullRequestId
    ): array|Generator;

    public function getOAuthCodeUrl(int $userId): string;

    /**
     * @param int $userId
     * @param string $code
     * @return bool
     */
    public function getAndSaveOAuthAccessToken(int $userId, string $code): bool;

    /**
     * @throws Exception
     */
    public function getUsersInfo(): array;

    public function updateRemoteUser(array $userInfo): void;

    public function init(?int $userId): void;

    public function refreshToken(): void;
}
