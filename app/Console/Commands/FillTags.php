<?php

namespace App\Console\Commands;

use App\Contracts\Services\TagParsingServiceInterface;
use App\Models\CommentContent;
use App\Repositories\CommentContentsRepository;
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
    protected $signature = 'app:fill-tags'; //@phpstan-ignore-line

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill tags for all comments that don\'t have it'; //@phpstan-ignore-line

    protected ProgressBar $progressBar;

    /**
     * @var CommentContentsRepository
     */
    private CommentContentsRepository $commentContentsRepository;

    /**
     * @var TagParsingServiceInterface
     */
    private TagParsingServiceInterface $tagParsingService;

    public function __construct(
        CommentContentsRepository $commentContentsRepository,
        TagParsingServiceInterface $tagParsingService
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

    private function tryToParseAndUpdateCommentContent(CommentContent $dbComment): void
    {
        $dbComment->tag = $this->tagParsingService->getTagFromCommentContent($dbComment->html);
        $this->commentContentsRepository->save($dbComment);
        $this->progressBar->advance();
    }
}
