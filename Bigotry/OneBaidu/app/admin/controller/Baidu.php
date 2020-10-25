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
 * 百度控制器
 */
class Baidu extends AdminBase
{
    
    /**
     * 百度自定义列表
     */
    public function baiduList()
    {
        
        $this->assign('list', $this->logicBaidu->getBaiduList());
        
        return $this->fetch('baidu_list');
    }
    
    /**
     * 百度自定义添加
     */
    public function baiduAdd()
    {
        
        IS_POST && $this->jump($this->logicBaidu->baiduEdit($this->param));
        
        return $this->fetch('baidu_edit');
    }
    
    /**
     * 百度自定义编辑
     */
    public function baiduEdit()
    {
        
        IS_POST && $this->jump($this->logicBaidu->baiduEdit($this->param));
        
        $info = $this->logicBaidu->getBaiduInfo(['id' => $this->param['id']]);
        
        $this->assign('info', $info);
        
        return $this->fetch('baidu_edit');
    }
    
    /**
     * 百度自定义删除
     */
    public function baiduDel($id = 0)
    {
        
        $this->jump($this->logicBaidu->baiduDel(['id' => $id]));
    }
}
