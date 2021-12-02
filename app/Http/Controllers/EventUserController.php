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
            'data.*' => 'required|array:id,ability',
            'data.*.id' => 'required|uuid|exists:users', // Checks the existence of the users in the db.
            'data.*.ability' => ['required', 'string', Rule::in(['manager', 'seller'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();

        foreach ($rawValidatedAttributes['data'] as $validatedAttributes) {
            $event->users()->attach($validatedAttributes['id'], ['ability' => $validatedAttributes['ability']]); // For performance, probably preferable to replace by query builder.
        }

        return response()->json(['data' => "User(s) added to event {$event->name}"], Response::HTTP_CREATED);
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
            'data' => 'required|array:ability',
            'data.ability' => ['required', Rule::in(['manager', 'seller'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $event->users()->updateExistingPivot($user->id, ['ability' => $validatedAttributes['ability']]);

        return response()->json(['data' => "{$user->name}'s role on event {$event->name} updated."], Response::HTTP_OK);
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
