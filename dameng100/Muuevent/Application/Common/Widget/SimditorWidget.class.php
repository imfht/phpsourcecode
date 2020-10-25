<?php
namespace Common\Widget;

use Think\Controller;

class SimditorWidget extends Controller
{

    public function editor($id = 'myeditor', $name = 'content', $default='', $config='', $style='', $param='', $width='100%')
    {
        $this->assign('id',$id);
        $this->assign('name',$name);
        $this->assign('default',$default);
        $this->assign('width',$width);
        $this->assign('height',$height);
        $this->assign('style',$style);
        if($config=='')
        {
        $config="toolbar:  [
                            'title', 
                            'bold', 
                            'italic', 
                            'underline', 
                            'strikethrough', 
                            'fontScale', 
                            'color', '|', 
                            'ol', 
                            'ul', 
                            'blockquote', 
                            'code', 
                            'table', '|', 
                            'link', 
                            'image', 
                            'hr', '|', 
                            'indent', 
                            'outdent', 
                            'alignment'
                            ]";
        }
        if($config == 'all'){
            $config="toolbar: [
                              'title',
                              'bold',
                              'italic',
                              'underline',
                              'strikethrough',
                              'fontScale',
                              'color',
                              'ol',          
                              'ul',
                              'blockquote',
                              'code',
                              'table',
                              'link',
                              'image',
                              'hr',
                              'indent',
                              'outdent',
                              'alignment',
                            ]";
        }
        //empty($param['zIndex']) && $param['zIndex'] = 977;
        //$config.=(empty($config)?'':',').'zIndex:'.$param['zIndex'];
        //is_bool(strpos($width,'%')) && $config.=',initialFrameWidth:'.str_replace('px','',$width);
        //is_bool(strpos($height,'%')) && $config.=',initialFrameHeight:'.str_replace('px','',$height);
        //$config.=',autoHeightEnabled: false';
        $this->assign('config',$config);
        $this->assign('param',$param);
        //cookie('video_get_info',U('Home/Public/getVideo'));

        $this->display(T('Application://Common@Widget/simditor'));
    }

}
