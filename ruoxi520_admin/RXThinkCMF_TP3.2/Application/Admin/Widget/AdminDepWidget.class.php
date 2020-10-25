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
 * 部门选择-挂件
 * 
 * @author 牧羊人
 * @date 2018-07-19
 */
namespace Admin\Widget;
use Think\Controller;
use Admin\Model\AdminDepModel;
class AdminDepWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 部门选择
     * 
     * @author 牧羊人
     * @date 2018-07-19
     */
    function select($idStr,$selectId) {
        $adminDepMod = new AdminDepModel();
        $adminDepList = $adminDepMod->getChilds(0,1);
        $this->assign('idStr',$idStr);
        $this->assign("selectId",$selectId);
        $this->assign('adminDepList',$adminDepList);
        $this->display("AdminDep:adminDep.select");
    }
    
}