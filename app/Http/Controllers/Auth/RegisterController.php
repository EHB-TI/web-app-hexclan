<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validatedAttributes = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        $count = DB::table('users')->count();
        
        // Close registration at central level after 1 user.
        if(tenant('id') == null && $count > 0) {
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
            'is_admin' => $firstUser,
            'pin_code' => random_int( 10 ** ( 6 - 1 ), ( 10 ** 6 ) - 1),// Generates random 6-digits integer.
            'pin_code_timestamp' => Carbon::now()
        ]);
        
        //Dispatches Registered event upon succesful registration.
        event(new Registered($user));
    
        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
