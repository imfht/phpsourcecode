<?php
class coreViewAdmin extends coreFrameworkView
{
	function __construct()
	{
		parent::__construct();
	}
	function index()
	{
		//中间区
		$this->tmp->assign("main_show_block","admin/com_core/index.html");
		$this->createMenu();
		//把区块发送到前
		$this->dp("admin/index");
	}

	function login(){
		$this->dp("admin/login");
	}

	//登录验
	function loginCheck(){
		$post=$this->GVar->fpost;
		
		if (!$post['username'] || !$post['password']){
			$url = "index.php?m=admin&t=login";
			MessageClass::ShowMessage(_lang_login_fail,$url,1,_lang_del_fail,null,10);
			exit;
		}
	
		$data=$this->model->checkUser($post);
		
		if ($data){
			$this->GVar->session['user_root']['id']=$data['id'];
			$this->GVar->session['user_root']['user_name']=$data['username'];
			$this->GVar->session['user_root']['gid']=$data['gid'];
			$this->GVar->SetSessionData();
		
			$ip=$_SERVER['REMOTE_ADDR'];
			//updata
			$login_data['logintime']=date("Y-m-d G:i:s");
			$login_data['loginip']=$ip;
			$login_data['session_id']=session_id();
		
			$this->model->updataUserLogin($login_data,$data['id']);
			//登陆记录
			$login['username']=$data['username'];
			$login['content']="User Login";
			//$data['username']=$this->user_root['user_name'];
			$login['op']="core";
			$login['task']="login";
			$login['type']="login";
			$login['ip']=$ip;
			$this->model->saveLog($login);
			
			$url = "index.php?m=admin&t=main";
	
			
			MessageClass::ShowMessage(_lang_login_success,$url,1,_lang_del_fail,null,2);
		}else{
			$url = "index.php?m=admin&t=login";
			MessageClass::ShowMessage(_lang_login_fail,$url,1,_lang_del_fail,null,10);
		}
		exit;
	}
	//后台首
	function main(){
		
		
		
		$this->tmp->assign("main_show_block","admin/com_core/main.html");
		$this->createMenu();
		//把区块发送到前
		$this->dp("admin/index");
	}
	//退出登
	function logOut(){
		unset($this->GVar->session['user_root']);
		$this->GVar->SetSessionData();
		$url = "index.php?m=admin&t=login";
		MessageClass::ShowMessage(_lang_logout_success,$url,1,_lang_del_fail,null,2);
	}

	//系统设
	function system(){
		$data=$this->model->getSystemConfig();
		
		$data['copyright']=$this->getKindEditor($data['copyright'] ,"copyright");
		
		$this->tmp->assign('data',$data);
		$this->tmp->assign("main_show_block","admin/com_core/system.html");
		$this->createMenu();
		//把区块发送到前
		$this->dp("admin/index");
	}
	//保存设
	function saveConfig(){
		$post=$this->GVar->fpost;
		
		if ($_FILES['music_url']['name']){
    		include _SITE_INCLUDE_CLASS_PATH."upfile.php";
    		$upfile=new upload();
    		$post['music_url']="/".$upfile->upMusic($_FILES['music_url']);
		}
		
		$re=$this->model->saveSystemConfig($post);
		$url = "index.php?m=admin&t=system";
		MessageClass::ShowMessage(_lang_save_success,$url,$re,_lang_save_fail,$url,2);
	}

	function menuManager(){
		$get=$this->GVar->fget;
		$datas=$this->model->getManagerMenu();
		
		
		foreach ($datas as $k=>$v){
			if ($v['pid']==0) {
				$tree[$k] = $v;
				foreach($datas as $k1=>$v1){
					if($v1['pid']==$v['id']){
						$tree[$k]['children'][] = $v1;
						
					}
				}
				
			}
		}
		
		
		
		$this->tmp->assign('tree',$tree);
		
		if ($get['id']){
			foreach ($datas as $v){
				if ($v['id']==$get['id']) {
					$data=$v;
					break;
				}
			}
		}
	
		$this->tmp->assign('data',$data);
		$this->tmp->assign('datas',$datas);
		$this->tmp->assign("main_show_block","admin/com_core/admin-menu.html");
		$this->createMenu();
		//把区块发送到前
		
		$this->dp("admin/index");
		echo 123;
	}

