<?php
namespace app\common\controller;


/**
 * 前台总控制器
 */
class IndexBase extends Base
{
    /**
     * 初始化
     */
    protected function _initialize()
    {
        parent::_initialize();
        
        if (!defined('LOAD_INDEXBASE')) {
            define('LOAD_INDEXBASE',TRUE);
            
            //钩子扩展
            $this->get_hook('index_begin',$data=[],$this->user);
            hook_listen('index_begin',$array=['user'=>$this->user]);
            
            //自动模板的布局母模板
            $this->assign('auto_tpl_base_layout', APP_PATH.'member/view/default/layout.htm');
        }
      
        if( isset($this->webdb['web_open']) && empty($this->webdb['web_open']) && empty($this->admin) && ENTRANCE!='admin' ){
            $this->error($this->webdb['close_why']?:'网站维护当中,暂停访问!');
        }
        
    }
}
