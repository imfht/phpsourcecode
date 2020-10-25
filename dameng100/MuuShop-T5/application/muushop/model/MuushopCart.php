<?php
namespace app\muushop\model;

use think\Model;

/**
 * 购物车
 */
class MuushopCart extends Model{

	protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段

	public function editData($data)
	{	
		if(empty($data['id'])){
			//先查询购物车是否有相同参数商品,有只更新商品购买数量
			$have = $this->getDataBySkuid($data['sku_id'],$data['uid']);

			if($have['id']){
				$data['quantity'] = $have['quantity'] + $data['quantity'];
				$res = $this->save($data,['id'=>$have['id']]);
				$res = $have['id'];
			}else{
				$res = $this->save($data);
				$res = $this->id;
			}
		}else{
			$res = $this->save($data,['id'=>$data['id']]);
			$res = $data['id'];
		}
		
		return $res;
	}

	public function deleteData($ids,$uid)
	{
		!is_array($ids)&&$ids=explode(',',$ids);
		$map['id']=['in',$ids];
		$map['uid'] = $uid;
		$res = $this->where($map)->delete();
		return $res;
	}
	public function getDataBySkuid($sku_id,$uid)
	{
		$map['sku_id'] = $sku_id;
		$map['uid'] = $uid;
		$res = $this->where($map)->find();
		if($res) {
			$tmp_sku = explode(';', $res['sku_id']);
			unset($tmp_sku[0]);
			$res['sku'] = $tmp_sku;
			$res['product'] = model('muushop/MuushopProduct')->getDataBySkuid($res['sku_id']);
		}
		
		return $res;
	}

	public function getListByUid($uid)
	{
		$map['uid']=$uid;
		$list = $this->where($map)->select();

		foreach($list as &$v){
			$tmp_sku = explode(';', $v['sku_id']);
			unset($tmp_sku[0]);
			$v['sku'] = $tmp_sku;
			$v['product'] = model('muushop/MuushopProduct')->getDataBySkuid($v['sku_id']);
		}
		unset($v);
		return $list;
	}

	public function getCountByUid($uid)
	{
		$map['uid']=$uid;
		return $this->where($map)->count();
	}

	public function getDataByIds($ids,$uid)
	{
		!is_array($ids)&&$ids=explode(',',$ids);
		$map['id'] = ['in',$ids];
		$map['uid'] = $uid;
		$res = $this->where($map)->select();
		foreach($res as &$v){
			$tmp_sku = explode(';', $v['sku_id']);
			unset($tmp_sku[0]);
			$v['sku'] = $tmp_sku;
			$v['product'] = model('muushop/MuushopProduct')->getDataBySkuid($v['sku_id']);
		}
		unset($v);
		return $res;
	}
}

