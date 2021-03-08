<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPullRequest;
use App\Services\CommentsCollectorService;
use Illuminate\Console\Command;

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

    public function handle(): int
    {
        if ($this->option('onlyActive')) {
            $commentsCollectorDto = $this->commentsCollector->collectAllActivePullRequestsForProcessing();
        } else {
            $commentsCollectorDto = $this->commentsCollector->collectAllPullRequestsForProcessing();
        }
        $this->output->text("Pull Requests sent to queue:");
        $bar = $this->output->createProgressBar(count($commentsCollectorDto->pullRequests));
        $bar->start();
        foreach ($commentsCollectorDto->pullRequests as $pullRequest) {
            ProcessPullRequest::dispatch($pullRequest);
            $bar->advance();
        }
        $bar->finish();
        $this->output->text("");
        return 0;
    }
}
