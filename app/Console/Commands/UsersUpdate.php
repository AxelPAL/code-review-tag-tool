<?php

namespace App\Console\Commands;

use App\Services\BitbucketService;
use Http\Client\Exception;
use Illuminate\Console\Command;

class UsersUpdate extends Command
{
    protected $signature = 'app:users-update';
    protected $description = 'Update users info';
    private BitbucketService $bitbucketService;

    public function __construct(BitbucketService $bitbucketService)
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
