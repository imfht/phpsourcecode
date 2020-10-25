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

namespace app\index\controller;

use app\common\controller\ControllerBase;

/**
 * 前端模块基类控制器
 */
class IndexBase extends ControllerBase
{
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        // 执行父类构造方法
        parent::__construct();
        
        $this->assign('seo_info', $this->logicSeo->getSeoInfo());
    }
}
