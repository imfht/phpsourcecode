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
 * 站点栏目-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Controller;
use Admin\Model\ItemCateModel;
use Admin\Service\ItemCateService;
class ItemCateController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new ItemCateModel();
        $this->service = new ItemCateService();
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-07-16
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::edit()
     */
    function edit() {
        $pid = I("get.pid",0);
        if($pid) {
            $pInfo = $this->mod->getInfo($pid);
        }
        
        //获取站点信息
        $itemMod = M("item");
        $itemList = $itemMod->where(['status'=>1,'mark'=>1])->select();
        $this->assign('itemList',$itemList);
        
        parent::edit([
            'parent_id'=>$pid,
            'parent_name'=>$pInfo['name'],
        ]);
    }
    
    /**
     * 获取子级【挂件专用】
     *
     * @author 牧羊人
     * @date 2018-08-28
     */
    function getChilds() {
        if(IS_POST) {
            $itemId = I("post.item_id",0);
            $pid = I("post.pid",0);
            $list = $this->mod->getChilds($itemId,$pid);
            $this->ajaxReturn(message('获取成功',true,$list));
        }
    }
    
}