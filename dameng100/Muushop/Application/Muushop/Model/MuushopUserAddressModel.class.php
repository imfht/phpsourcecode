<?php

namespace Muushop\Model;
use Think\Model;

class MuushopUserAddressModel extends Model{
	//用户收货地址
	protected $tableName='muushop_user_address';
	protected $_validate = array(
		array('name','1,64','收货人姓名长度不符',1,'length'),
		array('phone','7,16','电话长度不符',2,'length'),
		array('phone','/^1[3|4|5|8][0-9]\d{4,8}$/','手机号码格式错误！','0','regex',1),
		array('province','2,16','省长度不符',2,'length'),
		array('city','2,16','市长度不符',2,'length'),
		array('district','2,16','县长度不符',2,'length'),
		array('address','require','详细地址不能为空！'),
		array('address','4,128','详细地址长度不符',1,'length'),
		
	);
	protected $_auto = array(
		array('modify_time', NOW_TIME, 3),
		array('status', '1', self::MODEL_INSERT),
	);

	public function add_or_edit_user_address($user_address)
	{
		if(!empty($user_address['id'])){
			$ret = $this->save($user_address);
		}else{
			$user_address['modify_time'] = time();
			$user_address['create_time'] = time();
			$ret = $this->add($user_address);
		}
		return $ret;
	}

	public function delete_user_address($ids)
	{
		is_array($ids) || $ids = array($ids);
		return $this->where('id in ('.implode(',',$ids).')')->delete();
	}

	public function get_user_address_list($map,$order='modify_time desc,create_time desc',$field='*')
	{
		$totalCount=$this->where($map)->count();
        if($totalCount){
            $list=$this->where($map)->order($order)->field($field)->select();
        }
        return array($list,$totalCount);
	}

	public function get_last_user_address_by_user_id($user_id)
	{
		return $this->where('user_id='.$user_id)->order('modify_time desc')->find();
	}

	public function get_user_address_by_id($id)
	{
		return $this->where('id = '.$id)->find();
	}
}

