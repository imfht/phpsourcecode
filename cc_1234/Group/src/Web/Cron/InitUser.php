<?php

namespace src\Web\Cron;

use Group\Cron\CronJob;

class InitUser extends CronJob
{
    public function handle()
    {   
        $users = [];
        for ($i=0; $i < 10; $i++) { 
            $users[] = [
                'nickname' => 'user_'.$i.time(),
                'email' => 'test@qq.com'.$i.time(),
                'password' => mt_rand(0, 9999),
            ];
        }

        $this->getUserService()->addUsers($users);
    }

    public function getUserService()
    {
        return service("User:User");
    }
}