<?php

namespace App\Repositories;

use App\Models\CommentContent;

class CommentContentsRepository
{
    public function save(CommentContent $commentContent): bool
    {
        return $commentContent->save();
    }

    public function findByCommentId(int $id): ?CommentContent
    {
        return CommentContent::whereCommentId($id)->first();
    }
}
