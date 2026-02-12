<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Resolve the authenticated user's company and bind it as 'currentCompany'.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        $company = $request->user()->company;

        if (! $company) {
            abort(403, 'User is not assigned to a company.');
        }

        app()->instance('currentCompany', $company);

        return $next($request);
    }
}
