<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

// Stateless authentication based on sanctum tokens.
class LoginController extends Controller
{
    // Should be called on every app start-up.
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array:email,password,pin_code,device_name',
            'data.email' => ['required', 'email', Rule::exists('users', 'email'), 'max:255'],
            'data.password' => 'required',
            'data.pin_code' => 'required|integer|digits:6',
            'data.device_name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rawValidatedAttributes = $validator->validated();
        $validatedAttributes = $rawValidatedAttributes['data'];

        $user = User::with(['tokens'])->firstWhere('email', $validatedAttributes['email']);

        if ($user->pin_code == -1) {
            return response()->json(['error' => 'The account is deactivated.'], Response::HTTP_UNAUTHORIZED);
        }

        // Checking user credentials.
        if (!isset($user)) {
            abort(Response::HTTP_NOT_FOUND);
        } else if (!Hash::check($validatedAttributes['password'], $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect'], Response::HTTP_UNAUTHORIZED);
        }

        // Login action always expects a pin code but is only checked if user is not active.
        if (!$user->is_active) {
            // Rejects login if pin code timestamp is older than 5 minutes.
            $diff = $user->pin_code_timestamp->diff(Carbon::now());
            if (!$user->is_active && ($diff->i > 5 && $diff->s > 0)) {
                return response()->json(['error' => 'The pin code has expired. Please request a new one.'], Response::HTTP_UNAUTHORIZED);
            } else if ($user->pin_code != $validatedAttributes['pin_code']) {
                return response()->json(['error' => 'The provided pin code is incorrect'], Response::HTTP_UNAUTHORIZED);
            } else {
                $user->is_active = true;
                $user->save();
            }
        }

        // User is active. The user token for the user is created. The ability for global actions (create event, create bank account,...) is set.
        if ($user->is_active && $user->tokens->isEmpty()) {
            switch ($user->ability) {
                case 'admin':
                    $userToken = $user->createToken($validatedAttributes['device_name'], ['admin']);
                    break;
                case 'write':
                    $userToken = $user->createToken($validatedAttributes['device_name'], ['write']);
                    break;
                case 'self':
                    $userToken = $user->createToken($validatedAttributes['device_name'], ['self']);
                    break;
            }

            $token = new Token('user_token', $user->id, $userToken->plainTextToken);

            return response()->json(['data' => $token], Response::HTTP_OK);
        } else {
            return response()->json(['data' => 'The user token is set.'], Response::HTTP_OK);
        }
    }
}
