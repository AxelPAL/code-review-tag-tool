<?php

namespace App\Models;

class Report
{
    public array $tags = [];

    public function addTag(string $tag, int $count): void
    {
        $this->tags[$tag] = $count;
    }
}
