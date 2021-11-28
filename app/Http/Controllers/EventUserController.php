<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventResource;
use App\Http\Resources\UserResource;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventUserController extends Controller
{

    /**
     * Returns the relationships belonging to the entity. Since Eloquent provides "dynamic relationship properties", relationship methods are accessed as if they were defined as properties on the model.
     * 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user()->currentAccessToken()->tokenable_type === 'App\Models\User') {
            return UserResource::collection($request->user()->events);
        } else if ($request->user()->currentAccessToken()->tokenable_type === 'App\Models\Event') {
            return EventResource::collection($request->user()->users);
        }
    }

    /**
     * Attaches an ability to a user by inserting record in the pivot table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'ability' => ['required', Rule::in(['manager', 'seller'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $user = User::firstWhere('email', $validatedAttributes['email']);
        if ($user == null) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $event->users()->attach($user->id, ['ability' => $validatedAttributes['ability']]);

        return response()->json(['data' => "{$user->name} added to event {$event->name} with role {$validatedAttributes['ability']}"], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, User $user)
    {
        $validator = Validator::make($request->all(), [
            'ability' => ['required', Rule::in(['manager', 'seller'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $event->users()->updateExistingPivot($user->id, ['ability' => $validatedAttributes['ability']]);

        return response()->json(['data' => "{$user->name}'s role on event {$event->name} updated to {$validatedAttributes['ability']}"], Response::HTTP_OK);
    }

    /**
     * Detaches the ability from the user by deleting the record in the pivot table.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, User $user)
    {
        $event->users()->detach($user->id);

        return response()->json(['data' => "{$user->name} removed from event {$event->name}", Response::HTTP_OK);
    }
}
