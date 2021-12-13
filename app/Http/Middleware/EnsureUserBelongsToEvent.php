<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $model = current($request->route()->parameters());
        $eventId =  $model->event_id ?? $model->id; // Order is important.
        if (!$request->user()->ability != 'admin' && $eventId != $request->user()->event_id) {
            return response()->json(['error' => 'The user does not belong to this event.'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
