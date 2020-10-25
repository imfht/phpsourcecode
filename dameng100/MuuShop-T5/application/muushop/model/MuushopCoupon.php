<?php
namespace app\muushop\model;

use think\Model;

/**
 * 优惠卷
 */
class MuushopCoupon extends Model{
	
	protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段

	public function editData($data)
	{
		if(empty($data['id'])){
			$res = $this->save($data);
		}else{
			$res = $this->save($data,['id'=>$data['id']]);
		}
		return $res;
	}

	public function deleteData($ids)
	{
		is_array($ids) || $ids = array($ids);
		$map['id'] = ['in',implode(',',$ids)];
		return $this->where($map)->delete();
	}

	/**
	 * 优惠劵列表
	 */
	public function getListByPage($map,$order = 'id desc',$field='*',$r=20)
	{
		$list  = $this->where($map)->order('id desc')->field($field)->paginate($r,false,['query'=>request()->param()]);

		foreach($list as &$item){
			$item = $this->func_get_coupon($item);
		}
		unset($item);
		
		return $list;
	}

	public function getDataById($id)
	{
		$res  = $this->find($id);
		$res  = $this->func_get_coupon($res);
		return $res;
	}

	public function func_get_coupon($item)
	{
		if(!empty($item['rule'])) $item['rule'] = json_decode($item['rule'], true);
		return $item;
	}

	
	/*
	 * 设置密钥
	 */
	public function set_key($key)
	{
		$this->key = $key;
	}

	/*
	 * 获取密钥
	 */
	public function get_key()
	{
		return $this->key;
	}
}

