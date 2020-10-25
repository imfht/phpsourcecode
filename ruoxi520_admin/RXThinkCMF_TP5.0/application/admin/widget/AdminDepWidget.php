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
 * 组织部门-挂件
 * 
 * @author 牧羊人
 * @date 2018-02-14
 */
namespace app\admin\widget;
use app\admin\model\AdminDepModel;
class AdminDepWidget extends BaseWidget
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-02-14
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 组织部门选择
     * 
     * @author 牧羊人
     * @date 2018-02-14
     */
    function select($idStr, $selectId)
    {
        $adminDepMod = new AdminDepModel();
        $adminDepList = $adminDepMod->getChilds(0,1);
        $this->assign('idStr',$idStr);
        $this->assign("selectId",$selectId);
        $this->assign('adminDepList',$adminDepList);
        return $this->fetch('admin_dep/widget_select');
    }
    
}