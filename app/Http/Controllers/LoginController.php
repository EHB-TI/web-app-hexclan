<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

// Stateless authentication based on sanctum tokens. 1 token is issued per user per event.
class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_first_login' => 'required|boolean',
            'email' => 'required|email|max:255',
            'password' => 'required',
            'pin_code' => 'exclude_unless:is_first_login,true|required|integer|digits:6', // Pin code is required on first login only.
            'device_name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $user = User::with(['tokens'])->firstWhere('email', $validatedAttributes['email']);

        if (!$validatedAttributes['is_first_login'] && !$user->is_active && $user->tokens->isEmpty()) {
            return response()->json(['error' => 'The account is deactivated.'], Response::HTTP_UNAUTHORIZED);
        }

        // Checking user credentials should be done every time this route is visited.
        if ($user == null) {
            abort(Response::HTTP_NOT_FOUND);
        } else if (!Hash::check($validatedAttributes['password'], $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect'], Response::HTTP_UNAUTHORIZED);
        }

        if ($validatedAttributes['is_first_login']) {
            // Rejects login if pin code timestamp is older than 5 minutes.
            $diff = $user->pin_code_timestamp->diff(Carbon::now());
            if (!$user->is_active && ($diff->i > 5 && $diff->s > 0)) {
                return response()->json(['error' => 'The pin code has expired. Please request a new one.'], Response::HTTP_UNAUTHORIZED);
            } else if ($user->pin_code != $validatedAttributes['pin_code']) {
                return response()->json(['error' => 'The provided pin code is incorrect'], Response::HTTP_UNAUTHORIZED);
            } else {
                $user->is_active = true;
                $user->save();
            }
        }

        // User is active
        if ($user->is_active) {
            // All requests addressed to central context should be handled here given absence of unprivileged users. Tenant admin will also be handled here. Admin user receives 1 token. There is no scenario where admin token should be renewed.
            if ($user->is_admin) {
                $userToken = $user->createToken($validatedAttributes['device_name'], $user->id); // Not passing any argument in abilities parameter will grant all abilities: ['*'].
                // This manipulation is required to return an array of objects instead of a hierarchy of nested objects.
                $token = new Token($user->id, 'user_token', $userToken->plainTextToken);
                $tokens[] = $token;

                return response()->json(['data' => $token], Response::HTTP_OK);
            }
            // Tenant context. Managers and sellers. Users obtain 1 user token without abilities. Users also obtain 1 token per role, with 1 ability set with that role.
            else {
                $tokens = [];
                $userToken = $user->createToken($validatedAttributes['device_name'], $user->id, []);
                $token = new Token($user->id, 'user_token', $userToken->plainTextToken);
                array_push($tokens, $token); // tokenObjects[0] is user token.

                if ($user->events()->exists()) {
                    $user->load('events'); // Lazy eager loading.
                    foreach ($user->events as $event) {
                        $eventToken = $user->createToken($validatedAttributes['device_name'], $event->id, ["{$event->pivot->role}"]);
                        $token = new Token($event->id, "role_token", $eventToken->plainTextToken);
                        array_push($tokens, $token);
                    }
                }
                return response()->json(['data' => $tokens], Response::HTTP_OK);
            }
        }
    }
}
