<?php
/**
 * Created by PhpStorm.
 * User: imust
 * Date: 2017/1/6
 * Time: 9:48
 */

namespace App\Services\Common;

use Request;


class MessageAlert
{
    //danger,info,warning,success
    const SUCCESS = 'success';
    const DANGER = 'danger';
    const INFO = 'info';
    const WARNING = 'warning';

    public function store($messageTitle, $messageType, $messageBody)
    {
        if (in_array($messageType, [self::DANGER, self::INFO, self::SUCCESS, self::WARNING])) {
            if (Request::hasSession()) {
                Request::session()->flash('messageAlert', ['messageTitle'=>$messageTitle,'messageBody'=>$messageBody,'messageType'=>$messageType,]);
                return true;
            }
        }
       return false;
    }

    public function getAlert()
    {
        if (Request::hasSession()) {
            $messageAlert = Request::session()->get('messageAlert');
            if($messageAlert)
                return $messageAlert;
        }
        return false;
    }
}