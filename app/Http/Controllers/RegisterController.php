<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();

        $users = User::all();
        $user = $users->where('email', $validatedAttributes['email'])->first();
        if ($user == null) {
            return response()->json(['error' => 'Unkwown user.'], Response::HTTP_UNAUTHORIZED);
        }

        // Central context.
        if (tenant('id') == null) {
            $user->update([
                'name' => $validatedAttributes['name'],
                'password' => bcrypt($validatedAttributes['password']),
                'pin_code' => random_int(10 ** (6 - 1), (10 ** 6) - 1), // Generates random 6-digits integer.
                'pin_code_timestamp' => Carbon::now()
            ]);

            // Creates token with admin ability
            $token = $user->createToken('hexclan_token', ['admin']);

            // Dispatches Registered event upon succesful registration.
            event(new Registered($user));

            return (new UserResource($user))
                ->additional(['token' => $token->plainTextToken])
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        }
        // Tenant context.
        else {
            $user->update([
                'name' => $validatedAttributes['name'],
                'password' => bcrypt($validatedAttributes['password']),
                'pin_code' => random_int(10 ** (6 - 1), (10 ** 6) - 1), // Generates random 6-digits integer.
                'pin_code_timestamp' => Carbon::now()
            ]);

            $token = null;
            if ($users->count == 1) {
                $token = $user->createToken('hexclan_token', ['admin']);
            }

            event(new Registered($user));

            // If user is tenant admin.
            if ($token != null) {
                return (new UserResource($user))
                    ->additional(['token' => $token->plainTextToken])
                    ->response()
                    ->setStatusCode(Response::HTTP_OK);
            }
            // Token is assigned at login based on event selection.
            else {
                return (new UserResource($user))
                    ->response()
                    ->setStatusCode(Response::HTTP_OK);
            }
        }
    }
}
