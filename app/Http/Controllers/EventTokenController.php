<?php

namespace App\Http\Controllers;

use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class EventTokenController extends Controller
{
    // public function refresh()
    // {
    // }

    // An event token is created per event to which the user belongs. This route should be accessed after first login.
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_first_sync' => 'required|boolean',
            'device_name' => 'required',
            'ids' => 'exclude_if:is_first_sync,true|required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $user = $request->user();
        $tokens = [];
        if ($user->events()->exists()) {
            $user->load('events'); // Lazy eager loading.
            foreach ($user->events as $event) {
                if (!$validatedAttributes['is_first_sync']) {
                    $event->tokens()->whereIn('id', $validatedAttributes['ids'])->delete(); // Purge all event tokens of the user.
                }
                $eventToken = $event->createToken($validatedAttributes['device_name'], ["{$event->pivot->ability}"]);
                $token = new Token("event_token", $event->id, $eventToken->plainTextToken, $eventToken->accessToken->id);
                array_push($tokens, $token);
            }

            return response()->json(['data' => $tokens], Response::HTTP_OK);
        } else {
            response()->json(['error' => 'The user does not belong to any event.'], Response::HTTP_NOT_FOUND);
        }
    }
}
