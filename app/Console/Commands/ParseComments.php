<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Services\CommentsCollectorService;
use Http\Client\Exception;
use Illuminate\Console\Command;
use JsonException;

class ParseComments extends Command
{
    protected $signature = 'app:parse-comments {--onlyActive}';
    protected $description = 'Parse all new comments';
    private CommentsCollectorService $commentsCollector;

    public function __construct(CommentsCollectorService $commentsCollector)
    {
        $this->commentsCollector = $commentsCollector;
        parent::__construct();
    }

    /**
     * @return int
     * @throws Exception|JsonException
     */
    public function handle(): int
    {
        if ($this->option('onlyActive')) {
            $commentsCollectorDto = $this->commentsCollector->collectAllCommentsFromActivePullRequests();
        } else {
            $commentsCollectorDto = $this->commentsCollector->collectAllCommentsFromPullRequests();
        }
        $this->output->text("Pull Requests:");
        $bar = $this->output->createProgressBar($commentsCollectorDto->totalCount);
        $bar->start();
        $processedCommentsCount = 0;
        foreach ($commentsCollectorDto->pullRequestData as $pullRequestData) {
            $comments = $this->commentsCollector->processPullRequest($pullRequestData);
            $processedCommentsCount += count($comments);
            $bar->advance();
        }
        $bar->finish();
        $this->output->text("Comments processed: $processedCommentsCount");
        return 0;
    }
}
