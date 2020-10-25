<?php
// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 脚本基类
 * 
 * @author 牧羊人
 * @date 2018-09-06
 */
namespace Script\Controller;
use Think\Controller;
class BaseScriptController extends Controller {
    //模型、服务
    protected $mod,$service;
    function __construct() {
        parent::__construct();
    }
}