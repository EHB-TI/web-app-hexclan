<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RestrictToOwnResources
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $paramUserId = $request->route('user')->id;
        $userId = $request->user()->user_id ?? $request->user()->id;
        $self = $request->user()->tokenCan('self');
        if ($self && $paramUserId != $userId) {
            return response()->json(['error' => 'The user is only authorised to access his/her own record(s)'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