	function saveManagerMenu(){
		$get=$this->GVar->fget;
		$post=$this->GVar->fpost;
		$re=$this->model->saveManagerMenu($post,$get['id']);
		$url = "index.php?m=admin&t=menuManager";
		MessageClass::ShowMessage(_lang_save_success,$url,$re,_lang_save_fail,$url,2);
	}

	function delManagerMenu(){
		$get=$this->GVar->fget;
		$re=$this->model->delManagerMenu($get['id']);
		$url = "index.php?m=admin&t=menuManager";
		MessageClass::ShowMessage(_lang_del_success,$url,$re,_lang_del_fail,$url,2);
	}
	
	function delMenu(){
		$get=$this->GVar->fget;
		$re=$this->model->delMenu($get['id']);
		$url = "index.php?m=admin&t=menu";
		MessageClass::ShowMessage(_lang_del_success,$url,$re,_lang_del_fail,$url,2);
	}
	

	function adminList(){
		$get=$this->GVar->fget;
		$datas=$this->model->getAdminList($get);
	
		$this->tmp->assign('datas',$datas);
	
		$this->tmp->assign("page_menu",$this->model->page_obj->getPageMenu());
		$this->tmp->assign("main_show_block","admin/com_core/admin-list.html");
		$this->createMenu();
		//把区块发送到前
		$this->dp("admin/index");
	}

	function adminForm(){
		$get=$this->GVar->fget;
		if ($get['id']){
			$data=$this->model->getAdmin($get['id']);
			$this->tmp->assign('data',$data);
		}
		
		$group=$this->model->getGroup(null,true);
		
		$this->tmp->assign('group',$group);
		$this->tmp->assign("main_show_block","admin/com_core/admin-form.html");
		$this->createMenu();
		//把区块发送到前
		$this->dp("admin/index");
	}

	//保存管理员信
	function saveAdminData(){
		$get=$this->GVar->fget;
		$post=$this->GVar->fpost;
	
		if (!$get['id'] && !$post['username']){
			$url = "index.php?m=admin&t=adminForm";
			$re=1;
			MessageClass::ShowMessage(_lang_username_fail,$url,$re,_lang_save_fail,$url,2);
		}
	
		if ($post['password'] != $post['password2']){
			$url = "index.php?m=admin&t=adminForm&id=".$get['id'];
			$re=1;
			MessageClass::ShowMessage(_lang_password_fail,$url,$re,_lang_save_fail,$url,2);
		}
	
		if (!$get['id'] && !$post['password'] && !$post['password2']){
			$url = "index.php?m=admin&t=adminForm";
			$re=1;
			MessageClass::ShowMessage(_lang_password_none_fail,$url,$re,_lang_save_fail,$url,2);
		}
	
		if (!$get['id'] && $this->model->checkAdmin($post['username'])){
			$url = "index.php?m=admin&t=adminForm";
			$re=1;
			MessageClass::ShowMessage(_lang_usercheck_fail,$url,$re,_lang_save_fail,$url,2);
		}
	
		$re=$this->model->saveAdminData($post,$get['id']);
		$url = "index.php?m=admin&t=adminList";
		MessageClass::ShowMessage(_lang_save_success,$url,$re,_lang_save_fail,$url,2);
	}

	function delAdmin(){
		$get=$this->GVar->fget;
		$re=$this->model->delAdmin($get['id']);
		$url = "index.php?m=admin&t=adminList";
		MessageClass::ShowMessage(_lang_del_success,$url,$re,_lang_del_fail,$url,2);
	}
	//前台菜单管
	function menu(){

		$get=$this->GVar->fget;

		$datas=$this->model->getMenu();

		if ($get['id']){
			foreach ($datas as $v){
				if ($v['id']==$get['id']) {
					$data=$v;
					break;
				}
			}
		}
		//递归频
		$bread_class=$this->breadClass($datas,0);
	
		if ($get['pid']){
			$data['pid']=$get['pid'];
		}

		$this->tmp->assign('data',$data);
		$this->tmp->assign('datas',$datas);
		$this->tmp->assign('bread_class',$bread_class);
		$this->tmp->assign("main_show_block","admin/com_core/menu.html");
		$this->createMenu();
		//把区块发送到前
		$this->dp("admin/index");
	}

