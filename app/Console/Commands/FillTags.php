<?php

namespace App\Console\Commands;

use App\Models\CommentContent;
use App\Repositories\CommentContentsRepository;
use App\Services\TagParsingService;
use Illuminate\Console\Command;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Console\Helper\ProgressBar;

class FillTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fill-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill tags for all comments that don\'t have it';

    protected ?ProgressBar $progressBar;

    /**
     * @var CommentContentsRepository
     */
    private CommentContentsRepository $commentContentsRepository;

    /**
     * @var TagParsingService
     */
    private TagParsingService $tagParsingService;

    public function __construct(
        CommentContentsRepository $commentContentsRepository,
        TagParsingService $tagParsingService
    ) {
        parent::__construct();
        $this->commentContentsRepository = $commentContentsRepository;
        $this->tagParsingService = $tagParsingService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $dbComments = $this->getAllCommentsContents();
        $count = $dbComments->count();
        $this->initProgressBar($count);
        $this->processComments($dbComments);
        $processed = $this->progressBar->getProgress();
        $this->output->text("Number of written CommentContent tags: $processed");
        return 0;
    }

    /**
     * @return LazyCollection
     */
    private function getAllCommentsContents(): LazyCollection
    {
        return $this->commentContentsRepository->getAllCommentsContentsWithoutTag();
    }

    private function initProgressBar(int $count): void
    {
        $this->progressBar = $this->output->createProgressBar($count);
    }

    private function processComments(LazyCollection $dbComments): void
    {
        $this->progressBar->start();
        foreach ($dbComments as $dbComment) {
            $this->tryToParseAndUpdateCommentContent($dbComment);
        }
        $this->progressBar->finish();
    }

    private function tryToParseAndUpdateCommentContent(CommentContent $dbComment): bool
    {
        $dbComment->tag = $this->tagParsingService->getTagFromCommentContent($dbComment->html);
        $return = $this->commentContentsRepository->save($dbComment);
        $this->progressBar->advance();

        return $return;
    }
}
