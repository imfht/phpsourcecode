<?php
namespace Common\Widget;

use Think\Controller;

class UeditorWidget extends Controller
{

    public function editor($id = 'myeditor', $name = 'content', $default='', $config='', $style='', $param='', $width='100%')
    {
        $this->assign('id',$id);
        $this->assign('name',$name);
        $this->assign('default',$default);
        $this->assign('width',$width);
        $this->assign('style',$style);
        if($config=='mini'){
            $config="{toolbars:[
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
                autoFloatEnabled: true,
                initialFrameHeight: 350
            }";
        }
        if($config == 'all') {
            $config='{}';
        }
        if($config == '') {
            $config='{
                autoHeightEnabled: false,
                autoFloatEnabled: true,
                initialFrameHeight: 350}';
        }
        
        $this->assign('config',$config);
        $this->assign('param',$param);
        cookie('video_get_info',U('Home/Public/getVideo'));

        $this->display(T('Application://Common@Widget/ueditor'));
    }

}
