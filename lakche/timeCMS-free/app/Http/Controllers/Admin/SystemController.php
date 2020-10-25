<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\System;
use Request;
use Theme;
use Logs;

class SystemController extends Controller
{
    public function index()
    {
        $system = System::getValue();
        return Theme::view('system.index',compact('system'));
    }

    public function store()
    {
        $input = Request::only(['title','keywords','description','copyright','record','is_open','qq','wechat','wechatcode','weibo','theme','subtitle','miitbeian','beian']);
        $input['is_open'] = $input['is_open'] ? 1 : 0;

        System::saveValue($input);

        $system = System::getValue();
        $message = '参数设置成功！';
        Logs::save('system',0,'update','修改系统参数');
        return Theme::view('system.index',compact('system','message'));
    }
}
