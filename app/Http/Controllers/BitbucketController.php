<?php

namespace App\Http\Controllers;

use App\Domain\BitbucketService;
use App\Services\BitbucketUsersService;
use Http\Client\Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use JsonException;
use Psr\SimpleCache\InvalidArgumentException;

class BitbucketController extends Controller
{
    /**
     * @var BitbucketService
     */
    private BitbucketService $bitbucketService;
    /**
     * @var BitbucketUsersService
     */
    private BitbucketUsersService $bitbucketUsersService;

    public function __construct(BitbucketService $bitbucketService, BitbucketUsersService $bitbucketUsersService)
    {
        $this->bitbucketService = $bitbucketService;
        $this->bitbucketUsersService = $bitbucketUsersService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $isBitbucketApiKeyAcquired = !empty(config('bitbucket.connections.main.token'));
        return view('index', compact('isBitbucketApiKeyAcquired'));
    }

    /**
     * @param string $account
     * @param string $repo
     * @param int $pullRequestId
     * @return RedirectResponse
     * @throws Exception
     * @throws JsonException
     */
    public function test(string $account, string $repo, int $pullRequestId)
    {
        if (empty(config('bitbucket.connections.main.token'))) {
            return redirect()->route('auth');
        }
        $this->bitbucketService->init($account, $repo);
        /** pull request author and date info */
        $pullRequestData = $this->bitbucketService->getPullRequestData($pullRequestId);
        dump($pullRequestData['author'], $pullRequestData['created_on']);
        $this->bitbucketUsersService->createIfNotExistsFromRequest($pullRequestData);
        /** pull request author and date info */

        /** get all comments of the pull request */
        $comments = $this->bitbucketService->getAllCommentsOfPullRequest($pullRequestId);
        dump($comments);
        /** get all comments of the pull request */

        /** get pull requests */
        $pullRequests = $this->bitbucketService->getAllActivePullRequests();
        dump($pullRequests);
        /** get pull requests */
    }

    public function auth(): RedirectResponse
    {
        return redirect()->away($this->bitbucketService->getOAuthCodeUrl());
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws InvalidArgumentException
     */
    public function receiveOAuthCode(Request $request): RedirectResponse
    {
        $redirectRoute = 'auth';
        if ($this->bitbucketService->getAndSaveOAuthAccessToken($request->get('code'))) {
            $redirectRoute = 'root';
        }
        return redirect()->route($redirectRoute);
    }
}