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

    public function purge(Request $request)
    {
        $currentAccessToken = $request->user()->currentAccessToken();
        if ($currentAccessToken->tokenable_type === 'App\Models\Event') {
            $currentAccessToken->delete();

            return response()->noContent();
        } else {
            return response()->json(['error' => 'This type of token cannot be purged'], Response::HTTP_FORBIDDEN);
        }
    }

    // An event token is created per event to which the user belongs.
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:device_name',
            'data.device_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $user = $request->user();
        $tokens = [];
        if ($user->events()->exists()) {
            $user->load('events'); // Lazy eager loading.
            foreach ($user->events as $event) {
                $eventToken = $event->createToken($validatedAttributes['device_name'], ["{$event->pivot->ability}"]);
                $token = new Token("event_token", $event->id, $eventToken->plainTextToken);
                array_push($tokens, $token);
            }

            return response()->json(['data' => $tokens], Response::HTTP_OK);
        } else {
            return response()->json(['error' => 'The user does not belong to any event.'], Response::HTTP_NOT_FOUND);
        }
    }
}
