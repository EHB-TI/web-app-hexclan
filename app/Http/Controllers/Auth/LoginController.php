<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validatedAttributes = $request->validate([
            'email' => 'required|email|max: 255',
            'password' => 'required',
            //'device_name' => 'required'
        ]);

        $user = User::where('email', $validatedAttributes['email'])->first();

        if(! $user || ! Hash::check($validatedAttributes['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        return $user->createToken('hexclan_token')->plainTextToken;// Replace hexclan_token by $validateAttributes->device_name
    }
}
