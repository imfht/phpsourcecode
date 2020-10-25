<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\GoodsAppraises as validate;
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
 * 商品评价业务处理
 */
class GoodsAppraises extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		$where = 'p.shopId=g.shopId and gp.goodsId=g.goodsId and o.orderId=gp.orderId  and gp.dataFlag=1';
		$shopName = input('shopName');
     	$goodsName = input('goodsName');

	 	$areaId1 = (int)input('areaId1');
		if($areaId1>0){
			$where.=" and p.areaIdPath like '".$areaId1."%'";

			$areaId2 = (int)input("areaId1_".$areaId1);
			if($areaId2>0)
				$where.=" and p.areaIdPath like '".$areaId1."_".$areaId2."%'";

			$areaId3 = (int)input("areaId1_".$areaId1."_".$areaId2);
			if($areaId3>0)
				$where.=" and p.areaId = $areaId3";
		}


	 	if($shopName!='')
	 		$where.=" and (p.shopName like '%".$shopName."%' or p.shopSn like '%".$shopName."%')";
	 	if($goodsName!='')
	 		$where.=" and (g.goodsName like '%".$goodsName."%' or g.goodsSn like '%".$goodsName."%')";
	 	$sort = input('sort');
		$order = [];
		if($sort!=''){
			$sortArr = explode('.',$sort);
			$order = $sortArr[0].' '.$sortArr[1];
		}
		$rs = $this->alias('gp')->field('gp.*,g.goodsName,g.goodsImg,o.orderNo,u.loginName')
					->join('__GOODS__ g ','gp.goodsId=g.goodsId','left') 
		         	->join('__ORDERS__ o','gp.orderId=o.orderId','left')
		         	->join('__USERS__ u','u.userId=gp.userId','left')
		         	->join('__SHOPS__ p','p.shopId=gp.shopId','left')
		         	->where($where)
		         	->order($order)
		         	->order('id desc')
		         	->paginate(input('limit/d'))->toArray();
		return $rs;
	}
	public function getById($id){
		return $this->alias('gp')->field('gp.*,o.orderNo,u.loginName,g.goodsName,g.goodsImg')
					->join('__GOODS__ g ','gp.goodsId=g.goodsId','left') 
		         	->join('__ORDERS__ o','gp.orderId=o.orderId','left')
		         	->join('__USERS__ u','u.userId=gp.userId','left')
		         	->where('gp.id',$id)->find();
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$Id = input('post.id/d',0);
		$data = input('post.');
		$data['isShow'] = ((int)$data['isShow']==1)?1:0;
		WSTUnset($data,'createTime');
		Db::startTrans();
        try{
        	$validate = new validate();
		    if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError()); 
		    $result = $this->allowField(true)->save($data,['id'=>$Id]);
	        if(false !== $result){
	        	$goodsAppraises = $this->get($Id);
	        	$this->statGoodsAppraises($goodsAppraises->goodsId,$goodsAppraises->shopId);
	        	Db::commit();
	        	return WSTReturn("编辑成功", 1);
	        }else{
	        	return WSTReturn($this->getError(),-1);
	        }
	    }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn("编辑失败");
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d',0);
	    Db::startTrans();
        try{
		    $goodsAppraises = $this->get($id);
		    $goodsAppraises->dataFlag = -1;
		    $result = $goodsAppraises->save();
	        if(false !== $result){
	        	$this->statGoodsAppraises($goodsAppraises->goodsId,$goodsAppraises->shopId);
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }else{
	        	return WSTReturn($this->getError(),-1);
	        }
	    }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn("删除失败");
	}

	/**
	 * 重新统计商品
	 */
	public function statGoodsAppraises($goodsId,$shopId){
        $rs = Db::name('goods_appraises')->where(['goodsId'=>$goodsId,'isShow'=>1,'dataFlag'=>1])
               ->field('count(id) userNum,sum(goodsScore) goodsScore,sum(serviceScore) serviceScore, sum(timeScore) timeScore')
               ->find();
        $data = [];
        //商品评价数
        Db::name('goods')->where('goodsId',$goodsId)->update(['appraiseNum'=>$rs['userNum']]);
        //商品评价统计
        $data['totalScore'] = (int)$rs['goodsScore']+$rs['serviceScore']+$rs['timeScore'];
        $data['totalUsers'] = (int)$rs['userNum'];
        $data['goodsScore'] = (int)$rs['goodsScore'];
        $data['goodsUsers'] = (int)$rs['userNum'];
        $data['serviceScore'] = (int)$rs['serviceScore'];
        $data['serviceUsers'] = (int)$rs['userNum'];
        $data['timeScore'] = (int)$rs['serviceScore'];
        $data['timeUsers'] = (int)$rs['userNum'];
        Db::name('goods_scores')->where('goodsId',$goodsId)->update($data);
        //商家评价
        $rs = Db::name('goods_appraises')->where(['shopId'=>$shopId,'isShow'=>1,'dataFlag'=>1])
               ->field('count(userId) userNum,sum(goodsScore) goodsScore,sum(serviceScore) serviceScore, sum(timeScore) timeScore')
               ->find();
        $data['totalScore'] = (int)$rs['goodsScore']+$rs['serviceScore']+$rs['timeScore'];
        $data['totalUsers'] = (int)$rs['userNum'];
        $data['goodsScore'] = (int)$rs['goodsScore'];
        $data['goodsUsers'] = (int)$rs['userNum'];
        $data['serviceScore'] = (int)$rs['serviceScore'];
        $data['serviceUsers'] = (int)$rs['userNum'];
        $data['timeScore'] = (int)$rs['serviceScore'];
        $data['timeUsers'] = (int)$rs['userNum'];
        Db::name('shop_scores')->where('shopId',$shopId)->update($data);       
	}
	
}
