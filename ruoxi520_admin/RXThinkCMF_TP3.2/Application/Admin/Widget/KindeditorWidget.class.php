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
 * Kindeditor编辑器挂件
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Widget;
use Think\Controller;
class KindeditorWidget extends Controller {
    private $type;
    function __construct() {
        parent::__construct();
        
        //编辑器类型：简单类型、复杂类型
        $this->type = array('simple','default');
        
    }
    
    /**
     * 获取编辑器
     *
     * @author 牧羊人
     * @date 2018-07-16
     */
    function getEditor($type,$kindeditorId,$width,$height){
        if(!in_array($type, $this->type)){
            $type = $this->type[0];
        }
    
        $width = isset($width) ? $width : 900;	//宽度
        $height = isset($height) ? $height :500;	//高度
    
        $rootUrl = str_replace('http://www.', '', trim(SITE_URL, '/'));
        $this->assign('kindeditor_content',trim($_GET['component']));
        $this->assign('rootUrl',$rootUrl);
        $this->assign('type',$type);
        $this->assign('kindeditorId',$kindeditorId);
        $this->assign('width',$width);
        $this->assign('height',$height);
        $this->display("Widget:kindeditor.getEditor");
    }
    
}