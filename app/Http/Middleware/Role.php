<?php

namespace App\Http\Middleware;

use App\Model\User;
use Closure;
use Illuminate\Support\Facades\Auth;
class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        /** @var User $user */
        $user = Auth::guard()->user();
        if(!$user)
            return redirect()->guest(route('auth.login'));

        if (!$user->hasRole($role)) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return response('Unauthorized.', 401);
            }
        }

        return $next($request);
    }
}
