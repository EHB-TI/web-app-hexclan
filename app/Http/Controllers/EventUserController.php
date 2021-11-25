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
        } else if ($user->ability == '*') {
            return response()->json(['error' => 'User cannot be assigned an ability'], Response::HTTP_UNAUTHORIZED);
        }

        $event->users()->attach($user->id, ['ability' => $validatedAttributes['ability']]);

        return response()->json(['data' => $event->abilitys], Response::HTTP_CREATED);
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

        return response()->json(['data' => $event->abilitys], Response::HTTP_OK);
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

        return response()->noContent();
    }
}
