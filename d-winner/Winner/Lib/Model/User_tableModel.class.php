<?php
/*
 * @varsion		EasyWork系统 1.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */

class User_tableModel extends RelationModel{
	/*
	protected $_map = array(
        'username'=>'user',
        'password'=>'pwd',
    );
	*/
	
	protected $_link = array(
		'user_main'=>array(
			'mapping_type'=>HAS_ONE,
			'mapping_name'=>'user_main',
			'class_name'=>'user_main_table',
			'foreign_key'=>'user_id',
			'mapping_fields'=>'group_id,company_id,part_id',
		),
		'user_comy'=>array(
			'mapping_type'=>MANY_TO_MANY,
			'mapping_name'=>'user_comy',
			'class_name'=>'user_company_table',
			'foreign_key'=>'user_id',
			'relation_foreign_key'=>'company_id',
			'relation_table'=>'user_main_table',
			'mapping_fields'=>'name,access',
		),
		'user_part'=>array(
			'mapping_type'=>MANY_TO_MANY,
			'mapping_name'=>'user_part',
			'class_name'=>'user_part_table',
			'foreign_key'=>'user_id',
			'relation_foreign_key'=>'part_id',
			'relation_table'=>'user_main_table',
			'mapping_fields'=>'name,access',
		),
		'user_group'=>array(
			'mapping_type'=>MANY_TO_MANY,
			'mapping_name'=>'user_group',
			'class_name'=>'user_group_table',
			'foreign_key'=>'user_id',
			'relation_foreign_key'=>'group_id',
			'relation_table'=>'user_main_table',
			'mapping_fields'=>'name,access',
		)
	);
	
	//检查用户权限及登录方法
	public function checkUser($sess){
		$info = '';
		include($sess['path']);//包含对应的权限控制配置文件
		$menu = M('Menu');//实例化菜单表
		
		//获取登录用户是否存在
		$map['username'] = array('eq',$sess['user']);
		$map['id'] = array('eq',$sess['id']);
		$map['status'] = array('eq',1);
		$info = $this->relation(true)->where($map)->find();
		$menu = M('Menu');//实例化菜单表
		$access_type = $menu->where("code='".$sess['mode']."'")->getField('mode');//获取菜单设置的权限值分类
		unset($map);
		if($info){
			if($access_type==2){			//获取组别权限
				$access = $info['user_comy'][0]['access'];
			}elseif($access_type==3){		//获取公司权限
				$access = $info['user_part'][0]['access'];
			}else{							//获取部门权限
				$access = $info['user_group'][0]['access'];
			}
			if($access>=9999){
				return 'all';
			}
			
			if($role[0]=='pass'){
				return $info;
			}
			
			if(isset($role[$access])){//匹配到权限值
				$ja = array_intersect($role[$access],$sess['role']);
				if($ja){
					return $role[$access];
				}else{
					$ja = array_diff($sess['role'],$role[$access]);
					$ro = $ja[0];
					if($ro=='r'){
						return -1;
					}elseif($ro=='c'){
						return -2;
					}elseif($ro=='u'){
						return -3;
					}elseif($ro=='d'){
						return -4;
					}elseif($ro=='p'){
						return -5;
					}
					return -1;
				}
			}else{
				//获取开放用户ID
				$view = $menu->where("code='".$sess['mode']."'")->getField('view');
				$arr_view = unserialize($view);
				if(isset($role['user']) && in_array($sess['id'],$arr_view)){//匹配到对应的用户ID
					if(is_array($role['user'])){
						$ja = array_intersect($role['user'][$sess['id']],$sess['role']);
						if($ja){
							return $role['user'][$sess['id']];
						}else{
							$ja = array_diff($sess['role'],$role[$access]);
							$ro = $ja[0];
							if($ro=='r'){
								return -1;
							}elseif($ro=='c'){
								return -2;
							}elseif($ro=='u'){
								return -3;
							}elseif($ro=='d'){
								return -4;
							}elseif($ro=='p'){
								return -5;
							}
						}
					}elseif($role['user']=='a'){
						return $role['user'];
					}
				}
				return -1;
			}
		}else{
			if($role[0]=='pass'){
				return -98;
			}else{
				return -99;
			}
		}
		unset($info);
	}
}