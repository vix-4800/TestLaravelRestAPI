<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RequestMethod;
use App\Events\UserActivityLogged;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (env('LOG_USER_ACTIVITY', true)) {
            event(new UserActivityLogged(
                $request->user(),
                $request->path(),
                RequestMethod::from($request->method()),
                $response->status(),
                $request->ip(),
            ));
        }

        return $response;
    }
}
