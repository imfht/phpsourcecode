<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 富文本编辑器-挂件
 * 
 * @author 牧羊人
 * @date 2018-12-13
 */
namespace app\admin\widget;
class KindeditorWidget extends BaseWidget
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 获取编辑器
     * 
     * @author 牧羊人
     * @date 2018-12-13
     */
    function getEditor($type, $kindeditorId, $width, $height)
    {
        $width = isset($width) ? $width : 900;
        $height = isset($height) ? $height :500;
        
        $rootUrl = str_replace('http://www.', '', trim(SITE_URL, '/'));
        $this->assign('kindeditor_content',trim($_GET['component']));
        $this->assign('rootUrl',$rootUrl);
        $this->assign('type',$type);
        $this->assign('kindeditorId',$kindeditorId);
        $this->assign('width',$width);
        $this->assign('height',$height);
        return $this->fetch('widget/kindeditor_getEditor');
    }
    
}