<?php
namespace app\common\widget;

use think\Controller;

class Ueditor extends Controller
{

    public function editor($id = 'myeditor', $name = 'content', $default='', $config='', $style='', $param='', $width='100%')
    {
        $this->assign('id',$id);
        $this->assign('name',$name);
        $this->assign('default',$default);
        $this->assign('width',$width);
        $this->assign('style',$style);

        $url = Url("api/file/ueditor");

        if($config=='mini'){
            $config="
                toolbars:[
                        [
                            'source','|',
                            'bold',
                            'italic',
                            'underline',
                            'fontsize',
                            'forecolor',
                            'fontfamily',
                            'blockquote',
                            'backcolor','|',
                            'insertimage',
                            'insertcode',
                            'link',
                            'emotion',
                            'scrawl',
                            'wordimage'
                        ]
                ],
                autoHeightEnabled: false,
                autoFloatEnabled: false,
                initialFrameWidth: null,
                initialFrameHeight: 350
            ";
        }
        if($config == 'all') {
            $config="";
        }
        if($config == '') {
            $config="
                autoHeightEnabled: false,
                autoFloatEnabled: false,
                initialFrameWidth: null,
                initialFrameHeight: 350
            ";
        }

        $UMconfig = "{
            serverUrl :'$url',
            $config
        }";

        
        $this->assign('config',$UMconfig);
        $this->assign('param',$param);

        echo $this->fetch('common@widget/ueditor');
    }

}
