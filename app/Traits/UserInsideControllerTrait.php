<?php

namespace App\Traits;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

trait UserInsideControllerTrait
{
    protected ?User $user;

    public function getUserIdFromSession(): ?int
    {
        if ($this instanceof Controller) {
            $this->middleware(function (Request $request, $next) {
                $this->user = $request->user();
                return $next($request);
            });
        }

        return isset($this->user)  ? $this->user->id : null;
    }
}
