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
 * 百度搜索页控制器
 */
class S extends IndexBase
{
    
    /**
     * 搜索页
     */
    public function index()
    {
        
        //顶部需替换数据
        $this->assign('list', $this->logicIndex->getBaiduList());
        
        //插入顶部内容模板
        $top_list_template = 'id="content_left">'.$this->fetch('list_template');
        
        $html = $this->logicIndex->getShtml($this->param, $top_list_template);
        
        $deploy_domain = 'http://'.$_SERVER['HTTP_HOST'];
        
        //网址替换
        $data = preg_replace('#http\://www\.baidu\.com/link#', $deploy_domain, $html);

        $this->assign('deploy_domain', $deploy_domain);
        
        $this->assign('data', $data);
        
        return $this->fetch('baidu_list');
    }
    
    /**
     * 底部数据
     */
    public function getBottomList()
    {
        
        //底部需替换数据
        $this->assign('list', $this->logicIndex->getBaiduList(1));
        
        $wd = input('wd');
        
        $top_list_template = $this->fetch('list_template');
        
        //搜索关键字标红
        if(!is_numeric($wd)) : $top_list_template = preg_replace('#'.$wd.'#', '<em>'.$wd.'</em>', $top_list_template); endif;
        
        //底部内容
        echo  $top_list_template;
    }
}
