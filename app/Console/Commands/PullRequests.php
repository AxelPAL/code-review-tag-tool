<?php

namespace App\Console\Commands;

use App\Contracts\Services\PullRequestsCollectorServiceInterface;
use Http\Client\Exception;
use Illuminate\Console\Command;

class PullRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-pull-requests {--onlyActive}'; //@phpstan-ignore-line

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all new pull requests'; //@phpstan-ignore-line


    private PullRequestsCollectorServiceInterface $pullRequestsCollector;

    public function __construct(
        PullRequestsCollectorServiceInterface $pullRequestsCollector
    ) {
        parent::__construct();
        $this->pullRequestsCollector = $pullRequestsCollector;
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        if ($this->option('onlyActive')) {
            $pullRequests = $this->fetchAllActivePullRequests();
        } else {
            $pullRequests = $this->fetchAllPullRequests();
        }
        $count = count($pullRequests);
        $this->output->text("Processed Pull Requests: $count");
        return 0;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchAllActivePullRequests(): array
    {
        return $this->pullRequestsCollector->fetchAllActivePullRequests();
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchAllPullRequests(): array
    {
        return $this->pullRequestsCollector->fetchAllPullRequests();
    }
}
