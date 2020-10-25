<?php
namespace addons\groupon\controller;

use think\addons\Controller;
use addons\groupon\model\Groupons as M;
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
 * 团购插件
 */
class Shops extends Controller{
	public function __construct(){
		parent::__construct();
		$this->assign("v",WSTConf('CONF.wstVersion')."_".WSTConf('CONF.wstPCStyleId'));
	}
	/**
	 * 团购列表
	 */
	public function groupon(){
	    $this->assign("p",(int)input("p"));
    	return $this->fetch("/shop/list");
	}
	/**
	 * 加载团购数据
	 */
	public function pageQuery(){
		$m = new M();
		return WSTGrid($m->pageQueryByShop());
	}

	/**
	 * 搜索商品
	 */
	public function searchGoods(){
		$m = new M();
		return $m->searchGoods();
	}

	/**
	 * 跳去编辑页面
	 */
	public function edit(){
		$id = (int)input('id');
		$object = [];
		$m = new M();
		if($id>0){
            $object = $m->getById($id);
		}else{
			$object = $m->getEModel('groupons');
			$object['marketPrice'] = '';
			$object['goodsName'] = '请选择团购商品';
			$object['startTime'] = date('Y-m-d H:00:00',strtotime("+2 hours"));
			$object['endTime'] = date('Y-m-d H:00:00',strtotime("+1 month"));
		}
		$this->assign("object",$object);
        $this->assign("p",(int)input("p"));
		return $this->fetch("/shop/edit");
	}

	/**
	 * 保存团购信息
	 */
	public function toEdit(){
		$id = (int)input('post.grouponId');
		$m = new M();
		if($id==0){
            return $m->add();
		}else{
            return $m->edit();
		}
	}

	/**
	 * 删除团购
	 */
	public function del(){
		$m = new M();
		return $m->del();
	}

	/**
	 * 查看团购订单列表
	 */
    public function orders(){
    	$this->assign("grouponId",(int)input('grouponId'));
        $this->assign("p",(int)input("p"));
    	return $this->fetch("/shop/list_orders");
    }
    /**
     * 查询订单列表
     */ 
    public function pageQueryByGoods(){
    	$m = new M();
		return $m->pageQueryByGoods();
    }
}