<?php
class coreModelAdmin extends coreFrameworkModel 
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function checkuser($post){
		$username=$post['username'];
		$password=md5(md5($post['password']));
		$sql=SqlToolsClass::SelectItem("admin","username='$username' and password='$password'");
		return $this->GetRow($sql);
	}
	
	function getSystemConfig(){
		$sql=SqlToolsClass::SelectItem("config","id=1");
		return $this->GetRow($sql);
	}
	
	function saveSystemConfig($post){
		$data = $this->getSystemConfig();
		if ($data) {
			$sql=SqlToolsClass::EditData("config", $post, "id=1");
		}else{
			$post['id']=1;
			$sql=SqlToolsClass::InsertData('config',$post);
		}
		
		return $this->Execute($sql);
	}
	
	function saveManagerMenu($data,$id=null){
		
		$data['link_type']=1;
		
		if ($data['url']){
			$url_temp=explode("?",$data['url']);
			$url_temp=explode("&",$url_temp[1]);
			
			foreach ($url_temp as $v){
				$temp=explode("=", $v);
				if ($temp[0]=="o") $data['op']=$temp[1];
				if ($temp[0]=="t") $data['task']=$temp[1];
			}
			
			if ($data['task'] && !$data['op']) $data['op']="core";
			if (!$data['task'] && !$data['op']) $data['link_type']=2;
		}
		
		if (!$data['status']) $data['status']=2;
		if (!$data['execute']) $data['execute']=2;
		if (!$data['select']) $data['select']=2;
		if (!$data['insert']) $data['insert']=2;
		if (!$data['delete']) $data['delete']=2;
		if (!$data['other']) $data['other']=2;
		
		if ($id){
			$sql=SqlToolsClass::EditData("manager_menu", $data,"id=$id");
			return $this->Execute($sql);
		}else{
			$sql=SqlToolsClass::InsertData("manager_menu", $data);
			return $this->Execute($sql);
		} 
		
	}
	
	function getManagerMenu(){
		$sql=SqlToolsClass::SelectItem("manager_menu",null,"*",null,"sort desc,id asc");
		return $this->GetAll($sql);
	}
	
	function delManagerMenu($id){
		$sql=SqlToolsClass::DeleteData("manager_menu", "id=$id");
		return $this->Execute($sql);
	}
	function delMenu($id){
		$sql=SqlToolsClass::DeleteData("menu", "id=$id");
		return $this->Execute($sql);
	}
	//获取管理员用户
	function getAdminList($get){
		$join="left join ".SqlToolsClass::getTableName("admin_group")." as g on g.id=a.gid ";
		$sql=SqlToolsClass::SelectItem("admin as a",null,"a.*,g.title as group_title",$join,null,"a.id");
		return $this->createPage($sql);
	}
	//保存管理员信息
	function saveAdminData($data,$id){
		
		if ($data['password']){
			$data['password']=md5(md5($data['password']));
		}
		if (!$data['password'] && !$data['password2']) {
			unset($data['password']);
		}
		unset($data['password2']);
		if ($id){
			$sql=SqlToolsClass::EditData("admin", $data,"id=$id");
			return $this->Execute($sql);
		}else{
			$data['addtime']=date("Y-m-d G:i:s");
			$data['logintime']=date("Y-m-d G:i:s");
			$sql=SqlToolsClass::InsertData("admin", $data);
			return $this->Execute($sql);
		}
	}
	
	function getAdmin($id){
		
		$sql=SqlToolsClass::SelectItem("admin","id=$id");
		return $this->GetRow($sql);
	}
	
	function checkAdmin($username){

		$sql=SqlToolsClass::SelectItem("admin","username='$username'");

		return $this->GetRow($sql);

	}
	function delAdmin($id){

		$sql=SqlToolsClass::DeleteData("admin", "id=$id");

		return $this->Execute($sql);

	}
	function updataUserLogin($data,$id){
		$sql=SqlToolsClass::EditData("admin", $data, "id=$id");
		$this->Execute($sql);
	}
	
	function getMenu(){
		$sql=SqlToolsClass::SelectItem("menu");
		return $this->GetAll($sql);
	}
	
	function saveMenu($data,$id){
		if ($id){
			$sql=SqlToolsClass::EditData("menu", $data,"id=$id");
			return $this->Execute($sql);
		}else{
			$sql=SqlToolsClass::InsertData("menu", $data);
			return $this->Execute($sql);
		}
	}
	
	function getGroup($id,$status){
	
		if ($status){
			
			$sql=SqlToolsClass::SelectItem("admin_group",$where);
			return $this->GetAll($sql);
		}else{
			if ($id) $where="id=".$id;
			$sql=SqlToolsClass::SelectItem("admin_group",$where);
			return $this->GetRow($sql);
		}
	}
	
	function saveGroup($data,$id){
	
		if ($id){
			$sql=SqlToolsClass::EditData("admin_group", $data,"id=$id");
			return $this->Execute($sql);
		}else{
			$sql=SqlToolsClass::InsertData("admin_group", $data);
			return $this->Execute($sql);
		}
	}
	
	function delGroup($id){
		//先删子权限数据
		$sql=SqlToolsClass::DeleteData("admin_group_auth", "gid=$id");
		$re=$this->Execute($sql);
		$sql=SqlToolsClass::DeleteData("admin_group", "id=$id");
		$re*=$this->Execute($sql);
		return $re;
	}
	
	function saveAuth($id,$data){
		$sql=SqlToolsClass::DeleteData("admin_group_auth", "gid=$id");
		$this->Execute($sql);
		
		$sql=SqlToolsClass::InsertDatas("admin_group_auth", $data);
		return $this->Execute($sql);
	}
	
	function getAuth($id){
		$sql=SqlToolsClass::SelectItem("admin_group_auth","gid=$id");
		return $this->getAll($sql);
	}
}


?>