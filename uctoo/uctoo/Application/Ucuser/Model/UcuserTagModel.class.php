<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2015 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: UCT <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace Ucuser\Model;
use Think\Model;

/**+
 * Class UcuserTagModel 用户标签 兼容微信
 *
 * @package Ucuser\Model
 */
class UcuserTagModel extends Model
{


	protected $tableName = 'ucuser_tag';
	protected $_validate = array(
		array('name', '0,30', '标签长度不对,0-30之间', self::MUST_VALIDATE, 'length'),
		array('id', 'require', '缺少id', self::MUST_VALIDATE),
		array('mp_id', 'require', '缺少mp_id', self::MUST_VALIDATE),
	);
	protected $_auto     = array(
	);

	/**
	 * @param $tag 编辑或修改标签信息
	 *
	 * @return bool|mixed
	 */
	public function add_or_edit_tag($tag)
	{
		$ret = $this->get_tag_by_id_and_mp_id($tag['id'],$tag['mp_id']);
		if($ret)
		{
			$ret = $this->where(array('id'=>$tag['id'],'mp_id'=>$tag['mp_id']))->save($tag);
		}
		else
		{
			$ret = $this->add($tag);
		}
		return $ret;
	}

	/*
	 * 取标签信息列表
	 */
	public function get_tag_list($option)
	{
		$where_str = '';
		empty($option['mp_id']) || $where_arr[] = 'mp_id = ' . $option['mp_id'];
		empty($option['id']) || $where_arr[] = 'id = ' . $option['id'];
		empty($where_arr) || $where_str .= implode(' and ', $where_arr);

		$order_str = '';
		$order_arr[] = (empty($order_arr)?'id':$order_arr);
		$order_str .= implode(' , ', $order_arr);

		$option['page'] = (empty($option['page'])?1:$option['page']);
		$option['r'] = (empty($option['r'])?10:$option['r']);

		$ret['list']  = $this->where($where_str)->order($order_str)->page($option['page'], $option['r'])->select();
		$ret['count'] = $this->where($where_str)->count();
		return $ret;
	}

	/*
	 * 用id和mp_id 取标签信息
	 */
	public function get_tag_by_id_and_mp_id($id,$mp_id)
	{
		$ret = $this->where(array('id'=>$id,'mp_id'=>$mp_id))->find();
		return $ret;
	}

	/*
	 * 删除标签
	 */
	public function delete_tag($ids,$mp_id)
	{
		is_numeric($ids) &&  $ids = array($ids);
		if(empty($mp_id) || !is_array($ids))
		{
			return false;
		}
		return $this->where('id in ('.explode(',',$ids).') and mp_id = '.$mp_id)->delete();
	}

}