<?php
namespace app\admin\model;
use think\Model;

/**
 * 将角色授权给管理员
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class AuthGroupAccess extends Model
{
	/**
	 * 库名和表名一起配置(方便之后分库处理)
	 */
	protected $table = 'cthink.__AUTH_GROUP_ACCESS__';
	
	public function tagAccess($uid,$group_ids){
		if(is_array($group_ids)){
			foreach($group_ids as $value){
				$count = \think\Db::table($this->table)->where(['uid'=>$uid,'group_id'=>$value])->count();
				if($count == 0){
					\think\Db::table($this->table)->insert(['uid'=>$uid,'group_id'=>$value]);
				}
			}
		}
		return true;
	}
	
	/**
	 * 通过管理员id获取授权的角色
	 */
	public function getMembaerGroups($uid){
		return \think\Db::table($this->table)->where(['uid'=>$uid])->select();
	}
	
	/**
	 * 通过角色组id获取所包含的用户信息
	 */
	public function getGroupsMember($group_id){
		return \think\Db::table($this->table)->where(['group_id'=>$group_id])->select();
	}
	
	/**
	 * 删除用户和组的绑定关系
	 */
	public function removeBind($map){
		return \think\Db::table($this->table)->where($map)->delete();
	}
}
