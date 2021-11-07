<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validatedAttributes = $request->validate([
            'name' => 'required|max: 255',
            'email' => 'required|email|max: 255',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $validatedAttributes['name'],
            'email' => $validatedAttributes['email'],
            'password' => bcrypt($validatedAttributes['password'])
        ]);
        
        //Dispatches Registered event upon succesful registration.
        event(new Registered($user));
    
        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
