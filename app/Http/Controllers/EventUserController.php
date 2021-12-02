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
     * Sets the roles in the pivot table.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|array:id,ability',
            'roles.id' => => 'required|uuid|exists:users', // Checks the existence of the users in the db.
            'roles.ability' => ['required', 'string', Rule::in(['manager', 'seller'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        foreach ($validatedAttributes['roles'] as $) {
            $event->users()->attach($id, ['ability' => $validatedAttributes['ability']]); // For performance, probably preferable to replace by query builder.
        }

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
