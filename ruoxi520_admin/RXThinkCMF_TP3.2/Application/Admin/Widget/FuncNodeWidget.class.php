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
 * 功能节点-挂件
 * 
 * @author 牧羊人
 * @date 2018-07-23
 */
namespace Admin\Widget;
use Think\Controller;
class FuncNodeWidget extends Controller {
    function __construct() {
        parent::__construct();
    }
    
    /**
     * 添加节点【全局导航】
     * 
     * @author 牧羊人
     * @date 2018-07-23
     */
    function btnAdd($funcName,$param=[]) {
        $this->assign('param',json_encode($param));
        $this->btnFunc("add", '&#xe608;', $funcName);
    }
    
    /**
     * 批量删除节点【全局导航】
     * 
     * @author 牧羊人
     * @date 2018-07-23
     */
    function btnDAll($funcName) {
        $this->btnFunc("batchDrop", '&#xe640;', $funcName,"layui-btn-danger");
    }
    
    /**
     * 常用按钮【全局导航】
     * 
     * @author 牧羊人
     * @date 2018-08-01
     */
    function btnFunc($funcAct,$funcIcon,$funcName,$funcColor='',$funcType=1,$param=[]) {
        $this->assign('funcAct',$funcAct);
        $this->assign('funcIcon',$funcIcon);
        $this->assign('funcName',$funcName);
        $this->assign('funcColor',$funcColor);
        $this->assign('funcType',$funcType);
        if($param) {
            $this->assign('param',json_encode($param));
        }
        $this->display("Widget:FuncNode:funcNode.func");
    }
    
    /**
     * 添加节点【行数据】
     * 
     * @author 牧羊人
     * @date 2018-08-18
     */
    function btnAdd2() {
        $this->display("Widget:FuncNode:funcNode.add");
    }
    
    /**
     * 编辑节点【行数据】
     * 
     * @author 牧羊人
     * @date 2018-07-23
     */
    function btnEdit($funcName) {
        $this->assign('funcName',$funcName);
        $this->display("Widget:FuncNode:funcNode.edit");
    }
    
    /**
     * 删除节点【行数据】
     */
    function btnDel($funcName) {
        $this->assign('funcName',$funcName);
        $this->display("Widget:FuncNode:funcNode.drop");
    }
    
    /**
     * 查看详情【行数据】
     * 
     * @author 牧羊人
     * @date 2018-07-23
     */
    function btnDetail($funcName) {
        $this->assign('funcName',$funcName);
        $this->display("Widget:FuncNode:funcNode.detail");
    }
    
    /**
     * 设置权限
     * 
     * @author 牧羊人
     * @date 2018-08-28
     */
    function btnSetAuth($funcName) {
        $this->assign('funcName',$funcName);
        $this->display("Widget:FuncNode:funcNode.auth");
    }
    
}