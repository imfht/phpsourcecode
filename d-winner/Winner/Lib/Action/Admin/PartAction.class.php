<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */

class PartAction extends Action {
	/**
		* 部门列表
		*@param $json    为NULL输出模板。为1时输出列表数据到前端，格式为Json
		*@examlpe 
	*/
    public function index($json=NULL){
		$Public = A('Index','Public');
		$Public->check('Part',array('r'));
		
		//main
		if(!is_int((int)$json)){
			$json = NULL;
		}
		if($json==1){
			$comy = M('User_company_table');
			$part = M('User_part_table');
			if(C('MORE_COMY')){
				$cinfo = $comy->field('concat_ws(\'\',\'100\',id) as id,name')->where('`status`=1 and `type`=0')->order('id asc')->select();
				$info = $part->order('access desc')->select();
				array_unshift($cinfo,array(
					'id'=>0,
					'name'=>'无所属公司',
				));
			}else{
				$info = $part->field('id,name,status,access,comment,sort')->order('access desc')->select();
			}
			
			//dump($cinfo);
			
			$new_info = array();
			foreach($info as $t){
				if($t['status']==1){
					$t['status'] = '开启';
				}else{
					$t['status'] = '关闭';
				}
				$cinfo[] = $t;
			}
			echo '{"rows":'.json_encode($cinfo).'}';
			unset($comy,$part,$cinfo,$info,$new_info);
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
		$part = M('User_part_table');
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
					$info = $part->where($map)->find();
					unset($map);
					$this->assign('id',$id);
					$this->assign('act','edit');
					$this->assign('info',$info);
					$this->display();
					unset($info);
				}
			}	
		}else{
			$data = $part->create();
			//dump($id);exit;
			if($act=='add'){
				$Public = A('Index','Public');
				$role = $Public->check('User',array('c'));
				if($role<0){
					echo $role; exit;
				}
				
				$add = $part->add($data);
				if($add>0){
					$this->json(NULL);
					echo 1;
				}else{
					echo 0;
				}
				unset($data,$Public);
			}elseif($act=='edit'){
				$Public = A('Index','Public');
				$role = $Public->check('User',array('u'));
				if($role<0){
					echo $role; exit;
				}
				
				if(!is_int((int)$id)){
					echo 0;
				}else{
					$map['id'] = array('eq',$id);
					$edit = $part->where($map)->save($data);
					unset($map);
					if($edit !== false){
						$this->json(NULL);
						echo 1;
					}else{
						echo 0;
					}
				}
				unset($data,$Public);
			}
			unset($part);
		}
	}
	
	/**
		* 删除数据
		*@param $id 数据ID
		*@examlpe 
	*/
	public function del($id){
		$Public = A('Index','Public');
		$role = $Public->check('Part',array('d'));
		if($role<0){
			echo $role; exit;
		}
		
		//main
		if(!is_int((int)$id)){
			echo 0;
		}else{
			$part = M('User_part_table');
			$map['id'] = array('eq',$id);
			$del = $part->where($map)->delete();
			unset($map);
			if($del){
				$this->json(NULL);
				echo 1;
			}else{
				echo 0;
			}
			unset($part);
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
		$Loop->table = 'User_part_table';
		$Loop->field = 'id,name as text';
		$Loop->mode = 'noson';
		$Loop->isparnet = false;
		if(C('MORE_COMY')){
			$Loop->where = '`status`=1 and `_parentId`>0';
		}else{
			$Loop->where = '`status`=1';
		}
		$Loop->order = 'id';
		$Write = A('Write','Public');
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
	
		//main
		$comy = M('User_company_table');
		$part = M('User_part_table');
		$path = RUNTIME_PATH.'Data/Json';
		$info = $Loop->rowLevel();
		$json_data = json_encode($info);
		$put_json3 = $Write->write($path,$json_data,'Part_data');
		
    	$temp_path = RUNTIME_PATH.'/Temp/';
		if(file_exists($temp_path)){
			$dt = $sys->delFile($temp_path);
		}
		
		$sele = $comy->field('id as oid,concat_ws(\'\',\'100\',id) as id,name')->where('`status`=1 and `type`=0')->select();
		$path = RUNTIME_PATH.'Data/Json/Part';
		$num = 0;
		foreach($sele as $t){
			$sinfo = $Loop->rowLevel($t['id']);
			$json_datas = json_encode($sinfo);
			$ww = $Write->write($path,$json_datas,$t['id'].'_data');
			if($ww){
				$num++;
			}
		}
		
		$path = RUNTIME_PATH.'Data/Json';
		$info = $comy->field('concat_ws(\'\',\'100\',id) as id,name as text')->where('`status`=1 and `type`=0')->select();
		foreach($info as $k=>$t){
			$pinfo = $part->field('id,name as text')->where('`status`=1 and _parentId='.$t['id'])->select();
			$info[$k]['children'] = $pinfo;
		}
		$json_data = json_encode($info);
		$put_json2 = $Write->write($path,$json_data,'Comy_Part_data');
		
		$json_data = json_encode(array('id'=>0,'text'=>''));
		$put_json = $Write->write($path,$json_data,'Empty_data');
		
		
		if($back==1){
			if($put_json){
				echo 1;
			}else{
				echo 0;
			}
		}
		unset($comy,$part,$sele,$pathm,$sinfo,$json_datas,$Loop,$Write,$sys);
		$num = 0;
	}
}