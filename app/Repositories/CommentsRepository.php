<?php

namespace App\Repositories;

use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Support\LazyCollection;

class CommentsRepository
{
    public function save(Comment $comment): bool
    {
        return $comment->save();
    }

    public function findAllByRepositoryDateTimeAndRemoteUserId(
        Carbon $fromDate,
        Carbon $toDate,
        int $remoteUserId
    ): LazyCollection
    {
        $fromDateString = $fromDate->format('Y-m-d H:i:s');
        $toDateString = $toDate->format('Y-m-d H:i:s');

        return Comment::whereRemoteUserId($remoteUserId)
            ->whereBetween('repository_created_at', [$fromDateString, $toDateString])
            ->get()
            ->lazy();
    }

    public function findByRemoteId(int $id): ?Comment
    {
        return Comment::whereRemoteId($id)->first();
    }
}
