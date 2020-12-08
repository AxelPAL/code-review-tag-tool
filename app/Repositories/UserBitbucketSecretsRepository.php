<?php

namespace App\Repositories;

use App\Models\UserBitbucketSecrets;

class UserBitbucketSecretsRepository
{
    public function save(UserBitbucketSecrets $bitbucketToken): bool
    {
        return $bitbucketToken->save();
    }

    public function findByUserId(int $userId): ?UserBitbucketSecrets
    {
        return UserBitbucketSecrets::whereUserId($userId)->first();
    }
}
