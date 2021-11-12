<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function Psy\debug;

class TestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = User::findOrFail('534cf7ea-9236-3b25-949b-13ba9d1c2179');
        $roles = null;
        foreach ($user->events as $event) {
            $roles = $event->pivot->role;
        }

        return response()->json($roles, Response::HTTP_OK);
    }
}
