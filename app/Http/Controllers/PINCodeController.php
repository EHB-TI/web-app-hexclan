<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PINCodeNotification;
use Illuminate\Http\Request;

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
        $user->pin_code = random_int(10 ** (6 - 1), (10 ** 6) - 1);
        $user->save();
        $user->notify(new PINCodeNotification());

        return response()->noContent();
    }
}
