<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        $emails = User::all(['email']);


        $count = DB::table('users')->count();

        // Close registration at central level after 1 user.
        if (tenant('id') == null && $count > 0) {
            return response()->json(['error' => 'The maximum number of users has been reached.'], Response::HTTP_FORBIDDEN);
        }
        // At tenant level, first registered user is sole admin.
        else if ($count == 0) {
            $firstUser = true;
        } else {
            $firstUser = false;
        }

        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => $validatedAttributes['name'],
            'email' => $validatedAttributes['email'],
            'password' => bcrypt($validatedAttributes['password']),
            'is_active' => false,
            'pin_code' => random_int(10 ** (6 - 1), (10 ** 6) - 1), // Generates random 6-digits integer.
            'pin_code_timestamp' => Carbon::now()
        ]);

        //Dispatches Registered event upon succesful registration.
        event(new Registered($user));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
