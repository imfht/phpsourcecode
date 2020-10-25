<?php 
/** ***********************
 * 作者：卢逸 www.61php.com
 * 日期：2015/5/21
 * 作用：61php框架核心类
 ** ***********************/
class coreFrameworkController{
	function __construct(){
		global $GVar;
		$this->GVar=$GVar;
	}
	/*
	 * 实例化view
	 */
	function executeView(){
		global $path_view,$GVar;

		$className="{$GVar->fget['o']}View".ucfirst($GVar->fget['m']);

		if (!class_exists($className)){
			die(_lang_no_view);
		}
		return new $className();
	}
	/*
	 * 后台权限验证
	 */
	function checkAuth()
	{
		
		//如果有設定例外就執行例外
		if (method_exists($this,"setExceptionList"))
		{
			$this->setExceptionList();
		}

		global $GVar;
		$get=$GVar->fget;
	
		if (!$get['o']) $get['o']="core";
		if (!$get['t']) $get['t']="index";
		
		//NONE跳过验证
		if ($this->auth[$get['t']]=="NONE") return false;
		
		//跳转地址
		$url=$_SERVER['HTTP_REFERER'];
		
		if ($this->GVar->session['user_root']['id']){
			//用户已登录验证权限
			//第一步验证是否为数据库权限
			$gid=$GVar->session['user_root']['gid'];
			$auth=$this->getGroupAuth($gid);
			
			foreach ($auth as $v){
				if (!$v['task']){
					$auth_data[$v['op']]['execute']=1;
				}else{
					$auth_data[$v['op']][$v['task']][$v['auth']]=1;
				}
			}

			//检测默认是否存在执行权限
			if ($auth_data[$get['o']][$get['t']]['execute']){
				return false;
			}
			//说明不是默认执行页
			//取得该页所需要的权限
			$controller=$this->auth[$get['t']];
			if ($controller){
				$is_check=false;
				if (is_array($controller)){
					//一页多父页权限
					foreach ($controller as $v){
						$temp=explode("-", $v);
						$temp_op=$temp[1];
						$temp_task=$temp[2];
						$temp_auth=$temp[3];
						if ($auth_data[$temp_op][$temp_task][$temp_auth]) $is_check=true;
					}
						
				}else{
					$temp=explode("-", $controller);
					$temp_op=$temp[1];
					$temp_task=$temp[2];
					$temp_auth=$temp[3];
					if ($auth_data[$temp_op][$temp_task][$temp_auth]) $is_check=true;;
				}
	
				if ($is_check){
					return false;
				}else{
					//指定的权限群组没有
					MessageClass::ShowMessage(_lang_no_auth,$url,1,_lang_login_no,null,10);
				}
			}else{
				//没有指定权限
				MessageClass::ShowMessage(_lang_no_auth,$url,1,_lang_login_no,null,10);
			}
			return false;
		}else{
			MessageClass::ShowMessage(_lang_login_no,$url,1,_lang_del_fail,null,10);
		}
	
	}
	
	function getGroupAuth($id){
		global $db;
		$where[]="gid=$id";
		$where=implode(" and ", $where);
		$sql=SqlToolsClass::SelectItem("admin_group_auth",$where);
		return $db->getAll($sql);
	}
}
/*
 * 核心视图类
 */
class coreFrameworkView{
	
	function __construct()
	{
		global $GVar,$path_module,$Config;
		$this->GVar=$GVar;
		$this->Config=$Config;
		
		//实例化数据模型
		$path_model=$path_module."model.php";

		if (file_exists($path_model)) {
			require_once  $path_model;
			$className="{$this->GVar->fget['o']}Model".ucfirst($this->GVar->fget['m']);
			if (!class_exists($className)){
				die(_lang_no_model);
			}
			$this->model=new $className();
		}
		//载入模块语言包
		$language_file=$path_module."language".DS._DEFAULT_LANGUAGE.".php";
		if (file_exists($language_file)){
			require_once  $language_file;
		}
		
		//初始化模板引擎
		$this->tmp=new SmartyBC;
		$this->tmp->template_dir=_SITE_APPLICATION_TEMPLATE;
		$this->tmp->php_handling = SMARTY_PHP_ALLOW ;
		$this->tmp->compile_dir =_SITE_CACHE."smarty".DS."compile".DS;
		$this->tmp->cache_dir =_SITE_CACHE."smarty".DS."cache".DS;
		$this->tmp->left_delimiter = '<{';
		$this->tmp->right_delimiter = '}>';

		//模板web路径
		$this->tmp->assign("template_dir",substr(_SITE_APPLICATION_TEMPLATE,strlen(_SITE_ROOT.DS)));
		$this->tmp->assign("include_dir",substr(_SITE_INCLUDE_PATH,strlen(_SITE_ROOT.DS)));
		$this->tmp->assign("site_host",_SITE_HOST);
		$this->tmp->assign("version",_lang_version);

		$this->tmp->assign("config",$this->Config);
		$this->initialiseCms();
	}

