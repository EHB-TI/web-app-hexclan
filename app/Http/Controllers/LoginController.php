<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'email' => 'required|email|max:255',
            'password' => 'required',
            'pin_code' => 'required|integer|digits:6',
            'device_name' => 'required'
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
        }

        // Reject first login if pin code timestamp is older than 5 minutes.
        if (!$user->is_active) {
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
        else {
            // All requests addressed to central context should be handled here given absence of unprivileged users. Tenant admin will also be handled here.
            if ($user->is_admin) {
                $token = $user->createToken($validatedAttributes['device_name'], ['admin']);
                return response()->json(['data' => $token, "plaintext" => $token->plaintext], Response::HTTP_OK);
            }
            // Tenant context only. Managers and sellers.
            else {
                // Put tokens in array
                foreach ($user->events as $event) {
                    $token = $user->createToken($validatedAttributes['device_name'], ["{$event->id}:{$event->pivot->role}"]);
                    return response()->json(['data' => $token, "plaintext" => $token->plaintext], Response::HTTP_OK);
                }
            }
        }
    }
}
