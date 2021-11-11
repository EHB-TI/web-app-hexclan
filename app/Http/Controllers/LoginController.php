<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

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
        
        if($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validatedAttributes = $validator->validated();
        
        $user = User::where('email', $validatedAttributes['email'])->first();
        
        if($user == null ) {
            return response()->json(['error' => 'Resource not found.'], Response::HTTP_NOT_FOUND);
        } else {
            if(! $user || ! Hash::check($validatedAttributes['password'], $user->password)) {
                return response()->json(['error' => 'The provided credentials are incorrect'], Response::HTTP_UNAUTHORIZED);
            }
    
            // Reject first login if pin code timestamp is older than 5 minutes. 
            $diff = $user->pin_code_timestamp->diff(Carbon::now());
            if(!($user->is_active) && $diff->i > 5 && $diff->s > 0) {
                return response()->json(['error' => 'The pin code has expired. Please request a new one.'], Response::HTTP_UNAUTHORIZED);
            }
    
            $user->is_active = true;
            
            //TODO abilities
            return $user->createToken($validatedAttributes['device_name'])->plainTextToken;
        } 
    }
}
