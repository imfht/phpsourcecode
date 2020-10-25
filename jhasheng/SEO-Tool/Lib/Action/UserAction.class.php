<?php
/**
 * @name 会员管理
 */
class UserAction extends CommonAction
{
	/**
	 * @name 会员列表
	 */
	public function listuser()
	{
		import ( 'ORG.Util.Page' );
		$pageSize = 10;
		$page = I('p',1);
		$userinfo = M('userinfo');
		$join = "sc_role ON sc_userinfo.roleid = sc_role.id";
		$this->userlist = $userinfo->join($join)->page($page,$pageSize)->select();
		$rowCount = $userinfo->count();
		$pageInfo = new Page($rowCount,$pageSize);
		$this->page = $pageInfo->show();
		//echo $userinfo->getLastSql();
		//var_dump($userinfo->select());
		$this->display();
	}
	/**
	 * @name 添加会员
	 */
	public function adduser()
	{
		if(IS_POST)
		{
			$data['username'] = I('username','',string);
			$data['pwd'] = md5(I('password','',string));
			$data['repwd'] = md5(I('repassword','',string));
			$data['roleid'] = I('id',0,int);
			$data['aliasname'] = I('aliasname','',string);
			$data['regtime'] = time();
			$data['userstatus'] = 1;
			if(empty($data['username']) || empty($data['pwd']) || empty($data['roleid']) || $data['pwd'] != $data['repwd'])
			{
				$this->error('参数错误');
			}
			else
			{
				$userinfo = M('userinfo');
				if($userinfo->add($data))
				{
					$this->success('添加成功!');
				}
				else
				{
					$this->error($userinfo->getDbError());
				}
			}
		}
		else
		{
			$role = M('role');
			$this->rolelist = $role->select();
			$this->url = U('User/adduser');
			$this->display();
		}
	}
	
	/**
	 * @name 添加角色
	 */
	public function addrole()
	{
		if(IS_POST)
		{
			$user = M('role');
			$user->create();
			if ($user->add()) 
			{
				$this->success('添加角色成功');
			}
			else
				$this->error('添加角色失败');
		}
		else
		{
			$this->display();
		}
	}
	/**
	 * @name 角色列表
	 */
	public function rolelist()
	{
		$user = M('role');
		$rs = $user->select();
		$this->rlist = $rs;
		$this->display();
	}
	/**
	 * @name 编辑角色
	 */
	public function editrole()
	{
		if (IS_POST) 
		{
			$id = I('role_id',0,int);
			if(!isset($id) || $id == 0)
			{
				$this->error('参数丢失');
			}
			else
			{
				$access = M('access');
				$access->delete('role_id = $id');
				$node_arr = $_POST['node_id'];
				for($i = 0; $i < count($node_arr); $i++)
				{
					$arr = explode('_', $node_arr[$i]);
					$data['role_id'] = $id;
					$data['node_id'] = $arr[0];
					$data['level'] = $arr[1];
					$access->add($data);
				}
				$this->success('添加成功');
			}

		}
		else
		{
			$user = M('node');

			$id = I('id',0,int);

			$join = "sc_access on sc_node.id = sc_access.node_id and sc_access.role_id = $id";
			$rs = $user->field('sc_node.name,sc_node.id,sc_node.pid,sc_node.title,sc_access.node_id')->join($join)->select();

			$this->list = genTree($rs);

			$this->role_id = $id;
			$this->display();
		}
	}
	/**
	 * @name 权限列表
	 */
	public function listnode()
	{
		$user = M('node');

		$this->list = genTree($user->select());
		$this->display();
	}
	/**
	 * @name 添加权限
	 */
	public function addnode()
	{
		$user = M('node');
		if (IS_POST) 
		{
			$user->create();
			if ($user->add()) 
			{
				$this->success('添加权限成功');
			}
			else
				$this->error('添加权限失败');
		}
		else
		{
			//print_r($this->getNode());
			//$this->addSiteModule();
			$module = M('module');
			$field = array('title','id','pid','level');
			$rs = $user->field($field)->select();
			$this->nodelist = $module->where(array('modlevel'=>1))->select();
			//$this->list = $this->getNode();
			$this->tree = genTree($rs);
			$this->display();
		}
	}
	
	/**
	 * @name 异步节点
	 */
	public function ajax()
	{
		$action = I('action','',string);
		$pid = I('modpid',0,int);
		$module = M('module');
		$where['modpid'] = array('eq',$pid);
		$where['moduleid'] = array('neq',$pid);
		$this->ajaxReturn($module->where($where)->field('modpid',true)->select(),'操作成功',1);
	}
	
	private function addSiteModule()
	{
		$array = $this->getNode();
		$data = array();
		foreach($array as $k => $v)
		{
			$data[] = array('modname'=>$k,'moddesc'=>$v['comment'],'modlevel'=>1,'modpid'=>0);
			$pid = count($data);
			foreach($v['methods'] as $k => $v)
			{
				$data[] = array('modname'=>$k,'moddesc'=>$v['comment'],'modlevel'=>2,'modpid'=>$pid);
				$_pid = count($data);
				foreach($v['methods'] as $k => $v)
				{
					$data[] = array('modname'=>$k,'moddesc'=>$v,'modlevel'=>3,'modpid'=>$_pid);
				}
			}
		}
		$module = M('module');
		$module->execute("TRUNCATE TABLE sc_module");
		for($i = 0;$i < count($data);$i++)
		{
			$data[$i]['moduleid'] = $i+1;
			$module->add($data[$i]);
		}
	}
	
	private function getNode()
	{
		$dir = LIB_PATH.'Action/';
		$new = array();
		if(is_dir($dir))
		{
			$files = glob($dir."*.class.php");
			foreach($files as $v)
			{
				if(is_dir($v))
				{
					continue;
				}
				else
				{
					$dis = explode(',',C('NOT_AUTH_MODULE'));
					$dis[] = 'Common';
					$module = basename($v,'Action.class.php');
					if(!in_array($module,$dis))
					{
						$new[$module] = $this->getFunction($module);
					}
				}
					
			}
		}
		$return[APP_NAME]['comment'] = APP_NAME;
		$return[APP_NAME]['methods'] = $new;
	//	print_r($return);
		return $return;
	}

	private function getFunction($module)
	{
		if(empty($module)) return null;
		$action = A($module);
		$class = new ReflectionClass($module."Action");
		preg_match('/@name (.*)[\s]/',$class->getDocComment(),$matches);
		$comment_class = $matches[1] ? $matches[1] : '未知' ;
		$return['comment'] = trim($comment_class);
		$functions = $class->getMethods(ReflectionMethod::IS_PUBLIC);
		$_functions = array(
			'_initialize','__construct','getActionName','isAjax','display','show','fetch','buildHtml','assign','__set','get','__get','__isset','__call','error','success','ajaxReturn','redirect','__destruct','doRequest','theme','dispatchJump',''
		);
		foreach ($functions as $func)
		{
			if(!in_array($func->getName(), $_functions))
			{
				preg_match('/@name (.*)[\s]/',$func->getDocComment(),$matches);
				$comment = $matches[1] ? $matches[1] : '未知' ;
				$customer_functions[$func->getName()] = trim($comment);
				//echo $func->getDocComment();exit;
			}
		}
		$return['methods'] = $customer_functions;
		return $return;
	}
}
?>