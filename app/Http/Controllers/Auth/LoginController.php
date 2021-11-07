<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validatedAttributes = $request->validate([
            'email' => 'required|email|max: 255',
            'password' => 'required',
            'pin_code' => 'required|integer|max: 6'
            //'device_name' => 'required'
        ]);

        $credentials = array_slice($validatedAttributes, 0, 2, true);
        Auth::attempt($credentials);

        $user = User::where('email', $validatedAttributes['email'])->first();

        if(! Auth::attempt())//TO DO continue login logic. Verify API has not expired.

        if(! $user || ! Hash::check($validatedAttributes['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        return $user->createToken('hexclan_token')->plainTextToken;// Replace hexclan_token by $validateAttributes->device_name
    }
}
