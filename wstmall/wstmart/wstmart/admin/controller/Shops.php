<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Shops as M;
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
 * 店铺控制器
 */
class Shops extends Base{
    public function index(){
        $this->assign("areaList",model('areas')->listQuery(0));
        $this->assign("p",(int)input("p"));
    	return $this->fetch("list");
    }
    public function stopIndex(){
        $this->assign("p",(int)input("p"));
        $this->assign("areaList",model('areas')->listQuery(0));
    	return $this->fetch("list_stop");
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
    	$m = new M();
    	return WSTGrid($m->pageQuery(1));
    }
    /**
     * 停用店铺列表
     */
    public function pageStopQuery(){
    	$m = new M();
    	return WSTGrid($m->pageQuery(-1));
    }
    /**
     * 获取菜单
     */
    public function get(){
    	$m = new M();
    	return $m->get((int)Input("post.id"));
    }
    /**
     * 跳去编辑页面
     */
    public function toEdit(){
    	$m = new M();
    	$id = (int)input("get.id");
        $companyFields = [];
        $shopFields = [];
        $otherFields = [];
        $apply = [];
    	if($id>0){
            $apply = model('shops')->getById((int)input("get.id"));
    	}else{
    		$apply = $m->getEModel('shops');
            $apply['catshops'] = [];
    		$apply['loginName'] = '';
            $apply['fieldRelevance'] = 0;
    	}
       
        $sf = model('ShopFlows');
        $companyFields = $sf->getFlowFieldsById(2);
        $shopFields = $sf->getFlowFieldsById(3);
        $otherFields = $sf->getFlowFieldsById(-1);
        $this->assign("src",input("src"));
        $this->assign("p",(int)input("p"));
        $this->assign("apply",$apply);
        $this->assign("companyFields",$companyFields);
        $this->assign("shopFields",$shopFields);
        $this->assign("otherFields",$otherFields);
        if($id>0){
        	return $this->fetch("edit");
        }else{
            return $this->fetch("add");
        }
    }
    
    /**
     * 新增菜单
     */
    public function add(){
    	$m = new M();
    	return $m->add();
    }
    /**
     * 编辑菜单
     */
    public function edit(){
    	$m = new M();
    	return $m->edit();
    }
    /**
     * 删除菜单
     */
    public function del(){
    	$m = new M();
    	return $m->del();
    }
    
    /**
     * 检测店铺编号是否存在
     */
    public function checkShopSn(){
    	$m = new M();
    	$isChk = $m->checkShopSn(input('post.shopSn'),input('shopId/d'));
        if(!$isChk){
    		return ['ok'=>'该店铺编号可用'];
    	}else{
    		return ['error'=>'对不起，该店铺编号已存在'];
    	}
    }
    
    /**
     * 自营店铺后台
     */
    public function inself(){
    	$staffId=session("WST_STAFF");
    	if(!empty($staffId)){
    		$id=1;
    		$s = new M();
    		$r = $s->selfLogin($id);
    		if($r['status']==1){
    			header("Location: ".Url('shop/index/index'));
    			exit();
    		}
    	}
    	header("Location: ".Url('home/shops/selfShop'));
    	exit();
    }

    /**
     * 跳去店铺申请列表
     */
    public function apply(){
        $this->assign("p",(int)input("p"));
        $this->assign("areaList",model('areas')->listQuery(0));
        return $this->fetch("list_apply");
    }
    /**
     * 获取分页
     */
    public function pageQueryByApply(){
        $m = new M();
        return WSTGrid($m->pageQueryByApply());
    }
    /**
     * 去处理开店申请
     */
    public function toHandleApply(){
        $data = [];
        $data['object'] = model('shops')->getById((int)input("get.id"));
        $apply = $data['object'];
        $this->assign("p",(int)input("p"));
        $sf = model('ShopFlows');
        $companyFields = $sf->getFlowFieldsById(2);
        $shopFields = $sf->getFlowFieldsById(3);
        $otherFields = $sf->getFlowFieldsById(-1);
        $this->assign("apply",$apply);
        $this->assign("companyFields",$companyFields);
        $this->assign("shopFields",$shopFields);
        $this->assign("otherFields",$otherFields);
        return $this->fetch("edit_apply",$data);
    }

    public function delApply(){
        $m = new M();
        return $m->delApply();
    }

    /**
     * 开店申请处理
     */
    public function handleApply(){
        $m = new M();
        return $m->handleApply();
    }

    /*
     * 查看店铺风格管理
     */
    public function styles(){

    }

    public function wxEnterRefundNotify(){
        $m = model("admin/Weixinpays");
        $m->enterRefundNotify();
    }

    /**
     * 充值缴纳年费记录
     */
    public function renewMoney(){
        $this->assign("startDate",date('Y-m-d',strtotime("-1month")));
        $this->assign("endDate",date('Y-m-d'));
        return $this->fetch("/shops/renew_money");
    }
    /**
     * 缴纳年费记录分页
     */
    public function renewMoneyByPage(){
        $m = new M();
        return WSTGrid($m->renewMoneyByPage());
    }

    /**
     * 导出缴纳年费记录统计报表excel
     */
    public function toExportRenewMoney(){
        $m = new M();
        $rs = $m->toExportRenewMoney();
        $this->assign('rs',$rs);
    }

    /**
     * 缴纳年费统计
     */
    public function statRenewMoneyByPage(){
        $m = new M();
        return WSTGrid($m->statRenewMoneyByPage());
    }
}
