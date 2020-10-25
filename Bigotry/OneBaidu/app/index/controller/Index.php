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

/**
 * 百度首页控制器
 */
class Index extends IndexBase
{
    
    //首页
    public function index()
    {
        
        $html = $this->logicIndex->getHtml("http://www.baidu.com");
        
        $this->assign('data', $html);
        
        return $this->fetch('index');
    }
}
