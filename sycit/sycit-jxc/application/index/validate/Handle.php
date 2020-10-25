<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/9/19
// +----------------------------------------------------------------------
// | Title:  Handle.php
// +----------------------------------------------------------------------
namespace app\index\validate;

use think\Validate;

class Handle extends Validate
{
    protected $rule = [
        '__token__|数据'    =>  'require|token'
    ];
    protected $message = [
        '__token__.require' => ':attribute出错',
        '__token__.token' => ':attribute失效，重新刷新'
    ];
    protected $scene = [];
}