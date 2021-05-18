<?php

namespace App\Contracts\Services;

interface TagParsingServiceInterface
{
    public function getTagFromCommentContent(string $content): ?string;
}
