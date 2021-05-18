<?php

namespace App\Http\Controllers;

use App\Contracts\Services\BitbucketServiceInterface;
use App\Repositories\UserBitbucketTokenRepository;
use App\Traits\UserInsideControllerTrait;
use Cache;
use Http\Client\Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitbucketController extends Controller
{
    use UserInsideControllerTrait;

    /**
     * @var BitbucketServiceInterface
     */
    private BitbucketServiceInterface $bitbucketService;

    public function __construct(
        BitbucketServiceInterface $bitbucketService,
        public UserBitbucketTokenRepository $userBitbucketTokenRepository,
        public Request $request
    ) {
        $this->bitbucketService = $bitbucketService;
        $this->bitbucketService->init($this->getUserIdFromSession());
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): Factory|View|Application
    {
        $isBitbucketApiKeyAcquired = false;
        if ($request->user() !== null) {
            $isBitbucketApiKeyAcquired = $this->userBitbucketTokenRepository->existsAndStillActive(
                $request->user()->id
            );
        }
        return view('index', compact('isBitbucketApiKeyAcquired'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     * @throws Exception
     */
    public function workspaces(Request $request): \Illuminate\Contracts\View\View|Factory|RedirectResponse|Application
    {
        $this->bitbucketService->init($request->user()->id);
        $workspaces = $this->bitbucketService->getAvailableWorkspaces();

        return view('workspaces', compact('workspaces'));
    }

    /**
     * @param string $workspace
     * @param Request $request
     * @return Application|Factory|RedirectResponse|View
     * @throws Exception
     */
    public function repositories(string $workspace, Request $request): Factory|View|Application|RedirectResponse
    {
        $this->bitbucketService->init($request->user()->id);
        $repositories = $this->bitbucketService->getAvailableRepositories($workspace);

        return view('repositories', compact('repositories', 'workspace'));
    }

    /**
     * @param string $workspace
     * @param string $repository
     * @param Request $request
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function pullRequests(
        string $workspace,
        string $repository,
        Request $request
    ): Factory|\Illuminate\Contracts\View\View|Application|RedirectResponse {
        $cacheKey = $workspace . $repository;
        $pullRequests = Cache::remember(
            $cacheKey,
            3600,
            function () use ($workspace, $repository, $request) {
                $this->bitbucketService->init($request->user()->id);
                return $this->bitbucketService->getPullRequests($workspace, $repository);
            }
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
    public function comments(
        string $workspace,
        string $repository,
        int $pullRequestId
    ): Factory|\Illuminate\Contracts\View\View|Application {
        $comments = $this->bitbucketService->getAllCommentsOfPullRequest($workspace, $repository, $pullRequestId);

        return view('comments', compact('comments', 'pullRequestId'));
    }

    public function auth(Request $request): RedirectResponse
    {
        return redirect()->away($this->bitbucketService->getOAuthCodeUrl($request->user()->id));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function receiveOAuthCode(Request $request): RedirectResponse
    {
        $redirectRoute = 'auth';
        if ($this->bitbucketService->getAndSaveOAuthAccessToken($request->user()->id, $request->get('code'))) {
            $redirectRoute = 'dashboard';
        }
        return redirect()->route($redirectRoute);
    }
}
