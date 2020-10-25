<?php

namespace App\Listeners;

use App\Models\AdminAuthLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Jenssegers\Agent\Facades\Agent;

class LogSuccessfulLogin
{
    private $authLog;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(AdminAuthLog $authLog)
    {

        $this->authLog = $authLog;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        // 获取浏览器版本
        $browser = Agent::browser();
        $browserInfo = $browser .' '. Agent::version($browser);


        // 获取系统版本
        $platform = Agent::platform();
        $platformInfo =$platform .' '. Agent::version($platform);

        //IP地址

        $ip = request()->getClientIp();

        $this->authLog->create([
            'admins_id' => $event->user->id,
            'platform_info' => $platformInfo,
            'browser_info' => $browserInfo,
            'ip_address' => $ip,
            'login_time' => date('Y-m-d H:i:s')
        ]);
    }
}
