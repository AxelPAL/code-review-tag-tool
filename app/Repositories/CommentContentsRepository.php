<?php

namespace App\Repositories;

use App\Models\CommentContent;
use Illuminate\Support\LazyCollection;

class CommentContentsRepository
{
    public function save(CommentContent $commentContent): bool
    {
        return $commentContent->save();
    }

    public function getAllCommentsContentsWithoutTag(): LazyCollection
    {
        return CommentContent::whereTag(null)->get()->lazy();
    }

    public function findByCommentId(int $id): ?CommentContent
    {
        return CommentContent::whereCommentId($id)->first();
    }
}
