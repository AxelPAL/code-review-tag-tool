<?php

namespace App\Providers;

use App\Contracts\Services\BitbucketServiceInterface;
use App\Contracts\Services\BitbucketUsersServiceInterface;
use App\Contracts\Services\CommentsCollectorServiceInterface;
use App\Contracts\Services\PullRequestsCollectorServiceInterface;
use App\Contracts\Services\ReportAggregatorServiceInterface;
use App\Contracts\Services\TagParsingServiceInterface;
use App\Services\BitbucketService;
use App\Services\BitbucketUsersService;
use App\Services\CommentsCollectorService;
use App\Services\PullRequestsCollectorService;
use App\Services\ReportAggregatorService;
use App\Services\TagParsingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BitbucketServiceInterface::class, BitbucketService::class);
        $this->app->bind(BitbucketUsersServiceInterface::class, BitbucketUsersService::class);
        $this->app->bind(CommentsCollectorServiceInterface::class, CommentsCollectorService::class);
        $this->app->bind(PullRequestsCollectorServiceInterface::class, PullRequestsCollectorService::class);
        $this->app->bind(ReportAggregatorServiceInterface::class, ReportAggregatorService::class);
        $this->app->bind(TagParsingServiceInterface::class, TagParsingService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
