<?php

namespace App\Repositories;

use App\Models\Repository;
use Illuminate\Database\Eloquent\Collection;

class RepositoriesRepository
{
    public function save(Repository $repository): bool
    {
        return $repository->save();
    }

    /**
     * @return Repository[]|Collection
     */
    public function getAll()
    {
        return Repository::all();
    }

    public function findByUUID(string $uuid): ?Repository
    {
        return Repository::whereUuid($uuid)->first();
    }
}
