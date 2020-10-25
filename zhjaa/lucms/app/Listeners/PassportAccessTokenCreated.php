<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PassportAccessTokenCreated
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
     * @param  \Laravel\Passport\Events\AccessTokenCreated $event
     * @return void
     */
    public function handle(\Laravel\Passport\Events\AccessTokenCreated $event)
    {
//        DB::table('oauth_access_tokens')->where('id', '!=', $event->tokenId)
//            ->where('user_id', $event->userId)
//            ->where('client_id', $event->clientId)
////            ->where('expires_at', '<', Carbon::now())
//            ->orWhere('revoked', true)
//            ->delete();

    }
}
