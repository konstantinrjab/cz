<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
//    protected function redirectTo($request)
//    {
//        if (!$request->expectsJson()) {
//            return route('login');
//        }
//    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param mixed ...$guards
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        if ($this->authenticate($request, $guards) === 'authentication_error') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $next($request);
    }

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }
        return 'authentication_error';
    }
}
