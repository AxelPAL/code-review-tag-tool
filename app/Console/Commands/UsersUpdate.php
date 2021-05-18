<?php

namespace App\Console\Commands;

use App\Contracts\Services\BitbucketServiceInterface;
use Http\Client\Exception;
use Illuminate\Console\Command;

class UsersUpdate extends Command
{
    protected $signature = 'app:users-update'; //@phpstan-ignore-line
    protected $description = 'Update users info'; //@phpstan-ignore-line
    private BitbucketServiceInterface $bitbucketService;

    public function __construct(BitbucketServiceInterface $bitbucketService)
    {
        parent::__construct();
        $this->bitbucketService = $bitbucketService;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        $users = $this->bitbucketService->getUsersInfo();
        $this->output->text("Pull Requests sent to queue:");
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();
        foreach ($users as $user) {
            $this->bitbucketService->updateRemoteUser($user);
            $bar->advance();
        }
        $bar->finish();
        $this->output->text("");
        return 0;
    }
}
