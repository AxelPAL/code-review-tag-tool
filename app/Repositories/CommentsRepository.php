<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentsRepository
{
    public function save(Comment $comment): bool
    {
        return $comment->save();
    }

    public function findById(int $id): ?Comment
    {
        return Comment::whereRemoteId($id)->first();
    }
}
