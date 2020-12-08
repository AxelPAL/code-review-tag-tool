<?php

namespace App\Http\Middleware;

use App\Repositories\UserBitbucketTokenRepository;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class TokenExistence
{
    public function __construct(public UserBitbucketTokenRepository $userBitbucketTokenRepository)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $redirectToRoute
     * @return Response|RedirectResponse|null
     */
    public function handle(Request $request, Closure $next, $redirectToRoute = null)
    {
        if (!$this->userBitbucketTokenRepository->existsAndStillActive($request->user()?->id)) {
            return Redirect::to('auth');
        }

        return $next($request);
    }
}
