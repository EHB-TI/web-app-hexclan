<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:name,email,password',
            'data.name' => 'required|max:255',
            'data.email' => ['required', 'email', Rule::exists('users', 'email'), 'max:255'],
            'data.password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $user = User::firstWhere('email', $validatedAttributes['email']);
        $pinCode = $user->pin_code;
        // Refuses request if user already registered. No check on pin code validity.
        if (!isset($pinCode)) {
            $user->name = $validatedAttributes['name'];
            $user->password = bcrypt($validatedAttributes['password']);
            $user->pin_code = random_int(10 ** (6 - 1), (10 ** 6) - 1); // Generates random positive 6-digits integer.
            $user->pin_code_timestamp = Carbon::now();
            $user->save();

            // Dispatches Registered event upon succesful registration.
            event(new Registered($user));

            return response()->noContent();
        } else if ($pinCode == -1) {
            return response()->json(['error' => 'The account is deactivated.'], Response::HTTP_FORBIDDEN);
        } else {
            return response()->json(['error' => 'The user is already registered.'], Response::HTTP_FORBIDDEN);
        }
    }
}
