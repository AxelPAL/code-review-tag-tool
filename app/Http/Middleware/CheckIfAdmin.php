<?php

namespace App\Http\Middleware;

use App\Contracts\Auth\RolesInterface;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckIfAdmin
{
    /**
     *
     * @param User|Authenticatable $user
     *
     * @return bool
     */
    private function checkIfUserIsAdmin($user): bool
    {
        return ($user->hasRole(RolesInterface::ROLE_ADMIN));
    }


    /**
     * @param $request
     * @return Application|ResponseFactory|RedirectResponse|Response
     */
    private function respondToUnauthorizedRequest($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('backpack::base.unauthorized'), 401);
        }

        return redirect()->guest(backpack_url('login'));
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (backpack_auth()->guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }

        if (!$this->checkIfUserIsAdmin(backpack_user())) {
            return $this->respondToUnauthorizedRequest($request);
        }

        return $next($request);
    }
}
