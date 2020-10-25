<?php

namespace Index\Controller;

use Alpaca\Alpaca;
use Model\User;
use Service\Code;
use Service\Message;

class IndexController
{
    public function indexAction()
    {
        Alpaca::log()->info('Foo ddd sss');

        $a = Alpaca::app()->getParam('a', '111');

        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Message::SYSTEM_OK;
        $result['data'] = User::all(['user_id']);
        return Alpaca::app()->toJson($result);
    }

    public function index2Action()
    {
        $result['code'] = Code::SYSTEM_OK;
        $result['msg']  = Message::SYSTEM_OK;
        return Alpaca::app()->toJson($result);
    }
}

