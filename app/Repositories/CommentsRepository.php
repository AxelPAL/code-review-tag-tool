<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentsRepository
{
    public function save(Comment $comment): bool
    {
        return $comment->save();
    }

    public function findByRemoteId(int $id): ?Comment
    {
        return Comment::whereRemoteId($id)->first();
    }
}
