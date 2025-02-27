<?php

namespace App\Http\Middleware;

use Closure;

class HasTeacherRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->isTeacher() || auth()->user()->isAdmin()) {
            return $next($request);
        }

        return redirect()->route('home');
    }
}
