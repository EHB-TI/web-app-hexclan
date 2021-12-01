<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Http\Resources\UserResource;
use App\Models\Event;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use function Psy\debug;

class TestGetController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // $user = User::where('ability', '!=', '*')->first();
        // $event = Event::all()->first();
        // $events = $user->events;
        // $users = $event->users;


        // return response()->json($users);
        // Test UserResource collections.
        //return UserResource::collection(User::all());

        // Retrieves all roles of user.
        // $values = [];
        // foreach ($events as $event) {
        //     array_push($values, $event->pivot->role);
        // }
        // $roles['roles'] = $values;

        // return response()->json($roles, Response::HTTP_OK);

        // Alternative
        // $roles['roles'] = $user->getRoles();

        // return response()->json($roles, Response::HTTP_OK);

        // Retrieves ass. array with event id and user role.
        // $roles = [];
        // foreach ($events as $event) {
        //     $roles += [$event->id => $event->pivot->role];
        // }

        // return response()->json($roles, Response::HTTP_OK);

        //Retrieves role with respect to specific event.
        // $targetEvent = $events->find($event->id);
        // $role = $targetEvent->pivot->role;
        // $user = $users->find($user->id);
        // $role = $user->pivot->role;

        // return response()->json(['role' => $role]);

        //TODO: Eloquent syntax to retrieve subtotal of a given transaction.
    }
}
