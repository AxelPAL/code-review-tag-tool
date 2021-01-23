<?php

namespace App\Repositories;

use App\Models\RemoteUser;

class RemoteUsersRepository
{
    public function save(RemoteUser $remoteUser): bool
    {
        return $remoteUser->save();
    }

    public function getAll(): array
    {
        return RemoteUser::get()->all();
    }

    public function findByUUID(string $uuid): ?RemoteUser
    {
        return RemoteUser::whereUuid($uuid)->first();
    }
}
