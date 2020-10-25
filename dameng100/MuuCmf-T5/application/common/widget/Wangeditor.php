<?php
namespace app\common\widget;

use think\Controller;

class Wangeditor extends Controller
{
    /**
     * { function_description }
     *
     * @param      string  $id       The identifier
     * @param      string  $name     The name
     * @param      string  $default  默认文本
     * @param      string  $config   all全部编辑按钮、，mini部分编辑按钮
     * @param      string  $style    The style
     * @param      string  $param    The parameter
     * @param      string  $width    The width
     */
    public function editor($id = 'myeditor', $name = 'content', $default='', $config='all', $style='', $param='', $width='100%')
    {
        if(empty($style)) $style='min-height:300px;';
            
        $this->assign('id',$id);
        $this->assign('name',$name);
        $this->assign('default',$default);
        $this->assign('config',$config);
        $this->assign('style',$style);
        $this->assign('param',$param);
        $this->assign('width',$width);

        echo $this->fetch('common@widget/wangeditor');
    }

}
