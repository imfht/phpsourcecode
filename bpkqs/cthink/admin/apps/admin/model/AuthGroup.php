<?php
namespace app\admin\model;
use think\Model;

/**
 * 角色组创建
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class AuthGroup extends Model
{
	/**
	 * 库名和表名一起配置(方便之后分库处理)
	 */
	protected $table = 'cthink.__AUTH_GROUP__';
	
	const TYPE_ADMIN                = 1;                   // 管理员用户组类型标识
    const MEMBER                    = 'auth_member';
    const AUTH_GROUP_ACCESS         = 'auth_group_access'; // 关系表表名
    const AUTH_EXTEND               = 'auth_extend';       // 动态权限扩展信息表
    const AUTH_GROUP                = 'auth_group';        // 用户组表名
    const AUTH_EXTEND_CATEGORY_TYPE = 1;              // 分类权限标识
    const AUTH_EXTEND_MODEL_TYPE    = 2; //分类权限标识
	
	/**
	 * 角色列表,有分页
	 */
	public function lists($page = 15,$map = []){
		$list = \think\Db::table($this->table)->where($map)->paginate($page);
		return $list;
	}
	
	/**
	 * 角色列表，无分页
	 */
	public function items(){
		$list = \think\Db::table($this->table)->where(['status'=>1])->select();
		return $list;
	}
	
	/**
	 * 通过id获取一条角色信息
	 * @param int $id 角色组id
	 */
	public function getFindOne($id){
		return \think\Db::table($this->table)->where(['id'=>$id])->find();
	}
	
	/**
	 * 添加角色组
	 */
	public function addGroup($data){
		return \think\Db::table($this->table)->insertGetId($data);
	}
	
	/**
	 * 编辑角色组
	 */
	public function editGroup($input){
		return \think\Db::table($this->table)->update($input);
	}
	
	/**
	 * 设置角色组的状态信息
	 */
	public function stateGroup($map,$data){
		return \think\Db::table($this->table)->where($map)->update($data);
	}
	
	/**
	 * 物理删除
	 */
	public function removeGroup($id){
		$map = explode(',',$id);
		return \think\Db::table($this->table)->delete($map);
	}
}