	function dp($filename){
		//输出页面
		$gvar["fpost"] = $this->GVar->fpost;
		$gvar["frequest"] = $this->GVar->frequest;
		$gvar["fget"] = $this->GVar->fget;
		$gvar["cookie"] = $this->GVar->cookie;
		$this->tmp->assign("gvar",$gvar);
		$template_name=$filename.".html";
		$this->tmp->display($template_name);
	}
	function assign($name,$val){
		$this->tmp->assign($name,$val);
	}
	/*
	 * 菜单生成类
	*/
	function createMenu(){
		$menu=$this->model->getLeftMenuList();
		$menu=$this->recursionMenu($menu);
		
		$auth=$this->model->getGroupAuth($this->user_root['gid']);
		
		foreach ($auth as $v){
			if (!$v['task']){
				$auth_data[$v['op']]['execute']=1;
			}else{
				$auth_data[$v['op']][$v['task']][$v['auth']]=1;
			}
		}
	
		foreach ($menu as $k => $v){
			//根频道执行
			if (!$auth_data[$v['op']]['execute']){
				unset($menu[$k]);
			}
	
			foreach ($v['menu'] as $key => $val){
				if (!$auth_data[$v['op']][$val['task']]['execute']){
					unset($menu[$k]['menu'][$key]);
				}
			}
		}
		
		$this->tmp->assign("config",$this->Config);
        //初始化cms
        $this->initialiseCms();
		$this->tmp->assign("menu",$menu);
	}
	//左侧菜单整理
	function recursionMenu($data){
	
		$get=$this->GVar->fget;
	
		foreach ($data as $k=>$v){
			if (!$v['pid']) {
				if (($v['op']==$get['o'] && $v['op']) || (!$get['o'] && $v['op']=="core" )) $v['is_select']=true;
				$menu[$v['id']]=$v;
				unset($data[$k]);
			}
		}
	
		foreach ($data as $k=> $v) {
			if (($v['op']==$get['o'] && $v['op'] && $v['task']==$get['t']) || (!$get['o'] && $v['op']=="core"  && $v['task']==$get['t'])) $v['is_select']=true;
			$menu[$v['pid']]['menu'][$v['id']]=$v;
		}
	
		return $menu;
	}
	/*
	 * 编辑器
	*/
	function getKindEditor($value = "",$name = "content",$toolbar='default',$height=600,$uploadJson=null)
	{
		require_once(_SITE_INCLUDE_PATH."kindeditor".DS."kindEditor.php");
	
		$sBasePath = _WEB_INCLUDE_PATH . "kindeditor" . DS ;
		$okindEditor = new kindEditor($name) ;
		$okindEditor->BasePath	= $sBasePath ;
		$okindEditor->Height = $height;
		$okindEditor->Value = $value ;
		$okindEditor->ToolbarSet = $toolbar ;
		if(empty($uploadJson))
			$okindEditor->uploadJson = $sBasePath."php".DS."upload_json.php";
		else
			$okindEditor->uploadJson = $uploadJson;
	
		return $okindEditor->CreateHtml();
	}
	//递归分类数据
	function breadClass($datas,$pid,$temp=array(),$lv=0){
		//等级
		$lv++;
		foreach ($datas as $v){
			if ($pid==$v['pid']){
				$v['lv']=$lv;
				$temp[]=$v;
				$temp=$this->breadClass($datas,$v['id'],$temp,$lv);
			}
		}
		return $temp;
	}
	function initialiseCms(){
		//身份
		if ($this->GVar->session['user_root']) $this->user_root=$this->GVar->session['user_root'];
		
	}
}
/*
 * 核心数据类
*/
class coreFrameworkModel{
	function __construct()
	{
		global $db,$Config;
		$this->db=$db;
		$this->config=$Config;
	}
	/**
	 * 获取全部数据
	 * @param unknown $sql  SQL语句
	 * @param string $database 数据源，默认数据源default
	 * @return string
	 */
	function GetAll($sql,$database=null){
		$sql=$this->setDataBase($database,$sql);
		return $this->db->GetAll($sql);
	}
	/**
	 * 获取指定字段
	 * @param unknown $sql  SQL语句
	 * @param string $database 数据源
	 * @return string
	 */
	function GetOne($sql,$database=null){
		$sql=$this->setDataBase($database,$sql);
		return $this->db->GetOne($sql);
	}
	/**
	 * 获取单条数据
	 * @param unknown $sql  SQL语句
	 * @param string $database 数据源，默认数据源default
	 * @return string
	 */
	function GetRow($sql,$database=null){
		$sql=$this->setDataBase($database,$sql);
		return $this->db->GetRow($sql);
	}
	/**
	 * 执行sql语句
	 * @param unknown $sql  SQL语句
	 * @param string $database 数据源，默认数据源default
	 * @return string
	 */
	function Execute($sql,$database=null){
		$sql=$this->setDataBase($database,$sql);
		return $this->db->Execute($sql);
	}
	/**
	 * 设置新的数据源
	 * @param string $databaseName 数据源名称
	 * @param string $sql  需要处理的sql语句
	 * @return string $sql
	 */
	function setDataBase($databaseName=null,$sql){
			global $DataBase;

			//如果原本就是这个直接停止
			if ($databaseName=="default" || !$databaseName){
				if ($this->db->database=="default") return $sql;
				$this->db->host=_DATABASE_HOST;
				$this->db->name=_DATABASE_USER;
				$this->db->pass=_DATABASE_PASSWORD;
				$this->db->table=_DATABASE_NAME;
				$this->db->ut=_DATABASE_UT;
			}else{
				$sql=str_replace(_TABLE_FIRST_NAME, $DataBase[$databaseName]['first_name'], $sql);
				if ($this->db->database==$databaseName) {
					return $sql;
				}
				$this->db->host=$DataBase[$databaseName]['host'];
				$this->db->name=$DataBase[$databaseName]['user'];
				$this->db->pass=$DataBase[$databaseName]['password'];
				$this->db->table=$DataBase[$databaseName]['name'];
				$this->db->ut=$DataBase[$databaseName]['ut'];
			}
			$this->db->database=($databaseName)?$databaseName:"default";
			$this->db->connect();
			return $sql;
	}
	/**
	 * 分页方法
	 * @param unknown $sql  
	 * @param unknown $get 
	 * @param string $cols
	 * @param string $use_ado 
	 * @param string $page_count_max
	 * @param string $page_class_file
	 * @param string $database
	 */
	function createPage($sql,$get,$cols="*",$use_ado=false,$page_count_max=null,$page_class_file="page.php",$database=null){
		$get = $this->GVar->fget;
		$page_file=_SITE_INCLUDE_CLASS_PATH.$page_class_file;
		require $page_file;
		if (!$get['page']) $get['page']=1;
		if (!$cols) $cols="*";
		if (!$page_count_max) $page_count_max=$this->config['page_count_max'];
		$this->page_obj=new zPage($sql,$cols,$this->db,$use_ado,$page_count_max);
		$sql.=" limit ".$this->page_obj->this_lots_per_page*($get['page']-1).",".$this->page_obj->this_lots_per_page;
		return $this->GetAll($sql,$database);
	}
	function getLeftMenuList(){
		$where[] = "status = 1";
		$where = implode(" and ",$where);
		$sql = SqlToolsClass::SelectItem("manager_menu",$where,"*",null,"sort desc,id asc");
		$datas = $this->GetAll($sql);
	
		return $datas;
	}
	function getGroupAuth($id,$op=null,$task=null){
		$where[]="gid=$id";
		if ($op) $where[]="op='$op'";
		if ($task) $where[]="task='$task'";
		$where=implode(" and ", $where);
		$sql=SqlToolsClass::SelectItem("admin_group_auth",$where);
		return $this->getAll($sql);
	}
	function saveLog($data){
		$data['addtime']=date("Y-m-d G:i:s");
		$sql=SqlToolsClass::InsertData("log", $data);
		return $this->Execute($sql);
	}
}

?>