<?php

namespace App\Console\Commands;

use App\Services\CommentsCollectorService;
use Http\Client\Exception;
use Illuminate\Console\Command;

class ParseComments extends Command
{
    protected $signature = 'app:parse-comments';
    protected $description = 'Parse all new comments';
    private CommentsCollectorService $commentsCollector;

    public function __construct(CommentsCollectorService $commentsCollector)
    {
        $this->commentsCollector = $commentsCollector;
        parent::__construct();
    }

    /**
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        $commentsCollectorDto = $this->commentsCollector->collectAllCommentsFromPullRequests();
        $this->output->text("Pull Requests:");
        $bar = $this->output->createProgressBar($commentsCollectorDto->totalCount);
        $bar->start();
        foreach ($commentsCollectorDto->pullRequestData as $pullRequestData) {
            $comments = $this->commentsCollector->processPullRequest($pullRequestData);
            $bar->advance();
        }
        $bar->finish();
        $processedCommentsCount = count($comments);
        $this->output->text("Comments processed: $processedCommentsCount");
        return 0;
    }
}
