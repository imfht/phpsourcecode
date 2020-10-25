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
 * 布局描述-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Controller;
use Admin\Model\LayoutDescModel;
use Admin\Service\LayoutDescService;
class LayoutDescController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new LayoutDescModel();
        $this->service = new LayoutDescService();
    }
    
    /**
     * 删除
     * 
     * @author 牧羊人
     * @date 2018-08-16
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::drop()
     */
    function drop() {
        if(IS_POST) {
            $id = I('post.id');
            $count = M("Layout")->where(["page_id"=>$id,'mark'=>1])->count();
            if($count>0) {
                $this->ajaxReturn(message("当前布局描述已经在使用,无法删除",false));
                return;
            }
            parent::drop();
        }
    }
    
    /**
     * 获取子级【挂件专用】
     * 
     * @author 牧羊人
     * @date 2018-07-17
     */
    function getChilds() {
        if(IS_POST) {
            $itemId = I("post.item_id",0);
            $list = $this->mod->getChilds($itemId);
            $this->ajaxReturn(message('获取成功',true,$list));
        }
    }
    
}