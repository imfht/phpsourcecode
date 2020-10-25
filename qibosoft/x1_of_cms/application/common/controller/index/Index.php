<?php
namespace app\common\controller\index;

use app\common\controller\IndexBase;

//频道主页
class Index extends IndexBase
{
    public function index(){
        $template = '';
        $this->assign('mid',current(model_config())['id']);
        
        //频道自定义模板
        if (IN_WAP===true) {
            if ($this->webdb['module_wap_index_template']!='') {
                $template = TEMPLATE_PATH.'index_style/'.$this->webdb['module_wap_index_template'];
                if (!is_file($template)) {
                    $template = '';
                }
            }            
        }elseif($this->webdb['module_pc_index_template']) {
            $template = TEMPLATE_PATH.'index_style/'.$this->webdb['module_pc_index_template'];
            if (!is_file($template)) {
                $template = '';
            }
        }
        $this->get_module_layout('index');   //重新定义布局模板
        return $this->fetch($template?:'index');
    }
}
