<?php
namespace wstmart\admin\model;
use wstmart\admin\model\Goods as M;
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
 * 订单投诉业务处理
 */
class Informs extends Base{
	/**
	 * 获取举报列表
	 */
	public function pageQuery(){
     	$informStatus = (int)Input('informStatus',-1);
	 	if($informStatus>-1)$where['o.informStatus']=$informStatus;
     	$where['o.dataFlag']=1;
		$order = [];
		$rs = Db::name('informs')->alias('o')
		                      ->field('o.*,s.shopId,s.shopName,u.userName,u.loginName,oc.goodsImg,oc.goodsId,oc.goodsName')
						      ->join('__SHOPS__ s','o.shopId=s.shopId','left')
						      ->join('__USERS__ u','o.informTargetId=u.userId','inner')
						      ->join('__GOODS__ oc','oc.goodsId=o.goodId','inner')
						      ->where($where)
						      ->order('informId desc')
						      ->paginate()
						      ->toArray();
	    $reason = WSTDatas('INFORMS_TYPE');
	    for($i=1;$i<=count($reason);$i++){
	    	for($j=0;$j<count($rs['data']);$j++)
	    		if($rs['data'][$j]['informType'] == $i){
	    			$rs['data'][$j]['informType'] = $reason[$i]['dataName'];
	    		}
	    	}
		return $rs;
	}

	/**
	 * 获取举报信息
	 */
	 public function getDetail(){
	 	$informId = (int)Input('cid');
	 	$data = $this->alias('oc')
	 	             ->join('__SHOPS__ s','oc.shopId=s.shopId','left')
					 ->join('__USERS__ u','oc.informTargetId=u.userId','inner')
	 				 ->where("oc.informId=$informId")
	 				 ->find();
	 	if($data){
	 		if($data['informAnnex']!='')$data['informAnnex'] = explode(',',$data['informAnnex']);
			$data['userName'] = ($data['userName']=='')?$data['loginName']:$data['userName'];
		   
	 	}
	 	 return $data;
	 }

	

	 /**
	  * 处理
	  */
	 public function finalHandle(){
	 	$rd = array('status'=>-1,'msg'=>'无效的举报信息');
	 	$informId = (int)Input('cid');
	 	$finalResult = Input('finalResult');
	 	$informStatus = Input('informStatus');
	 	if($informId==0){
	 		return WSTReturn('无效的举报信息',-1);
	 	}
	 	//判断是否已经处理过了
	 	$rs = Db::name('informs')->alias('oc')
	 			   ->field('oc.informTargetId,oc.informStatus,oc.goodId,oc.shopId,oc.informTargetId')
	 			   ->where("oc.informId=$informId")
	 			   ->find();
	    if($informStatus == 3){
	    	 try{
	    	 	$data['isInform'] = 0;
	 	        $ers = Db::name('informs')->where('informTargetId='.$rs['informTargetId'])->delete();
	 	        $res = Db::name('users')->where('userId='.$rs['informTargetId'])->update($data);
	 	        if($ers!==false){
					//发站内用户信息提醒
		 	    	WSTSendMsg($rs['informTargetId'],"由于您被检验出恶意举报，您所有未处理举报商品已被取消并且已被禁止举报！",['from'=>3,'dataId'=>$informId]);                
					Db::commit();
					return WSTReturn('操作成功',2);
	 	        }
	 	    }catch(\Exception $e){
	 	    	Db::rollback();
	            return WSTReturn('操作失败',-1);
	 	    }
	    }
	 	if($rs['informStatus']!=1 && $rs['informStatus']!=2){
	 		$data = array();
	 		$data['finalHandleStaffId'] = session('WST_STAFF.staffId');
	 		$data['informStatus'] = $informStatus;
	 		$data['respondContent'] = Input('finalResult');
	 		$data['finalHandleTime'] = date('Y-m-d H:i:s');
	 		Db::startTrans();
		    try{
	 	        $ers = Db::name('informs')->where('informId='.$informId)->update($data);
	 	        if($ers!==false){
	 	        	//下架商品
	 	        	if($informStatus == 2){
			 			$m = new M();
			 			$m->illegal($rs['goodId'],1);
			 		}
		 	        
					//发站内用户信息提醒
		 	    	WSTSendMsg($rs['informTargetId'],"您举报的商品已有回复，请查看违规举报详情。",['from'=>3,'dataId'=>$informId]);                
					Db::commit();
					return WSTReturn('操作成功',1);
	 	        }
	 	    }catch(\Exception $e){
	 	    	Db::rollback();
	            return WSTReturn('操作失败',-1);
	 	    }
	 	}else{
	 	    return WSTReturn('操作失败，该举报状态已发生改变，请刷新后重试!',-1);
	 	}

	 }
}
