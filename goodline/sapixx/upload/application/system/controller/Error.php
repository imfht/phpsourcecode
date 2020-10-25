<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 空控制器访问跳转
 */
namespace app\system\controller;
use think\facade\Request;

class Error{

    /**
     * 空控制器
     *
     * @return void
     */
    public function index(){
      return redirect(Request::root(true));
    }

    /**
     * 空方法
     *
     * @return void
     */
    public function _empty(){
      return redirect(Request::root(true));
    }
}