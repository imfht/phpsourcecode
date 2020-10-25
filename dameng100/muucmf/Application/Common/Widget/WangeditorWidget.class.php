<?php
namespace Common\Widget;

use Think\Controller;

class WangeditorWidget extends Controller
{

    public function editor($id = 'myeditor', $name = 'content', $default='', $config='', $style='', $param='', $width='100%')
    {
        if(empty($style)) $style='min-height:300px;';
            
        $this->assign('id',$id);
        $this->assign('name',$name);
        $this->assign('default',$default);
        $this->assign('config',$config);
        $this->assign('style',$style);
        $this->assign('param',$param);
        $this->assign('width',$width);

        $this->display(T('Application://Common@Widget/wangeditor'));
    }

}