	function saveMenu(){
		$get=$this->GVar->fget;
		$post=$this->GVar->fpost;
		
		include _SITE_INCLUDE_CLASS_PATH."upfile.php";
		$upfile=new upload();
		
		if ($_FILES['menu_photo']){
		    $photo=$upfile->upimg($_FILES['menu_photo']);
		    if ($photo) {
		        $post['menu_photo']=$photo['photo_dir'].$photo['photo'];
		    }
		}
		
		if ($_FILES['menu_hover']){
		    $photo=$upfile->upimg($_FILES['menu_hover']);
		    if ($photo) {
		        $post['menu_hover']=$photo['photo_dir'].$photo['photo'];
		    }
		}
		
		$re=$this->model->saveMenu($post,$get['id']);
		$url = "index.php?m=admin&o=core&t=menu&pid=".$post['pid'];
		MessageClass::ShowMessage(_lang_save_success,$url,$re,_lang_save_fail,$url,2);
	}
	
	function group(){
		$get=$this->GVar->fget;
		$datas=$this->model->getGroup(null,true);
	
		$this->tmp->assign('datas',$datas);
	
		$this->tmp->assign("main_show_block","admin/com_core/admin-group.html");
		$this->createMenu();
		//把区块发送到前
		$this->dp("admin/index");
	}
	
	function groupForm(){
		$get=$this->GVar->fget;		
		
		$root=$this->model->getLeftMenuList();
		$root=$this->recursionMenu($root);
		
// 		print_r("<pre>");
// 		print_r($root);
// 		print_r("</pre>");
		
		
		if ($get['id']){
			$data=$this->model->getGroup($get['id'],false);
			$this->tmp->assign('data',$data);
			$auth=$this->model->getAuth($get['id']);
			
			foreach ($auth as $v){
				if (!$v['task']){
					$auth_data[$v['op']]['execute']=1;
				}else{
					$auth_data[$v['op']][$v['task']][$v['auth']]=1;
				}
			}
			$this->tmp->assign('$auth_data',$auth_data);
		}
		
// 		print_r("<pre>");
// 		print_r($auth_data);
// 		print_r("</pre>");
		
// 		print_r("<pre>");
// 		print_r($root);
// 		print_r("</pre>");

		foreach ($root as $k => $v){
			//根频道执行
			if ($auth_data[$v['op']]['execute']){
				$root[$k]['check']=true;
			}
			
			foreach ($v['menu'] as $key => $val){
				if ($auth_data[$v['op']][$val['task']]['execute']){
					$root[$k]['menu'][$key]['execute_check']=true;
				}
				
				if ($auth_data[$v['op']][$val['task']]['select']){
					$root[$k]['menu'][$key]['select_check']=true;
				}
				
				if ($auth_data[$v['op']][$val['task']]['insert']){
					$root[$k]['menu'][$key]['insert_check']=true;
				}
				
				if ($auth_data[$v['op']][$val['task']]['delete']){
					$root[$k]['menu'][$key]['delete_check']=true;
				}
				
				if ($auth_data[$v['op']][$val['task']]['other']){
					$root[$k]['menu'][$key]['other_check']=true;
				}
				
			}	
		} 
		
		$this->tmp->assign("root",$root);
		$this->tmp->assign("main_show_block","admin/com_core/admin-group-form.html");
		$this->createMenu();
		//把区块发送到前
		$this->dp("admin/index");
	}
	
