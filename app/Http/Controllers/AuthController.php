<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
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

        $token = $user->createToken('hexclan_token')->plainTextToken;
        return (new UserResource($user))
            ->additional(['token' => $token]);
    }

    public function login(Request $request)
    {
        // TODO
    }
}
