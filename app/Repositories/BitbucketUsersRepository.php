<?php

namespace App\Repositories;

use App\Models\BitbucketUser;

class BitbucketUsersRepository
{
    public function save(BitbucketUser $bitbucketUser): bool
    {
        return $bitbucketUser->save();
    }

    public function fetchBitbucketUserByAccountId(string $accountId): ?BitbucketUser
    {
        return BitbucketUser::whereAccountId($accountId)->first();
    }
}