	function saveGroup(){
		$get=$this->GVar->fget;
		$post=$this->GVar->fpost;
		
		$auth=$post['auth'];
		unset($post['auth']);
		$re=$this->model->saveGroup($post,$get['id']);
		if ($re){
			$gid=($get['id'])?$get['id']:$re;
			//处理权限数据
			foreach ($auth as $k=> $v){
				$op=$k;
				if ($v['execute']){
					//生成执行权限
					$data[]=array("op"=>$op,"task"=>"","auth"=>"execute","gid"=>$gid);
					unset($v['execute']);
					//循环下级数据
					foreach ($v as $key => $val){
						if ($val['execute']){
							$data[]=array("op"=>$op,"task"=>$key,"auth"=>"execute","gid"=>$gid);
						}
						if ($val['select']){
							$data[]=array("op"=>$op,"task"=>$key,"auth"=>"select","gid"=>$gid);
						}
						if ($val['insert']){
							$data[]=array("op"=>$op,"task"=>$key,"auth"=>"insert","gid"=>$gid);
						}
						if ($val['delete']){
							$data[]=array("op"=>$op,"task"=>$key,"auth"=>"delete","gid"=>$gid);
						}
						if ($val['other']){
							$data[]=array("op"=>$op,"task"=>$key,"auth"=>"other","gid"=>$gid);
						}
					}

				}
			}

			$this->model->saveAuth($gid,$data);
		}
		$url = "index.php?m=admin&t=group";
		MessageClass::ShowMessage(_lang_save_success,$url,$re,_lang_save_fail,$url,2);
	}
	
	function delGroup(){
		$get=$this->GVar->fget;
		$re=$this->model->delGroup($get['id']);
		$url = "index.php?m=admin&t=group";
		MessageClass::ShowMessage(_lang_del_success,$url,$re,_lang_del_fail,$url,2);
	}

	function adminPass(){
		$uid=$this->user_root['id'];
		
		if (!$uid){
			$url = "index.php?m=admin&t=main";
			$re=1;
			MessageClass::ShowMessage(_lang_no_auth,$url,$re,_lang_no_auth,$url,2);
		}

		$data=$this->model->getAdmin($uid);
		$this->tmp->assign('data',$data);
	
		$group=$this->model->getGroup(null,true);
	
		$this->tmp->assign('group',$group);
		$this->tmp->assign("main_show_block","admin/com_core/admin-form-pass.html");
		$this->createMenu();
		//把区块发送到前
		$this->dp("admin/index");
	}
	
	function savePass(){
		$post=$this->GVar->fpost;
		$uid=$this->user_root['id'];
		
		if (!$uid){
			$url = "index.php?m=admin&t=main";
			$re=1;
			MessageClass::ShowMessage(_lang_no_auth,$url,$re,_lang_no_auth,$url,2);
		}
		
		if ($post['password'] != $post['password2']){
			$url = "index.php?m=admin&t=adminPass";
			$re=1;
			MessageClass::ShowMessage(_lang_password_fail,$url,$re,_lang_save_fail,$url,2);
		}
		
		if (!$uid | !$post['password'] && !$post['password2']){
			$url = "index.php?m=admin&t=adminPass";
			$re=1;
			MessageClass::ShowMessage(_lang_password_none_fail,$url,$re,_lang_save_fail,$url,2);
		}
		
		$re=$this->model->saveAdminData($post,$uid);
		$url = "index.php?m=admin&t=main";
		MessageClass::ShowMessage(_lang_save_success,$url,$re,_lang_save_fail,$url,2);
	}
	
	function ajaxUploadImg(){
	     echo json_encode(array("id"=>1,"file_dir"=>"/uploads/up_files/2014/11/13/","file_name"=>"20141113121758354.jpg"));
	}
	function ShowMessage()
	{
		$msg = $this->GVar->session;
		$msg["url"] = $msg["success_url"];
		$msg["msg"] = $msg["success_msg"];
		$msg["use_script"] = 0;
		if (!$msg["result"])
		{
			$msg["url"] = $msg["fail_url"];
			$msg["msg"] = $msg["fail_msg"];
		}
	
		if (strtolower(substr($msg["url"],0,11))=="javascript:")
		{
			$msg["javascript"] = substr($msg["url"],11);
			$msg["use_script"] = 1;
		}
		$this->tmp->assign("msg",$msg);
		$this->dp("admin/message");
	}
}

?>