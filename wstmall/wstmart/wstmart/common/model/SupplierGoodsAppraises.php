<?php
namespace wstmart\common\model;
use wstmart\common\validate\GoodsAppraises as Validate;
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
 * 评价类
 */
use think\Db;
class SupplierGoodsAppraises extends Base{
	
 	
	/**
	* 根据商品id取评论
	*/
	public function getById(){
		// 处理匿名
		$anonymous = (int)input('anonymous',1);
		$goodsId = (int)input('goodsId');
		$where = ['ga.goodsId'=>$goodsId,
				  'ga.dataFlag'=>1,
				  'ga.isShow'=>1];
		// 筛选条件
		$type = input('type');
		$filterWhere = '';
		switch ($type) {
			case 'pic':// 晒图
				$filterWhere = " ga.images <> '' ";
				break;
			case 'best':// 好评
				$filterWhere = "(ga.goodsScore+ga.serviceScore+ga.timeScore)>=15*0.9";
				break;
			case 'good':// 中评
				$filterWhere = "(ga.goodsScore+ga.serviceScore+ga.timeScore)>=15*0.6 and (ga.goodsScore+ga.serviceScore+ga.timeScore)<15*0.9";
				break;
			case 'bad':// 差评
				$filterWhere = "(ga.goodsScore+ga.serviceScore+ga.timeScore)<15*0.6";
				break;
		}
		$rs  = 	$this->alias('ga')
					 ->field('ga.id,ga.content,ga.images,ga.supplierReply,ga.replyTime,ga.createTime,ga.goodsScore,ga.serviceScore,ga.timeScore,ga.supplierId,ga.orderId,s.supplierName,u.userPhoto,u.loginName,u.userTotalScore,goodsSpecNames')
					 ->join('__USERS__ u','ga.userId=u.userId','left')
					 ->join('supplier_order_goods og','og.orderId=ga.orderId and og.id=ga.orderGoodsId and og.goodsId=ga.goodsId','inner')
					 ->join('suppliers s','ga.supplierId=s.supplierId','inner')
					 ->where($where)
					 ->where($filterWhere)
					 ->order('ga.id desc')
					 ->paginate(input('pagesize/d'))
					 ->toArray();
		foreach($rs['data'] as $k=>$v){
			// 格式化时间
			$rs['data'][$k]['createTime'] = date('Y-m-d',strtotime($v['createTime']));
			//$goodsSpecNames = str_replace('@@_@@','，',$v['goodsSpecNames']);
			$goodsSpecNames = explode('@@_@@',$v['goodsSpecNames']);
            $strName = [];
            if(is_array($goodsSpecNames)){

            	foreach ($goodsSpecNames as $key =>$vo){
	                if($vo!=''){
                        if(strpos($vo, '：') !== FALSE) {
                            $str = explode('：',$vo);
                            $strName[] = $str[1];
                        }
                        if(strpos($vo, ':') !== FALSE) {
                            $str = explode(':',$vo);
                            $strName[] = $str[1];
                        }
	                }
	            }
            }
            
            $rs['data'][$k]['goodsSpecNames'] = $strName;
			// 总评分
			$rs['data'][$k]['avgScore'] = ceil(($v['goodsScore'] + $v['serviceScore'] + $v['timeScore'])/3);
			if($anonymous){
				$rs['data'][$k]['loginName'] = WSTAnonymous($v['loginName']);
			}
			//获取用户等级
			$rrs = WSTUserRank($v['userTotalScore']);
			$rs['data'][$k]['rankImg']  = $rrs['userrankImg'];
			$rs['data'][$k]['rankName'] = empty($rrs['rankName'])?' ':$rrs['rankName'];

		}
		// 获取该商品 各评价数
		$eachApprNum = $this->getGoodsEachApprNum($goodsId);
		$rs['bestNum'] = $eachApprNum['best'];
		$rs['goodNum'] = $eachApprNum['good'];
		$rs['badNum'] = $eachApprNum['bad'];
		$rs['picNum'] = $eachApprNum['pic'];
		$rs['sum'] = $eachApprNum['sum'];
		if($rs!==false){
			return WSTReturn('',1,$rs);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	/**
	* 根据商品id获取各评价数
	*/
	public function getGoodsEachApprNum($goodsId){
		$rs = $this->field('(goodsScore+timeScore+serviceScore) as sumScore')->where(['dataFlag'=>1,'isShow'=>1,'goodsId'=>$goodsId])->select();
		$data = [];
		$best=0;
		$good=0;
		$bad=0;
		foreach($rs as $k=>$v){
			$sumScore = $v['sumScore'];
			// 计算好、差评数
			if($sumScore >= 15*0.9){
				++$best;
			}else if($sumScore < 15*0.6){
				++$bad;
			}
		}
		$data['best'] = $best;
		$data['bad'] = $bad;
		$data['good'] = count($rs)-$best-$bad;
		// 晒图评价数
		$data['pic'] = $this->where([['images','<>',''],['goodsId','=',$goodsId],['isShow','=',1],['dataFlag','=',1]])->count();
		// 总评价数
		$data['sum'] = $this->where(['dataFlag'=>1,'isShow'=>1,'goodsId'=>$goodsId])->count();
		return $data;
	}

}
