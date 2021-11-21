<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserBelongsToEvent
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
        $user = $request->user();
        if(!$user->is_admin) {
            if(in_array($event->id, $user->getEvents(), true) {
                return $next($request);
            }
            else {
                return response()->json(['error' => 'The user does not belong to the event.'])
            }
        } 

        return $next($request);
    }
}
