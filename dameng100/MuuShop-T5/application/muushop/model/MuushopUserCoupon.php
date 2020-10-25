<?php
namespace app\muushop\model;

use think\Model;

class MuushopUserCoupon extends Model{
	
	/*
	protected $_validate = array(
		array('brief','0,255','优惠卷描述长度不对',1,'length'),
		array('title','1,32','优惠券名称长度不对',3,'length'),
	);
	*/

	//自动写入创建和更新的时间戳字段
	protected $autoWriteTimestamp = true; 
	// 关闭自动写入update_time字段
    protected $updateTime = false;

	public function editData($data)
	{
		if(empty($data['id'] || $data['id']==0)){
			$res = $this->save($data);
		}else{
			$res = $this->save($data,['id'=>$data['id']]);
		}
		return $res;
	}

	public function deleteData($ids)
	{
		return $this->where('id in ('.implode(',',$ids).')')->delete();
	}

	/*
		用户优惠劵列表
	*/
	public function getListByPage($map,$order = 'id desc',$field='*',$r=20)
	{

		$list  = $this->where($map)->order('id desc')->field($field)->paginate($r,false,['query'=>request()->param()]);
		foreach($list as &$v){
			$v['info'] = json_decode($v['info'],true);
			$v['coupon_info'] = model('MuushopCoupon')->getDataById($v['coupon_id']);
			if( empty($v['order_id']) && $v['expire_time']!=0 && $v['expire_time']<time()){
				$v['status'] = 2;
			}else{
				$v['status'] = (empty($item['order_id'])?0:1);
			}
		}
		unset($v);
		return $list;
	}
	
	public function getList($map,$order = 'id desc',$field='*') 
	{
		$list  = $this->where($map)->order('id desc')->field($field)->select();
		foreach($list as &$v){
			$v['info'] = json_decode($v['info'],true);
			$v['coupon_info'] = model('MuushopCoupon')->getDataById($v['coupon_id']);
		}
		unset($v);
		return $list;
	}

	public function getDataById($id)
	{
		$map['id']=$id;
		$res = $this->where($map)->find();
		if($res){
			$res['info'] = json_decode($res['info'], true);
		}
		return $res;
	}

	public function func_get_user_coupon(&$item)
	{
		if(!empty($item['info'])) $item['info'] = json_decode($item['info'], true);
		if(!(empty($GLOBALS['_TMP']['paid_fee']))
			&& !empty($item['info']['rule']['min_price'])
			&& $GLOBALS['_TMP']['paid_fee']<$item['info']['rule']['min_price'] )
		{
			$item['out_limit_price'] = true;
		}
		if( empty($item['order_id']) && $item['expire_time']!=0 && $item['expire_time']<time()){
			$item['status'] = 2;
		}else{
			$item['status'] = (empty($item['order_id'])?0:1);
		}
	}

}

