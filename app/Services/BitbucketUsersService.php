<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\BitbucketUserDto;
use App\Models\BitbucketUser;
use App\Exceptions\SaveException;
use App\Repositories\BitbucketUsersRepository;
use JsonException;

class BitbucketUsersService
{
    /**
     * @var BitbucketUsersRepository
     */
    private BitbucketUsersRepository $bitbucketUsersRepository;

    public function __construct(BitbucketUsersRepository $bitbucketUsersRepository)
    {
        $this->bitbucketUsersRepository = $bitbucketUsersRepository;
    }

    /**
     * @param BitbucketUserDto $bitbucketUserDto
     * @throws JsonException|SaveException
     */
    public function create(BitbucketUserDto $bitbucketUserDto): void
    {
        $bitbucketUser = new BitbucketUser();
        $bitbucketUser->name = $bitbucketUserDto->name;
        $bitbucketUser->nickname = $bitbucketUserDto->nickname;
        $bitbucketUser->uuid = $bitbucketUserDto->uuid;
        $bitbucketUser->link = $bitbucketUserDto->link;
        $bitbucketUser->avatar = $bitbucketUserDto->avatar;
        $bitbucketUser->type = $bitbucketUserDto->type;
        $bitbucketUser->account_id = $bitbucketUserDto->accountId;
        if (!$this->bitbucketUsersRepository->save($bitbucketUser)) {
            throw new SaveException('Cannot save user with params: ' . json_encode($bitbucketUserDto, JSON_THROW_ON_ERROR));
        }
    }

    /**
     * @param array $pullRequestData
     * @throws JsonException|SaveException
     */
    public function createIfNotExistsFromRequest(array $pullRequestData): void
    {
        if (!$this->checkExistence($pullRequestData['author']['account_id'])) {
            $bitbucketUserDto = new BitbucketUserDto();
            $bitbucketUserDto->accountId = $pullRequestData['author']['account_id'];
            $bitbucketUserDto->nickname = $pullRequestData['author']['nickname'];
            $bitbucketUserDto->name = $pullRequestData['author']['display_name'];
            $bitbucketUserDto->type = $pullRequestData['author']['type'];
            $bitbucketUserDto->uuid = $pullRequestData['author']['uuid'];
            $bitbucketUserDto->link = $pullRequestData['author']['links']['html']['href'];
            $bitbucketUserDto->avatar = $pullRequestData['author']['links']['avatar']['href'];
            $this->create($bitbucketUserDto);
        }
    }

    public function checkExistence(string $accountId): bool
    {
        return $this->bitbucketUsersRepository->fetchBitbucketUserByAccountId($accountId) !== null;
    }
}
