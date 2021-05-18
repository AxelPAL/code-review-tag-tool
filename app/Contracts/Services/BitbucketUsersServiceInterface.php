<?php

namespace App\Contracts\Services;

use App\Exceptions\SaveException;
use JsonException;

interface BitbucketUsersServiceInterface
{
    /**
     * @param array $pullRequestData
     * @throws JsonException|SaveException
     */
    public function createIfNotExistsFromRequest(array $pullRequestData): void;
}
