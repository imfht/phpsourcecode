<?php
namespace wstmart\common\model;
use think\Db;
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
 * 收藏类
 */
class Informs extends Base{
	protected $pk = 'informId';
	/**
	 * 跳到举报列表
	 */
	public function inform(){
		$id = input('id');
		$type = input('type');
		$userId = (int)session('WST_USER.userId');
		//判断用户是否拥有举报权利
		    $s = Db::name('users')->where("userId=$userId")->find();
		    if($s['isInform']==0)return WSTReturn("你已被禁止举报！", -1);
		//判断记录是否存在
		$isFind = false;
			$c = Db::name('goods')->where(['goodsStatus'=>1,'dataFlag'=>1,'goodsId'=>$id])->find();
			$isFind = ($c>0);
		if(!$isFind)return WSTReturn("举报失败，无效的举报对象", -1);
		    $shopId = $c['shopId'];
			$s = Db::name('shops')->where(['shopStatus'=>1,'dataFlag'=>1,'shopId'=>$shopId])->field('shopName,shopId')->find();
			$c = array_merge($c,$s); 
		return WSTReturn('',1,$c);
	} 
	
/**
	  * 获取用户举报列表
	  */
	public function queryUserInformByPage(){
		$userId = (int)session('WST_USER.userId');
		$informStatus = (int)Input('informStatus');

		$where['oc.informTargetId'] = $userId;
		if($informStatus>=0){
			$where['oc.informStatus'] = $informStatus;
		}
		$rs = $this->alias('oc')
		           ->join('__SHOPS__ s','oc.shopId=s.shopId','left')
				   ->join('__GOODS__ o','oc.goodId=o.goodsId and o.dataFlag=1','inner')
				   ->order('oc.informId asc')
				   ->where($where)
				   ->paginate()->toArray();

		foreach($rs['data'] as $k=>$v){
			if($v['informStatus']==0){
				$rs['data'][$k]['informStatus'] = '等待处理';
			}elseif($v['informStatus']==1){
				$rs['data'][$k]['informStatus'] = '无效举报';
			}elseif($v['informStatus']==2){
				$rs['data'][$k]['informStatus'] = '有效举报';
			}elseif($v['informStatus']==3){
				$rs['data'][$k]['informStatus'] = '恶意举报';
			}
		}
		if($rs !== false){
			return WSTReturn('',1,$rs);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	// 判断是否已经举报过
	public function alreadyInform($goodsId,$userId){
		return $this->field('informId')->where("goodId=$goodsId and informTargetId=$userId")->find();
	}
/**
	 * 保存订单举报信息
	 */
	public function saveInform(){

		$userId = (int)session('WST_USER.userId');
        $data['goodId'] = (int)input('goodsId');
		//判断是否提交过举报
		$rs = $this->alreadyInform($data['goodId'],$userId);
		if((int)$rs['informId']>0){
			return WSTReturn("该商品已进行过举报,请勿重复提交举报信息",-1);
		}
		Db::startTrans();
		try{
			$data['informTargetId'] = $userId;
			$data['shopId'] = (int)input('shopsId');
			$data['informStatus'] = 0;
			$data['informType'] = (int)input('informType');
			$data['informTime'] = date('Y-m-d H:i:s');
			$data['informAnnex'] = input('informAnnex');
			$data['informContent'] = input('informContent');
			$rs = $this->save($data);
			if($rs !==false){
				
				Db::commit();
				return WSTReturn('',1);
			}else{
				return WSTReturn($this->getError(),-1);
			}
		}catch (\Exception $e) {
		    Db::rollback();
	    }
	    return WSTReturn('举报失败',-1);
	}
	
    /**
	 * 获取举报详情
	 */
	public function getUserInformDetail($userType = 0){
		$userId = (int)session('WST_USER.userId');
		$id = (int)Input('id');
		if($userId==0){
			$where['informTargetId']=$userId;
		}

		//获取举报信息
		$where['informId'] = $id;
		$rs = Db::name('informs')->alias('oc')
		           ->field('oc.*,o.goodsId ,o.goodsName, o.goodsImg , s.shopId , s.shopName')
		           ->join('__SHOPS__ s','oc.shopId=s.shopId','left')
				   ->join('__GOODS__ o','oc.goodId=o.goodsId and o.dataFlag=1','inner')
				   ->where($where)->find();
		if($rs){
			if($rs['informAnnex']!='')$rs['informAnnex'] = explode(',',$rs['informAnnex']);
		}
        return $rs;
	}
}