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
        $user = $users->firstWhere('email', $validatedAttributes['email']);
        if ($user == null) {
            return response()->json(['error' => 'Unkwown user.'], Response::HTTP_UNAUTHORIZED);
        }

        $user->update([
            'name' => $validatedAttributes['name'],
            'password' => bcrypt($validatedAttributes['password']),
            'pin_code' => random_int(10 ** (6 - 1), (10 ** 6) - 1), // Generates random 6-digits integer.
            'pin_code_timestamp' => Carbon::now()
        ]);

        // Dispatches Registered event upon succesful registration.
        event(new Registered($user));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
