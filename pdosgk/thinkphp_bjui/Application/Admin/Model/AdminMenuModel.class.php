<?php 
/*
 * 后台管理员模型
 */
namespace Admin\Model;
use Think\Model;
class AdminMenuModel extends Model {
	
	protected $_validate = array(
			array('name','require','菜单名不能为空！'), //默认情况下用正则进行验证
	//		array('listorder','number ','排序输入错误，请输入数字'),
	//		array('content','require','内容不能为空！'), //默认情况下用正则进行验证
	// 			array('name','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
	// 			array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
	// 			array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
	// 			array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式
	);

	public function nodeDrag($move_type, $parentid, $ids, $target_id = null){
		$menu_list = array();
		//获取该父级下的现有排序
		$map['parentid'] = $parentid;
		$map['id']	= array('notin', $ids);
		$menu_list_res = $this->where($map)->order('listorder, id')->field('id')->select();
		$array_ids = explode(',', $ids);
		if($menu_list_res){
			foreach ($menu_list_res as $key => $value) {
				if($target_id && $value['id'] == $target_id){
					if($move_type == 'prev'){
						//前面插入
						// $menu_list + $array_ids
						$menu_list = array_merge($menu_list, $array_ids);
						$menu_list[] = $value['id'];
					}elseif($move_type == 'next'){
						//后面插入
						$menu_list[] = $value['id'];
						$menu_list = array_merge($menu_list, $array_ids);
					}
				}else{
					$menu_list[] = $value['id'];
				}
			}
		}
		if($move_type == 'inner'){
			//尾部插入
			$menu_list = array_merge($menu_list, $array_ids);
		}
		return $menu_list;
	}
}
