<?php
namespace app\api\controller;

use think\Controller;

class Schedule extends Controller
{

    public function runSchedule(){

        $aToken = input('token','','text');
        $aTime = input('time',0,'intval');
        if($aTime + 30  < time()){
            exit('Error');
        }
        if($aToken != md5($aTime.config('database.auth_key'))){
            exit('Error');
        }
        model('common/Schedule')->run();
    }
}