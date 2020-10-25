<?php
namespace wstmart\shop\controller;
use wstmart\shop\model\Stores as M;
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
 * 门店控制器
 */

class Stores extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
    * 店铺公告页
    */
    public function index(){
        $this->assign("p",(int)input("p"));
        return $this->fetch('stores/list');
    }
    /**
    * 查询
    */
    public function pageQuery(){
        $m = new M();
        return WSTGrid($m->pageQuery());
    }
    
    /**
     * 新增店铺管理员
     */
    public function add(){
        $this->assign("p",(int)input("p"));
        return $this->fetch('stores/add');
    }
    
    /**
     * 新增店铺管理员
     */
    public function toAdd(){
        $m = new M();
        return $m->add();
    }
    
    /**
     * 修改门店管理员
     */
    public function edit(){
        $m = new M();
        $object = $m->getById();
        $this->assign("object",$object);
        $this->assign("p",(int)input("p"));
        return $this->fetch('stores/edit');
    }

    /**
     * 编辑店铺管理员
     */
    public function toEdit(){
        $m = new M();
        return $m->edit();
    }
    
    /**
     * 删除操作
     */
    public function del(){
        $m = new M();
        $rs = $m->del();
        return $rs;
    }

    /**
     * 启用关闭门店
     */
    public function setStoreStatus(){
        $m = new M();
        $rs = $m->setStoreStatus();
        return $rs;
    }

    /**
     * 门店销售统计 
     */
    public  function salestatistics(){
        return $this->fetch('stores/sale_statistics');
    }

    /**
    * 查询门店销售统计
    */
    public function pageQuerySalestatistics(){
        $m = new M();
        $rs = $m->pageQuerySalestatistics();
        return WSTReturn("", 1,$rs);
    }
    
}
