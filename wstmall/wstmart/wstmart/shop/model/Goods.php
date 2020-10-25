<?php
namespace wstmart\shop\model;
use wstmart\common\model\Goods as CGoods;
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
 * 商品类
 */
class Goods extends CGoods{
     
	
	
	/**
	 * 违规的商品
	 */
	public function illegalByPage(){
		$shopId = (int)session('WST_USER.shopId');
		$where = [];
		$where[] = ['shopId',"=",$shopId];
		$where[] = ['goodsStatus',"=",-1];
		$where[] = ['dataFlag',"=",1];
		$goodsType = input('goodsType');
		if($goodsType!='')$where['goodsType'] = (int)$goodsType;
		$c1Id = (int)input('cat1');
		$c2Id = (int)input('cat2');
		$goodsName = input('goodsName');
		if($goodsName != ''){
			$where[] = ['goodsName|goodsSn','like',"%$goodsName%"];
		}
		if($c2Id!=0 && $c1Id!=0){
			$where[] = ['shopCatId2',"=",$c2Id];
		}else if($c1Id!=0){
			$where[] = ['shopCatId1',"=",$c1Id];
		}

		$rs = $this->where($where)
			->field('goodsId,goodsName,goodsImg,goodsType,goodsSn,isSale,isBest,isHot,isNew,isRecom,illegalRemarks,goodsStock,saleNum,shopPrice,isSpec')
			->order('saleTime', 'desc')
			->paginate(input('limit/d'))->toArray();
		foreach ($rs['data'] as $key => $v){
			$rs['data'][$key]['verfiycode'] = WSTShopEncrypt($shopId);
		}
		return $rs;
	}
	
	/**
	* 修改商品状态
	*/
	public function changSaleStatus(){
		$shopId = (int)session('WST_USER.shopId');
		$allowArr = ['isHot','isNew','isBest','isRecom'];
		$is = input('post.is');
		if(!in_array($is,$allowArr))return WSTReturn('非法操作',-1);
		$status = (input('post.status',1)==1)?0:1;
		$id = (int)input('post.id');
		$rs = $this->where(["shopId"=>$shopId,'goodsId'=>$id])->setField($is,$status);
		if($rs!==false){
			return WSTReturn('设置成功',1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	/**
	 * 批量修改商品状态
	 */
	public function changeGoodsStatus(){
		$shopId = (int)session('WST_USER.shopId');
		//设置为什么 hot new best rec
		$allowArr = ['isHot','isNew','isBest','isRecom'];
		$is = input('post.is');
		if(!in_array($is,$allowArr))return WSTReturn('非法操作',-1);
		//设置哪一个状态
		$status = input('post.status',1);
		$ids = input('post.ids/a');
		$rs = $this->where([['goodsId','in',$ids],['shopId','=',$shopId]])->setField($is, $status);
		if($rs!==false){
			return WSTReturn('设置成功',1);
		}else{
			return WSTReturn($this->getError(),-1);
		}

	}
	
	/**
	 * 修改商品库存/价格
	 */
	public function editGoodsBase(){
		$goodsId = (int)Input("goodsId");
		$post = input('post.');
		$data = [];
		if(isset($post['goodsStock'])){
			$data['goodsStock'] = (int)input('post.goodsStock',0);
			if($data['goodsStock']<0)return WSTReturn('操作失败，库存不能为负数');
		}elseif(isset($post['shopPrice'])){
			$data['shopPrice'] = (float)input('post.shopPrice',0);
			if($data['shopPrice']<0.01)return WSTReturn('操作失败，价格必须大于0.01');
		}else{
			return WSTReturn('操作失败',-1);
		}
		$rs = $this->update($data,['goodsId'=>$goodsId,'shopId'=>(int)session('WST_USER.shopId')]);
		if($rs!==false){
			return WSTReturn('操作成功',1);
		}else{
			return WSTReturn('操作失败',-1);
		}
	}
	
	/**
	 *  预警修改预警库存
	 */
	public function editwarnStock(){
		$id = input('post.id/d');
		$type = input('post.type/d');
		$number = (int)input('post.number');
		$shopId = (int)session('WST_USER.shopId');
		$data = $data2 = [];
		$data['shopId'] =  $data2['shopId'] = $shopId;
		$datat=array('1'=>'specStock','2'=>'warnStock','3'=>'goodsStock','4'=>'warnStock');
		if(!empty($type)){
			$data[$datat[$type]] = $number;
			if($type==1 || $type==2){
				$data['goodsId'] = $goodsId = input('post.goodsId/d');
				$rss = Db::name("goods_specs")->where('id', $id)->update($data);
				//更新商品库存
				$goodsStock = 0;
				if($rss!==false){
					$specStocks = Db::name("goods_specs")->where(['shopId'=>$shopId,'goodsId'=>$goodsId,'dataFlag'=>1])->field('specStock')->select();
					foreach ($specStocks as $key =>$v){
						$goodsStock = $goodsStock+$v['specStock'];
					}
					$data2['goodsStock'] = $goodsStock;
					$rs = $this->update($data2,['goodsId'=>$goodsId]);
				}else{
					return WSTReturn('操作失败',-1);
				}
			}
			if($type==3 || $type==4){
				$rs = $this->update($data,['goodsId'=>$id]);
			}
			if($rs!==false){
				return WSTReturn('操作成功',1);
			}else{
				return WSTReturn('操作失败',-1);
			}
		}
		return WSTReturn('操作失败',-1);
	}
}
