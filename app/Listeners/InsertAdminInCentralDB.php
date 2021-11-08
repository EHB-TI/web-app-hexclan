<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class InsertAdminInCentralDB
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        DB::connection(config('tenancy.database.central_connection'))
            ->table('tenants')
            ->where('id', tenant('id'))
            ->update(['tenancy_admin_email' => $event->user->email]);
    }
}
