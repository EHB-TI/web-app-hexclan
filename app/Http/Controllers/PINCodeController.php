<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PINCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PINCodeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = User::findOrFail($request->uuid);
        $user->pin_code = random_int( 10 ** ( 6 - 1 ), ( 10 ** 6 ) - 1);
        $user->save();
        $user->notify(new PINCodeNotification());

        return Response::HTTP_OK;
    }
}
