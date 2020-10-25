<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\install\controller;

use think\Controller;

/**
 * Install基类
 */
class InstallBase extends Controller
{
    
    //基类初始化
    public function _initialize()
    {
        
        parent::_initialize();
        
        file_exists(APP_PATH . 'database.php') && $this->error('OneBase已经成功安装，请勿重复安装!');
    }
    
}
