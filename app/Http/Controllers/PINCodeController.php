<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PINCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class PINCodeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(User $user)
    {
        if (isset($user->pin_code)) {
            $diff = $user->pin_code_timestamp->diff(Carbon::now());
            if ($diff->i > 5 && $diff->s > 0) {
                $user->pin_code = random_int(10 ** (6 - 1), (10 ** 6) - 1);
                $user->save();
                $user->notify(new PINCodeNotification());

                return response()->noContent();
            } else {
                return response()->json(['error' => 'The pin code has not expired.'], Response::HTTP_FORBIDDEN);
            }
        } else {
            return response()->json(['error' => 'The user has not attempted a first registration.'], Response::HTTP_FORBIDDEN);
        }
    }
}
