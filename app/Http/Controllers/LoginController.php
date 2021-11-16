<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use stdClass;

// Stateless authentication based on sanctum tokens. 1 token is issued per user per event.
class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required',
            'pin_code' => 'required|integer|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $user = User::firstWhere('email', $validatedAttributes['email']);

        // Checking user credentials should be done every time this route is visited.
        if ($user == null) {
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } else if (!Hash::check($validatedAttributes['password'], $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect'], Response::HTTP_UNAUTHORIZED);
        } else {
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
                $token = $user->createToken($validatedAttributes['user_token'], ['admin']);
                // This manipulation is required to return an array of objects instead of a hierarchy of nested objects.
                $tokenObject = new stdClass();
                $tokenObject->id = $user->id;
                $tokenObject->token = $token->plainTextToken;
                $tokenObjects[] = $tokenObject;

                return response()->json(['data' => $tokenObjects], Response::HTTP_OK);
            }
            // Tenant context only. Managers and sellers. Users obtain 1 user token without abilities. Users also obtain 1 token per event, with 1 ability set with the role on that event.
            else { // Tokens should be synced with user current roles.
                $tokens = [];
                $userToken = $user->createToken('user_token', []);
                $tokenObject = new stdClass();
                $tokenObject->id = $user->id;
                $tokenObject->token = $userToken->plainTextToken;
                // tokenObjects[0] is user token.
                $tokenObjects[] = $tokenObject;

                if ($user->roles()->exists()) {
                    foreach ($user->roles as $role) {
                        $token = $user->createToken("role_{$role->id}_token", ["{$role->role}"]);
                        $tokens += [$role->id => $token->plainTextToken];
                    }

                    foreach ($tokens as $key => $value) {
                        $tokenObject = new stdClass();
                        $tokenObject->id = $key;
                        $tokenObject->token = $value;
                        $tokenObjects[] = $tokenObject;
                    }
                }

                return response()->json(['data' => $tokenObjects], Response::HTTP_OK);
            }
        }
    }
}
