<?php

namespace App\Console\Commands;

use App\Contracts\Services\BitbucketServiceInterface;
use Illuminate\Console\Command;

class RefreshToken extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:refresh-token';

    /**
     * @var string
     */
    protected $description = 'Refresh user\'s token to operate with Bitbucket API';

    /**
     * @return void
     */
    public function __construct(public BitbucketServiceInterface $bitbucketService)
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle()
    {
        $this->bitbucketService->refreshToken();
        return 0;
    }
}
