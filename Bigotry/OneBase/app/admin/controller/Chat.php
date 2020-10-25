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

namespace app\admin\controller;

/**
 * 聊天室控制器
 */
class Chat extends AdminBase
{
    
    /**
     * 聊天室
     */
    public function index()
    {
        
        $this->assign('ob_chat_contents', get_chat_contents());
        
        return $this->fetch('index');
    }
}
