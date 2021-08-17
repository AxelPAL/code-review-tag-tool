<?php

namespace Tests\Unit;

use App\Contracts\Services\TagParsingServiceInterface;
use Tests\TestCase;

class TagParsingServiceTest extends TestCase
{
    private TagParsingServiceInterface $tagParsingService;

    public function setUp(): void
    {
        parent::setUp();
        $this->tagParsingService = app()->get(TagParsingServiceInterface::class);
    }

    /**
     * @dataProvider tagDataProvider
     * @param string $content
     * @param null|string $tag
     * @return void
     */
    public function testCases(string $content, ?string $tag): void
    {
        $this->assertEquals($tag, $this->tagParsingService->getTagFromCommentContent($content));
    }

    public function tagDataProvider(): array
    {
        return [
            'normal comment'                              => [
                'content' => '[cs] test comment',
                'tag'     => 'cs',
            ],
            'comment with a space at the front'           => [
                'content' => ' [cs] test comment',
                'tag'     => 'cs',
            ],
            'comment without a space after close bracket' => [
                'content' => '[cs]test comment',
                'tag'     => 'cs',
            ],
            'double close bracket'                        => [
                'content' => '[cs]]test comment',
                'tag'     => 'cs',
            ],
            'broken tag brackets 1'                       => [
                'content' => '[cs test comment',
                'tag'     => null,
            ],
            'broken tag brackets 2'                       => [
                'content' => 'cs] test comment',
                'tag'     => null,
            ],
        ];
    }
}
