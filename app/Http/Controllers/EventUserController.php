<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Event;
use App\Models\EventUser;
use App\Models\Transaction;
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
    public function upsert(Request $request, Event $event, User $user)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:user_id,ability',
            'data.ability' => ['required', 'string', Rule::in(['write', 'self'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        EventUser::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['ability' => $validatedAttributes['ability']]
        );

        return response()->json(['data' => "User {$user->name}'s role on event {$event->name} set to {$validatedAttributes['ability']}"], Response::HTTP_CREATED);
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

    public function transactions(Request $request, Event $event, User $user)
    {
        if ($request->user()->tokenCan('self') && $user->id != $request->user()->user_id) {
            return response()->json(['error' => 'The user is only authorised to access his/her own record(s)'], Response::HTTP_FORBIDDEN);
        }

        $transactions = Transaction::where('user_id', '=', $user->id)
            ->where('event_id', '=', $event->id)
            ->get();

        return TransactionResource::collection($transactions);
    }
}
