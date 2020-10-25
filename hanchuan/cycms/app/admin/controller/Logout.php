<?php
namespace app\admin\controller;

use think\facade\Cookie;
use app\admin\controller\Common;

class Logout extends Common
{
    public function index()
    {
        Cookie::delete('auth');
        return $this->success('恭喜，退出成功！', url('admin/login/index'));
    }
}
