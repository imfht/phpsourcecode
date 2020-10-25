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
 * 脚本服务-基类
 * 
 * @author 牧羊人
 * @date 2018-11-22
 */
namespace Script\Service;
use Think\Model;
class ScriptServiceModel extends Model {
    public $mod;
    function __construct() {
        
    }
}