<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\TagParsingServiceInterface;
use JetBrains\PhpStorm\Pure;

class TagParsingService implements TagParsingServiceInterface
{
    public function getTagFromCommentContent(string $content): ?string
    {
        $content = $this->filterTrim($content);
        $content = $this->filterHtmlTags($content);
        $content = $this->filterEmptyBrackets($content);

        return $this->findTag($content);
    }

    #[Pure] private function filterTrim(string $content): string
    {
        return trim($content);
    }

    #[Pure] private function filterHtmlTags(string $content): string
    {
        return strip_tags($content);
    }

    private function filterEmptyBrackets(string $content): string
    {
        return str_replace('[]', '', $content);
    }

    private function findTag(string $content): ?string
    {
        if (!str_starts_with($content, '[')) {
            $tag = null;
        } else {
            preg_match('/\[(.+?)].+/', $content, $matches);
            $tag = $matches[1] ?? null;
        }

        return $tag;
    }
}
