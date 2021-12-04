<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventUserController extends Controller
{
    /**
     * Events can have multiple managers.
     * Sets the roles in the pivot table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:user_id,ability',
            'data.user_id' => ['required', 'uuid', Rule::exists('users', 'id')], // Checks the existence of the users in the db.
            'data.ability' => ['required', 'string', Rule::in(['manager', 'seller'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $event->users()->attach($validatedAttributes['user_id'], ['ability' => $validatedAttributes['ability']]); // For performance, possibly preferable to replace by bulk operation via query builder.

        return response()->json(['data' => "User {$validatedAttributes['user_id']} added to event {$event->name} with role {$validatedAttributes['ability']}"], Response::HTTP_CREATED);
    }

    /**
     * Only to be used to change user role.
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event, User $user)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:ability',
            'data.ability' => ['required', Rule::in(['manager', 'seller'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        if ($user->role($event) !== $validatedAttributes['ability']) {
            $event->users()->updateExistingPivot($user->id, ['ability' => $validatedAttributes['ability']]);

            return response()->json(['data' => "{$user->name}'s role on event {$event->name} modified to {$validatedAttributes['ability']}."], Response::HTTP_OK);
        } else {
            return response()->json(['error' => "The role of the user on that event is already set as {$validatedAttributes['ability']}"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Removes the roles from the pivot table.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, User $user)
    {
        $event->users()->detach($user->id);

        return response()->json(['data' => "{$user->name} removed from event {$event->name}"], Response::HTTP_OK);
    }
}
