<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
//    const AUTHENTICATION_ERROR = 'authentication_error';

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    protected function redirectTo($request): ?string
    {
        if (!$request->expectsJson()) {
            return route('login');
        }

        return null;
    }

//    public function handle($request, Closure $next, ...$guards)
//    {
//        if ($this->authenticate($request, $guards) === self::AUTHENTICATION_ERROR) {
//            return response()->json(['error' => 'Unauthorized'], 401);
//        }
//
//        return $next($request);
//    }

//    protected function authenticate($request, array $guards)
//    {
//        if (empty($guards)) {
//            $guards = [null];
//        }
//        foreach ($guards as $guard) {
//            if ($this->auth->guard($guard)->check()) {
//                return $this->auth->shouldUse($guard);
//            }
//        }
//
//        return self::AUTHENTICATION_ERROR;
//    }
}
