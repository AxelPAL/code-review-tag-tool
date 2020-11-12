<?php

namespace App\Http\Controllers;

use App\Services\BitbucketService;
use App\Services\BitbucketUsersService;
use Cache;
use Http\Client\Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitbucketController extends Controller
{
    /**
     * @var BitbucketService
     */
    private BitbucketService $bitbucketService;
    /**
     * @var BitbucketUsersService
     */

    public function __construct(BitbucketService $bitbucketService)
    {
        $this->bitbucketService = $bitbucketService;
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
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     * @throws Exception
     */
    public function workspaces()
    {
        $workspaces = $this->bitbucketService->getAvailableWorkspaces();

        return view('workspaces', compact('workspaces'));
    }

    /**
     * @param string $workspace
     * @return Application|Factory|RedirectResponse|View
     * @throws Exception
     */
    public function repositories(string $workspace)
    {
        $repositories = $this->bitbucketService->getAvailableRepositories($workspace);

        return view('repositories', compact('repositories', 'workspace'));
    }

    /**
     * @param string $workspace
     * @param string $repository
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     * @throws Exception
     */
    public function pullRequests(string $workspace, string $repository)
    {
        $cacheKey = $workspace . $repository;
        $pullRequests = Cache::remember(
            $cacheKey,
            3600,
            fn() => $this->bitbucketService->getPullRequests($workspace, $repository)
        );

        return view('pullRequests', compact('pullRequests', 'workspace', 'repository'));
    }

    /**
     * @param string $workspace
     * @param string $repository
     * @param int $pullRequestId
     * @return Application|Factory|\Illuminate\Contracts\View\View
     * @throws Exception
     */
    public function comments(string $workspace, string $repository, int $pullRequestId)
    {
        $comments = $this->bitbucketService->getAllCommentsOfPullRequest($workspace, $repository, $pullRequestId);

        return view('comments', compact('comments', 'pullRequestId'));
    }

    public function auth(): RedirectResponse
    {
        return redirect()->away($this->bitbucketService->getOAuthCodeUrl());
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function receiveOAuthCode(Request $request): RedirectResponse
    {
        $redirectRoute = 'auth';
        if ($this->bitbucketService->getAndSaveOAuthAccessToken($request->get('code'))) {
            $redirectRoute = 'dashboard';
        }
        return redirect()->route($redirectRoute);
    }
}