<?php
namespace wstmart\shop\controller;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 订单投诉控制器
 */
class Supplierordercomplains extends Base{
    protected $beforeActionList = ['checkAuth'];
    /******************************** 用户 ******************************************/
    /**
    * 查看投诉列表
    */
	public function index(){
	    $this->assign("p",(int)input("p"));
		return $this->fetch('supplier/orders/list_complain');
	}
    /**
    * 获取用户投诉列表
    */    
    public function queryUserComplainByPage(){
        $m = model('SupplierOrderComplains');
        return $m->queryUserComplainByPage();
        
    }
    /**
     * 订单投诉页面
     */
    public function complain(){
        $data = model('SupplierOrderComplains')->getOrderInfo();
        $this->assign("data",$data);
        $this->assign("src",input('src'));
        $this->assign("p",(int)input('p',1));
        return $this->fetch("supplier/orders/complain");
    }
    /**
     * 保存订单投诉信息
     */
    public function saveComplain(){
        return model('SupplierOrderComplains')->saveComplain();
    }
    /**
     * 用户查投诉详情
     */
    public function getUserComplainDetail(){
        $data = model('SupplierOrderComplains')->getComplainDetail();
        $this->assign("data",$data);
        $this->assign("p",(int)input("p"));
        return $this->fetch("supplier/orders/complain_detail");
    }
}
