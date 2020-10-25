<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */
 
class GroupAction extends Action {
	/**
		* 组别列表
		*@param $json    为NULL输出模板。为1时输出列表数据到前端，格式为Json
		*@examlpe 
	*/
    public function index($json=NULL){
		$Public = A('Index','Public');
		$Public->check('Group',array('r'));
		
		//main
		if(!is_int((int)$json)){
			$json = NULL;
		}
		if($json==1){
			$group = M('User_group_table');
			$info = $group->order('access desc')->select();
			$new_info = array();
			foreach($info as $t){
				if($t['status']==1){
					$t['status'] = '开启';
				}else{
					$t['status'] = '关闭';
				}
				$new_info[] = $t;
			}
			echo json_encode($new_info);
			unset($group,$info,$new_info);
		}else{
			$this->display();
		}
		unset($Public);
    }
	
	/**
		* 新增与更新数据
		*@param $act add为新增、edit为编辑
		*@param $go  为1时，获取post
		*@param $id  传人数据id
		*@examlpe 
	*/
	public function add($act=NULL,$go=false,$id=NULL){		
		//main
		$group = M('User_group_table');
		if($go==false){
			$this->assign('uniqid',uniqid());
			if($act=='add'){
				$this->assign('act','add');
				$this->display();
			}else{
				if(!is_int((int)$id)){
					$id = NULL;
					$this->show('无法获取ID');
				}else{
					$map['id'] = array('eq',$id);
					$info = $group->where($map)->find();
					$this->assign('id',$id);
					$this->assign('act','edit');
					$this->assign('info',$info);
					$this->display();
					unset($info);
				}
			}	
		}else{
			$data = $group->create();
			if($act=='add'){
				$Public = A('Index','Public');
				$role = $Public->check('Group',array('c'));
				if($role<0){
					echo $role; exit;
				}
				
				$add = $group->add($data);
				if($add>0){
					$this->json(NULL);
					echo 1;
				}else{
					echo 0;
				}
			}elseif($act=='edit'){
				$Public = A('Index','Public');
				$role = $Public->check('Group',array('u'));
				if($role<0){
					echo $role; exit;
				}
				
				if(!is_int((int)$id)){
					echo 0;
				}else{
					$map['id'] = array('eq',$id);
					$edit = $group->where($map)->save($data);
					unset($map);
					if($edit !== false){
						$this->json(NULL);
						echo 1;
					}else{
						echo 0;
					}
				}
			}
			unset($data,$Public);
		}
		unset($group);
	}
	
	/**
		* 删除数据
		*@param $id 数据ID
		*@examlpe 
	*/
	public function del($id){
		$Public = A('Index','Public');
		$role = $Public->check('Group',array('d'));
		if($role<0){
			echo $role; exit;
		}
		
		//main
		if(!is_int((int)$id)){
			echo 0;
		}else{
			$group = M('User_group_table');
			$map['id'] = array('eq',$id);
			$del = $group->where($map)->delete();
			unset($map);
			if($del){
				$this->json(NULL);
				echo 1;
			}else{
				echo 0;
			}
			unset($group);
		}
		unset($Public);
	}
	
	/**
		* 生成json文件
		*@param $back  为1时，返回数据
		*@examlpe 
	*/
	public function json($back=1){
		$Loop = A('Loop','Public');
		$Loop->table = 'User_group_table';
		$Loop->field = 'id,name as text';
		$Loop->order = 'access';
		$Loop->where = '`status`=1';
		$Loop->mode = 'noson';
		$Loop->isparnet = false;
		$Write = A('Write','Public');
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
	
		//main
    	$temp_path = RUNTIME_PATH.'/Temp/';
		if(file_exists($temp_path)){
			$dt = $sys->delFile($temp_path);
		}
		$group = M('User_group_table');
		$info = $Loop->rowLevel();
		$json_data = json_encode($info);
		$path = RUNTIME_PATH.'Data/Json';
		$put_json = $Write->write($path,$json_data,'Group_data');
		
		if($back==1){
			if($put_json){
				echo 1;
			}else{
				echo 0;
			}
		}
		
		unset($group,$info,$json_data,$path,$Loop,$Write,$sys);
	}
}