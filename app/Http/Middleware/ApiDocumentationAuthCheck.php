<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class ApiDocumentationAuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user(); //get authenticated user
        if (empty($user)) { //if no user is authenticated then manually log him
            // return response()->redirectTo("/api/documentation");
        }
        // if ($user->author == '1') {
        //     throw new UnauthorizedException("You do not have the necessary access to perform this action (Documentation Access).");
        // }

      
        return $next($request);
    }
}
