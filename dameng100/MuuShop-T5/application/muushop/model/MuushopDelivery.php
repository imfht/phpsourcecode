<?php
namespace app\muushop\model;

use think\Model;

/**
 * 物流模板
 */
class MuushopDelivery extends Model
{
	/*
	protected $_validate = array(
		array('title', '1,32', '模板名称长度不对', 1, 'length'),
		array('brief', '0,256', '模板说明长度不对', 1, 'length'),

	);*/
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

	public function deleteData($ids){
		if(!is_array($ids)){
			$ids = '';
		}
		$map['id'] = ['in',implode(',',$ids)];
		$ret = $this->where($map)->delete();
		return $ret;
	}

	public function getListByPage($map,$order='id asc',$field='*',$r=20)
    {
        $list=$this->where($map)->order($order)->field($field)->paginate($r,false,['query'=>request()->param()]);
        if(!empty($list)) {
			foreach($list as $k => $c) {
				$list[$k] = $this->func_get_delivery($c);
			}
		}
        return $list;
    }

	public function getDataById($id){
		$map['id'] = $id;
		$res = $this->where($map)->find();
		$res = $this->func_get_delivery($res);
		return $res;
	}

	/*
	 * 处理数据库返回数据
	 */
	public function func_get_delivery($item)
	{
		if(!empty($item['rule'])){
			$item['rule'] = json_decode($item['rule'], true);
		}
		return $item;
	}


}

