<?php
namespace app\muushop\model;

use think\Model;

/**
 * 用户收货地址
 */
class MuushopUserAddress extends Model{
	//用户收货地址
	/*
	protected $_validate = array(
		array('name','1,64','收货人姓名长度不符',1,'length'),
		array('phone','7,16','电话长度不符',2,'length'),
		array('phone','/^1[3|4|5|8][0-9]\d{4,8}$/','手机号码格式错误！','0','regex',1),
		array('province','2,16','省长度不符',2,'length'),
		array('city','2,16','市长度不符',2,'length'),
		array('district','2,16','县长度不符',2,'length'),
		array('address','require','详细地址不能为空！'),
		array('address','4,128','详细地址长度不符',1,'length'),
	);*/
	protected $autoWriteTimestamp = true; //自动写入创建和更新的时间戳字段

	public function editData($data)
	{
		if(empty($data['id'] || $data['id'] = 0)){
			$res = $this->save($data);
		}else{
			$res = $this->save($data,['id' => $data['id']]);
		}
		return $res;
	}

	public function deleteData($ids)
	{
		is_array($ids) || $ids = array($ids);
		$map['id'] = ['in',implode(',',$ids)];
		return $this->where($map)->delete();
	}

	public function getList($map,$order='update_time desc,create_time desc',$field='*')
	{
        $list=$this->where($map)->order($order)->field($field)->select();

        return $list;
	}

	public function getLastByUid($uid)
	{
		return $this->where('uid='.$uid)->order('update_time desc')->find();
	}

	public function getDataById($id)
	{	
		return $this->where(['id'=>$id])->find();
	}
}

