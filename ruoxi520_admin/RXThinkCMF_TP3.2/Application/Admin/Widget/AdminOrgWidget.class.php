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
 * 组织机构-挂件
 * 
 * @author 牧羊人
 * @date 2018-07-24
 */
namespace Admin\Widget;
use Think\Controller;
use Admin\Model\AdminOrgModel;
class AdminOrgWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 选择组织机构
     * 
     * @author 牧羊人
     * @date 2018-07-24
     */
    function select($param,$selectId) {
        $arr = explode('|', $param);
    
        //参数
        $idStr = $arr[0];
        $isV = $arr[1];
        $msg = $arr[2];
        $show_name = $arr[3];
        $show_value = $arr[4];
        
        //获取组织机构
        $adminOrgMod = new AdminOrgModel();
        $list = $adminOrgMod->where(['mark'=>1])->select();
    
        $this->assign('idStr',$idStr);
        $this->assign('isV',$isV);
        $this->assign('msg',$msg);
        $this->assign('show_name',$show_name);
        $this->assign('show_value',$show_value);
        $this->assign('dataList',$list);
        $this->assign("selectId",$selectId);
        $this->display("Widget:single.select");
    }
    
}