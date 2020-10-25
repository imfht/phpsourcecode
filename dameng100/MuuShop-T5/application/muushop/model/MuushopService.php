<?php
namespace app\muushop\model;

use think\Model;

/**
 * 售后服务
 */
class MuushopService extends Model {

	const SERVICE_UNDER_NOT         = -1; //商家拒绝
	const SERVICE_UNDER_NEGOTATION  = 0; //退货（售后）申请中，商家拒绝后状态更改为-1已拒绝
	const SERVICE_NEGOTATION_OK     = 1; //退货中，商家同意退货后，买家可提交退货物流
	const SERVICE_GOOD_RETURN_OK    = 2; //卖家已退回商品，商家根据服务类型进行后续处理，如退款、换货等
	const SERVICE_REFUND_OK         = 3; //已收到退货，换货提交发货物流，退款处理退款信息
	const SERVICE_REFUND_ALL_OK		= 4; //售后服务完成
	const SERVICE_END  		        = 5; //售后服务完成

	protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段

	/*
	 * 编辑商品
	 */
	public function editData($data)
	{
		if(empty($data['id'])){
			$res = $this->save($data);
		}else{
			$res = $this->save($data,['id'=>$data['id']]);
		}
		return $res;
	}

	/*
	 * 获取订单列表
	 */
	public function getListByPage($map,$order='create_time desc',$field='*',$r=20)
	{
		$list  = $this->where($map)->order($order)->field($field)->paginate($r,false,['query'=>request()->param()]);
		foreach($list as &$val){
			$val = $res = $this->_after($val);
		}
		unset($val);
		
		return $list;
	}

	/*
	 * 获取详细
	 */
	public function getDataById($id)
	{	
		$map['id']=$id;
		$res = $this->where($map)->find();
		$res = $this->_after($res);
		return $res;
	}

	public function getDataByOrderIdAndProductId($order_id, $product_id)
	{
		$map['order_id'] = $order_id;
		$map['product_id'] = $product_id;
		$res = $this->where($map)->find();

		if($res){
			$res = $this->_after($res);
		} 
		return $res;
	}
	/**
	 * 状态码转文字
	 * @param  integer $status [description]
	 * @param  string 售后类型 exchange 换货 return 退货
	 * @return [type]          [description]
	 */
	public function statusStr($status = 0,$type = 'exchange')
	{
		switch($status){
    		case -1:
				$status_str = '商家拒绝';
    		break;
    		case 0:
				$status_str = '已申请，等待商家审核';
    		break;
    		case 1:
    			$status_str = '已确认，等待用户退货';
    		break;
    		case 2;
    			$status_str = '已退货，等待商家确认';
    		break;
    		case 3;
    			$status_str = '已收到退货，等待商家处理';
    		break;
    		case 4;
    			if($type = 'exchange') {
	    			$status_str = '商家已发货';
	    		}else{
	    			$status_str = '商家已退款';
	    		}
    		break;
    		case 5;
    			$status_str = '已完成';
    		break;
    	}

    	return $status_str;
	}

	private function _after($res)
	{
		if($res['type'] == 'exchange'){
			$res['type_str'] = '换货';
		}else{
			$res['type_str'] = '退货';
		}
		$res['info'] = json_decode($res['info'],true);
		$res['status_str'] = $this->statusStr($res['status']);
		$res['images'] = explode(',',$res['images']);


		return $res;
	}

}