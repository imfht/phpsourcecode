<?php
namespace wstmart\shop\controller;
use wstmart\shop\model\Goods as M;
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
 * 商品控制器
 */
class Goods extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
     * 批量删除商品
     */
    public function batchDel(){
        $m = new M();
        return $m->batchDel();
    }
    /**
     * 修改商品库存/价格
     */
    public function editGoodsBase(){
        $m = new M();
        return $m->editGoodsBase();
    }

    /**
     * 修改商品状态
     */
    public function changSaleStatus(){
        $m = new M();
        return $m->changSaleStatus();
    }
    /**
     * 批量修改商品状态 新品/精品/热销/推荐
     */
    public function changeGoodsStatus(){
        $m = new M();
        return $m->changeGoodsStatus();
    }
    /**
     *   批量上(下)架
     */
    public function changeSale(){
        $m = new M();
        return $m->changeSale();
    }
    /**
     *  上架商品列表
     */
    public function sale(){
        $this->assign("p",(int)input("p"));
        return $this->fetch('goods/list_sale');
    }
    /**
     * 获取上架商品列表
     */
    public function saleByPage(){
        $m = new M();
        $rs = $m->saleByPage();
        $rs['status'] = 1;
        return WSTGrid($rs);
    }
    /**
     * 仓库中商品
     */
    public function store(){
        $this->assign("p",(int)input("p"));
        return $this->fetch('goods/list_store');
    }
    /**
     * 审核中的商品
     */
    public function audit(){
        $this->assign("p",(int)input("p"));
        return $this->fetch('goods/list_audit');
    }
    /**
     * 获取审核中的商品
     */
    public function auditByPage(){
        $m = new M();
        $rs = $m->auditByPage();
        $rs['status'] = 1;
        return WSTGrid($rs);
    }
    /**
     * 获取仓库中的商品
     */
    public function storeByPage(){
        $m = new M();
        $rs = $m->storeByPage();
        $rs['status'] = 1;
        return WSTGrid($rs);
    }
    /**
     * 违规商品
     */
    public function illegal(){
        $this->assign("p",(int)input("p"));
        return $this->fetch('goods/list_illegal');
    }
    /**
     * 获取违规的商品
     */
    public function illegalByPage(){
        $m = new M();
        $rs = $m->illegalByPage();
        $rs['status'] = 1;
        return WSTGrid($rs);
    }

    /**
     * 跳去新增页面
     */
    public function add(){
        $m = new M();
        $this->assign("p",1);
        $object = $m->getEModel('goods');
        $object['goodsSn'] = WSTGoodsNo();
        $object['productNo'] = WSTGoodsNo();
        $src=input("src")?input("src"):'add';
        $data = ['object'=>$object,'src'=>$src];
        $shopExpressList = model("common/express")->shopExpressList();
        $this->assign("shopExpressList",$shopExpressList);
        return $this->fetch('goods/edit',$data);
    }

    /**
     * 新增商品
     */
    public function toAdd(){
        $m = new M();
        return $m->add();
    }

    /**
     * 跳去编辑页面
     */
    public function edit(){
        $m = new M();
        $object = $m->getById(input('get.id'));
        $this->assign("p",(int)input("p"));
        $data = ['object'=>$object,'src'=>input('src')];
        $shopExpressList = model("common/express")->shopExpressList();
        $this->assign("shopExpressList",$shopExpressList);
        return $this->fetch('goods/edit',$data);
    }

    /**
     * 编辑商品
     */
    public function toEdit(){
        $m = new M();
        return $m->edit();
    }
    /**
     * 删除商品
     */
    public function del(){
        $m = new M();
        return $m->del();
    }
    /**
     * 获取商品规格属性
     */
    public function getSpecAttrs(){
        $m = new M();
        return $m->getSpecAttrs();
    }

    /**
     * 预警库存
     */
    public function stockwarnbypage(){
        $this->assign("p",(int)input("p"));
        return $this->fetch("stockwarn/list");
    }
    /**
     * 获取预警库存列表
     */
    public function stockByPage(){
        $m = new M();
        $rs = $m->stockByPage();
        $rs['status'] = 1;
        return WSTGrid($rs);
    }
    /**
     * 修改预警库存
     */
    public function editwarnStock(){
        $m = new M();
        return $m->editwarnStock();
    }
}
