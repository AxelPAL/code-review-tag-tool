<?php

namespace App\Repositories;

use App\Models\UserBitbucketToken;
use Illuminate\Support\Carbon;

class UserBitbucketTokenRepository
{
    public function save(UserBitbucketToken $bitbucketToken): bool
    {
        return $bitbucketToken->save();
    }

    public function findByUserId(int $userId): ?UserBitbucketToken
    {
        return UserBitbucketToken::whereUserId($userId)->first();
    }

    public function existsAndStillActive(int $userId): bool
    {
        $result = false;
        $user = $this->findByUserId($userId);
        if ($user !== null) {
            $result = $user->expires_at >= new Carbon();
        }
        return $result;
    }
}
