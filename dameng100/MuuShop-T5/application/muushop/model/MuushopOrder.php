<?php
namespace app\muushop\model;

use think\Model;

/**
 * 订单
 */
class MuushopOrder extends Model {

	//status 状态码
	const PAY_TYPE_NULL      	  = 0; //未设置付款方式
	const ORDER_WAIT_USER_PAY     = 1; //待付款
	const ORDER_WAIT_FOR_DELIVERY = 2; //待发货
	const ORDER_WAIT_USER_RECEIPT = 3; //已发货
	const ORDER_DELIVERY_OK       = 4; //已收货（确认收货）
	const ORDER_CANCELED          = 10; //已取消订单
	const ORDER_COMMENT_OK        = 12; //评论完成

	/*
	protected $_validate = array(
		array('title', '1,64', '分类标题长度不对', 1, 'length'),
		array('title_en', '0,128', '分类英文标题长度不对', 2, 'length'),

	);*/
	protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段

	/*
	 * 获取订单列表
	 */
	public function getListByPage($map,$order='create_time desc',$field='*',$r=20)
	{
		$list  = $this->where($map)->order($order)->field($field)->paginate($r,false,['query'=>request()->param()]);
		foreach($list as $val){
			$val = $this->func_get_corder($val);
		}
		return $list;
	}

	public function getCount($map)
	{
		$count = $this->where($map)->count();

		return $count;
	}

	/*
	 * 获取订单
	 */
	public function getDataById($id)
	{	
		$map['id']=$id;
		$res = $this->where($map)->find();
		$res = $this->func_get_corder($res);
		return $res;
	}
	/*
	 * 根据订单号获取订单
	 */
	public function getDataByOrderNO($order_no)
	{	
		$map['order_no']=$order_no;
		$res = $this->where($map)->find();
		$res = $this->func_get_corder($res);
		return $res;
	}

	/*
	 * 编辑订单
	 */
	public function editData($data)
	{
		if (empty($data['id'])){
			$res = $this->save($data);
			if($res) $res = $this->id;
		}else{
			$res = $this->save($data,['id'=>$data['id']]);
		}
		return $res;
	}

	/*
	 * 删除订单
	 */
	public function deleteData($ids)
	{
		if (!is_array($ids))
		{
			$ids = array($ids);
		}
		$map['id'] = ['in',implode(',',$ids)];
		$res = $this->where($map)->delete();
		return $res;
	}

	public function func_get_corder($item)
	{
		if(!empty($item['address'])) $item['address'] = json_decode($item['address'], true);
		if(!empty($item['products'])) $item['products'] = json_decode($item['products'], true);
		if(!empty($item['delivery_info'])) $item['delivery_info'] = json_decode($item['delivery_info'], true);
		return $item;
	}

	public function order_status_list_select()
	{
		return [
			['id' => 0, 'value' => '全部'],
			['id' => self::ORDER_WAIT_USER_PAY, 'value' => '待付款'],
			['id' => self::ORDER_WAIT_FOR_DELIVERY, 'value' => '待发货'],
			['id' => self::ORDER_WAIT_USER_RECEIPT, 'value' => '已发货'],
			['id' => self::ORDER_DELIVERY_OK, 'value' => '已收货'],
			['id' => self::ORDER_CANCELED, 'value' => '已取消'],
			['id' => self::ORDER_COMMENT_OK, 'value' => '已完成'],
		];
	}

	public function order_status_config_select()
	{
		return [
			self::ORDER_WAIT_USER_PAY=> '待付款',
			self::ORDER_WAIT_FOR_DELIVERY=> '待发货',
			self::ORDER_WAIT_USER_RECEIPT=> '已发货',
			self::ORDER_DELIVERY_OK => '已收货',
			self::ORDER_CANCELED=> '已取消',
			self::ORDER_COMMENT_OK=> '已完成',//评价完成
		];
	}

	/**
	 * 状态码转文字
	 * @param  integer $status [description]
	 * @param  string 售后类型 exchange 换货 return 退货
	 * @return [type]          [description]
	 */
	public function statusStr($status = 0)
	{
		switch($status){
    		case -1:
				$status_str = '已删除';
    		break;
    		
    		case self::ORDER_WAIT_USER_PAY:
    			$status_str = '待付款';
    		break;
    		case self::ORDER_WAIT_FOR_DELIVERY;
    			$status_str = '待发货';
    		break;
    		case self::ORDER_WAIT_USER_RECEIPT;
    			$status_str = '已发货';
    		break;
    		case self::ORDER_DELIVERY_OK;
	    		$status_str = '已收货';
    		break;
    		case self::ORDER_CANCELED;
    			$status_str = '已取消';
    		break;
    		case self::ORDER_COMMENT_OK;
    			$status_str = '已完成';
    		break;
    		default:
    			$status_str = '状态错误,状态码'.$status;
    	}
    	return $status_str;
    }
    /**
     * 处理支付平台的异步通知
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function notify($data)
    {
        $order = $this->getDataByOrderNO($data['order_no']);

        if($order){
        	$v['id'] = $order['id'];
        	$v['status'] = self::ORDER_WAIT_FOR_DELIVERY;
	        $v['paid'] = 1;
	        $v['paid_time'] = time();

	        $res = $this->editData($v);
        }else{
        	$res = 0;
        }
        
        return $res;
    }
}

