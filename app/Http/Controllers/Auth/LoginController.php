<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validatedAttributes = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required',
            'pin_code' => 'required|integer|digits:6'
            //'device_name' => 'required'
        ]);

        $user = User::where('email', $validatedAttributes['email'])->first();

        if(! $user || ! Hash::check($validatedAttributes['password'], $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect'], Response::HTTP_UNAUTHORIZED);
        }

        // Reject first login if pin code timestamp is older than 5 minutes. 
        $diff = $user->pin_code_timestamp->diff(Carbon::now());
        if(!($user->is_active) && $diff->i > 5 && $diff->s > 0) {
            return response()->json(['error' => 'The pin code has expired. Please request a new one.'], Response::HTTP_UNAUTHORIZED);
        }

        $user->is_active = true;

        return $user->createToken('hexclan_token')->plainTextToken;// Replace hexclan_token by $validateAttributes->device_name
    }
}
