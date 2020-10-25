<?php
/*
 * @varsion		Winner权限管理系统 3.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, d-winner, Inc.
 * @link		http://www.d-winner.com
 */
 
class MenuAction extends Action {
	/**
		* 菜单列表
		*@param $json    为NULL输出模板。为1时输出列表数据到前端，格式为Json
		*@examlpe 
	*/
    public function index($json=NULL){
		$Public = A('Index','Public');
		$Public->check('Menu',array('r'));
		
		//main
		if(!is_int((int)$json)){
			$json = NULL;
		}
		if($json==1){
			$menu = M('Menu');
			$user = M('User_table');
			$info = $menu->order('sort')->select();
			
			$new_info = array();
			$arr_mode = array(
				1=>'组别',
				2=>'公司',
				3=>'部门',
			);
			foreach($info as $t){
				$t['levels'] = $arr_mode[$t['mode']].$t['type'].$t['level'];
				$t['view'] = implode(',',unserialize($t['view']));
				$view = $user->where('id in('.$t['view'].')')->getField('username');
				if($t['code']){
					$t['role'] = '<a class="up-font-over" href="javascript:void(0);" onclick="openWin('.$t['id'].')">点击设置</a>';
				}else{
					$t['role'] = '';
				}
				
				if(strstr($t['view'],',')){
					$views = $user->field('GROUP_CONCAT(username) as username')->where('id in('.$t['view'].')')->find();
					$t['view'] = '<span title="'.$views['username'].'">'.$view.'...</span>';
				}else{
					if($view){
						$t['view'] = $view;
					}else{
						$t['view'] = '无';
					}					
				}
				if($t['status']==1){
					$t['status'] = '开启';
				}else{
					$t['status'] = '关闭';
				}
				if($t['_parentId']==0){
					unset($t['_parentId']);
					$new_info[] = $t;
				}else{
					$new_info[] = $t;
				}
			}
			echo '{"rows":'.json_encode($new_info).'}';
			unset($info,$new_info,$menu);
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
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
		
		//main
		$menu = M('Menu');
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
					$info = $menu->where($map)->find();
					$info['view'] = implode(',',unserialize($info['view']));
					if($info['_parentId']==0){
						$info['_parentId'] = '';
					}
					unset($map);
					$this->assign('id',$id);
					$this->assign('act','edit');
					$this->assign('info',$info);
					$this->display();
					unset($info);
				}
			}	
		}else{
			$data = $menu->create();
			if($data['view']){
				$data['view'] = serialize($data['view']);
			}else{
				$data['view'] = '';
			}
			
			if($data['_parentId']){
				$tdeep = $menu->where('id='.$data['_parentId'])->getField('deep');
				$data['deep'] = $tdeep+1;
			}
			if($act=='add'){
				$Public = A('Index','Public');
				$role = $Public->check('Menu',array('c'));
				if($role<0){
					echo $role; exit;
				}
				
				$add = $menu->add($data);
				if($add>0){
					if($data['code']){
						$path = CONF_PATH.'/Role/'.$data['code'].'Role.php';
						$content = '<?php'."\r\n"
						 . '$role = array('."\r\n"
							. "\t".'999=>array(\'r\',\'c\',\'u\',\'d\',\'p\',\'a\'),'."\r\n"
							. "\t".'\'user\'=>\'a\','."\r\n"
						 . ');';
						 $sys->putFile($path,$content);
					}
					$this->json(NULL);
					echo 1;
				}else{
					echo 0;
				}
			}elseif($act=='edit'){
				$Public = A('Index','Public');
				$role = $Public->check('Menu',array('u'));
				if($role<0){
					echo $role; exit;
				}
				
				if(!is_int((int)$id)){
					echo 0;
				}else{
					$map['id'] = array('eq',$id);
					$edit = $menu->where($map)->save($data);
					unset($map);
					if($edit !== false){
						$this->json(NULL);
						echo 1;
					}else{
						echo 0;
					}
				}
			}
			unset($Public,$data);
		}
		unset($menu);
	}
	
	/**
		* 删除数据
		*@param $id 数据ID
		*@examlpe 
	*/
	public function del($id){
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
		$Public = A('Index','Public');
		$role = $Public->check('Menu',array('d'));
		if($role<0){
			echo $role; exit;
		}
		
		//main
		if(!is_int((int)$id)){
			echo 0;
		}else{
			$menu = M('Menu');
			$map['id'] = array('eq',$id);
			$code = $menu->where($map)->getField('code');
			$del = $menu->where($map)->delete();
			unset($map);
			if($del){
				$path = CONF_PATH.'/Role/'.$code.'Role.php';
				if($code && file_exists($path)){
					$sys->delFile($path);
				}
				$this->json(NULL);
				echo 1;
			}else{
				echo 0;
			}
			unset($menu);
		}
		unset($Public);
	}
	
	/**
		* 检查版本
		*@param $serial 传入序列号
		*@examlpe 
	*/
	public function auto($serial=NULL){
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
		$sys->root = ITEM;
		$sys->charset = C('CFG_CHARSET');
		
		//main
		$config = M('Config');
		if($serial==NULL){
			$nowtime = time();
			$nowdate = date("Y-m-d H:i:s",$nowtime);
			$path = CONF_PATH.'version.txt';
			$ver = $sys->getFile($path);
			$arr_ver = explode(";\r\n",$ver);
			$arr_ver = array_filter($arr_ver);
			if($nowtime-strtotime($arr_ver[2])>864000){
				$arr_ver[0] = $arr_ver[0]?$arr_ver[0]:'Null';
				$arr_ver[1] = $arr_ver[1]?$arr_ver[1]:$nowdate;
				$contents = $arr_ver[0].";\r\n"
				 .$arr_ver[1].";\r\n"
				 .$nowdate.";\r\n";
				$sys->putFile($path,$contents);
				$serial = $config->where("keyword='CFG_APPID'")->getField('vals');
				$mail = $config->where("keyword='CFG_MAIL'")->getField('vals');
				echo "{version:'$arr_ver[0]',serial:'$serial',mail:'$mail'}";
			}else{
				echo 0;
			}
		}else{
			$data = array(
				'vals'=>$serial,
				'opts'=>$serial,
			);
			$config->where("keyword='CFG_APPID'")->save($data);
			echo 1;
		}	
	}
	
	/**
		* 生成json文件
		*@param $back  为1时，返回数据
		*@examlpe 
	*/
	public function json($back=1){
		$Loop = A('Loop','Public');
		$Loop->table = 'Menu';
		$Loop->field = 'id,_parentId,text,state,iconCls,url,level,sort,deep';
		$Loop->where = '';
		$Loop->isparnet = false;
		$Loop->mode = 'son';
		$Loop->order = 'deep,sort';
		$Write = A('Write','Public');
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
	
		//main
    	$temp_path = RUNTIME_PATH.'/Temp/';
		if(file_exists($temp_path)){
			$dt = $sys->delFile($temp_path);
		}
		$menu = M('Menu');
		$info = $Loop->rowLevel();
		array_unshift($info,array(
			'id'=>0,
			'text'=>' ',
		));
		//dump($info);
		$json_data = json_encode($info);
		$path = RUNTIME_PATH.'Data/Json';
		$put_json = $Write->write($path,$json_data,'Menu_data');
		
		if($back==1){
			if($put_json){
				echo 1;
			}
		}
		unset($info,$json_data,$menu,$Loop,$Write,$sys);
	}
	
	/**
		* 编辑权限配置文件
		*@param $id 	传入的数据ID
		*@examlpe 
	*/
	
	public function role($id,$go=0){
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
		$Public = A('Index','Public');
		
		//main
		$id = intval($id);
		if($go==0){
			$menu = M('Menu');
			$code = $menu->where('id='.$id)->getField('code');
			$path = CONF_PATH.'/Role/'.$code.'Role.php';
			$file = $sys->getFile($path);
			$this->assign('file',$file);
			$this->assign('code',$code);
			$this->assign('uniqid',uniqid());
			$this->assign('id',$id);
			$this->assign('path','/Conf/Role/'.$code.'Role.php');
			$this->display();
		}else{
			$role = $Public->check('Menu',array('u'));
			if($role<0){
				echo $role; exit;
			}
			$code = I('code');
			$data = I('file','',false);
			if(!preg_match("/^\<\?php/i",$data)){
				$data = "<?php\r\n".$data;
			}
			$path = $path = CONF_PATH.'/Role/'.$code.'Role.php';
			if($code){
				$edit = $sys->putFile($path,$data);
				if($edit==1){
					echo 1;
				}else{
					echo 0;
				}
			}else{
				echo 2;
			}
		}
	}
}