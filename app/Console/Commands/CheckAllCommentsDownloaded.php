<?php

namespace App\Console\Commands;

use App\Contracts\Services\PullRequestsServiceInterface;
use App\Models\PullRequest;
use App\Repositories\PullRequestsRepository;
use App\Repositories\RepositoriesRepository;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class CheckAllCommentsDownloaded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check whether all comments have been downloaded';

    protected ProgressBar $progressBar;
    private PullRequestsRepository $pullRequestsRepository;
    private RepositoriesRepository $repositoriesRepository;
    private PullRequestsServiceInterface $pullRequestsService;

    public function __construct(
        PullRequestsServiceInterface $pullRequestsService,
        PullRequestsRepository $pullRequestsRepository,
        RepositoriesRepository $repositoriesRepository,
    ) {
        parent::__construct();
        $this->pullRequestsRepository = $pullRequestsRepository;
        $this->repositoriesRepository = $repositoriesRepository;
        $this->pullRequestsService = $pullRequestsService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $prs = [];
        foreach ($this->repositoriesRepository->getAll() as $repo) {
            foreach ($this->pullRequestsRepository->findAllByRepositoryId($repo->id) as $pr) {
                $prs[] = $pr;
            }
        }
        $count = count($prs);
        $this->initProgressBar($count);
        $this->processPrs($prs);
        $processed = $this->progressBar->getProgress();
        $this->output->text("Number of pull requests checked: $processed");
        return 0;
    }


    private function initProgressBar(int $count): void
    {
        $this->progressBar = $this->output->createProgressBar($count);
    }

    /**
     * @param array<int, PullRequest> $prs
     */
    private function processPrs(array $prs): void
    {
        $this->progressBar->start();
        foreach ($prs as $pr) {
            $this->processPr($pr);
        }
        $this->progressBar->finish();
    }

    private function processPr(PullRequest $pullRequest): void
    {
        $allCommentsWereDownloaded = $this
            ->pullRequestsService
            ->checkAllCommentsWereDownloadedForPullRequest($pullRequest);
        if (!$allCommentsWereDownloaded) {
            $this->output->writeln("There are comments for PR #{$pullRequest->id} that should be downloaded.");
        }
        $this->progressBar->advance();
    }
}
