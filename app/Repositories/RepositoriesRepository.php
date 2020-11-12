<?php

namespace App\Repositories;

use App\Models\Repository;

class RepositoriesRepository
{
    public function save(Repository $repository): bool
    {
        return $repository->save();
    }
}
