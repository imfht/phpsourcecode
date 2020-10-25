<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
class SystemController extends AdminController{
	public function _initialize(){
		$action = array(
				'permission'=>array('profile', 'changePassword', 'ajax_checkUsername'),
				//'allow'=>array('index')
		);
		B('Admin\\Behaviors\\Authenticate', '', $action);
	}

	/**
	 * 修改个人资料
	 * 		-只有修改昵称部分
	 */
	public function profile(){
		$userid = session('userid');
		if(IS_POST){
			$data['nickname'] = I('post.nickname');
			
			D('Admin')->where('userid='.$userid)->save($data);
			//更新成功后， 重新登录
			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','message'=>'操作成功'));
		}else{
			
			$detail = D('Admin')->where('userid='.session('userid'))->find();
			$this->assign('Detail', $detail);
			$this->display();
		}
	}
	/**
	 * 修改密码
	 */
	public function changePassword(){
		$userid = session('userid');
		if(IS_POST){
			
			$detail = D('Admin')->where('userid='.$userid)->field('username, password')->find();
			if ( hash_hmac('sha256', I('post.password_old'), $detail['username']) != $detail['password'] )
				$this->ajaxReturn(array('statusCode'=>300,'message'=>'旧密码输入错误'));
			$password_new = I('post.password_new');
			if(isset($password_new) && !empty($password_new)) {
				$data['password'] = hash_hmac('sha256', $password_new, $detail['username']);
				D('Admin')->where('userid='.$userid)->save($data);
			}
			//更新成功后， 重新登录
			$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','message'=>'密码更改成功','forwardUrl'=>'/admin.php/Publics/Logout/'));
		}else{
			$this->display();
		}
	}
	
	public function adminManage(){
		//检索条件
		if(I('post.username')){
			$this->username = $username = I('post.username');
			$map['username'] = array('like', "%$username%");
		}
		if(I('post.roleid')){
			$this->roleid = $roleid = I('post.roleid');
			$map['roleid'] = $roleid;
		}
		//排序
		if(I('post.orderField')){
			$this->orderField = $orderField = I('post.orderField');
			$this->orderDirection = $orderDirection = I('post.orderDirection') ? I('post.orderDirection') : 'asc';
			$order = $orderField . ' ' . $orderDirection;
		}else{
			//默认排序
			$order = 'userid desc';
		}
		
		//分页相关
		$page['pageCurrent'] = max(1 , I('post.pageCurrent'));
		$page['pageSize']= I('post.pageSize') ? I('post.pageSize') : 30 ;
		
		$totalCount = D('admin')->where($map)->count();
		$page['totalCount']=$totalCount ? $totalCount : 0;
		 
		$this->page_list = D('admin')->order('userid')->where($map)->order($order)->page($page['pageCurrent'], $page['pageSize'])->select();
		//获取角色
		$this->roles = S('role') ? S('role') : D('AdminRole')->get_role_list();
		$this->page = $page;
		
		$this->display();
	}

	/**
	 * 禁用,启用账号
	 */
	public function adminChangeStatus(){
		//只有学校管理员有权限
		$userid = I('get.userid');
		if($userid == 1){
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'该对象不可更改'));
		}
		//判断权限, 只能禁用和启用教师角色
		$map['userid'] = $userid;
		$detail = D('Admin')->where($map)->find();
		if(!$detail)
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误'));
	
		//修改状态
		$status = $detail['status'] == 0 ? 1 : 0;
		D('Admin')->where($map)->save(array('status' => $status));
		$this->ajaxReturn(array('statusCode'=>200,'message' => '操作成功'));
	}
	
    //判断用户名是否重复
    public function ajax_checkUsername(){
        if(IS_GET){
        	$username = I('get.username');
        	
        	$exist_username = D('Admin')->where(array('username' => $username))->find();
        	if($exist_username){
        		echo '{"error":"用户名已存在"}';
        	}else {
        		echo '{"ok":""}';
        	}
        	exit;
        }
        
        
    }
    
    /**
     * 管理员编辑
     */
	public function adminEdit(){
		$userid = I('get.userid','','intval');
		if(IS_POST){

			$info['nickname'] 	= I('post.nickname');
			$info['roleid'] 	= I('post.roleid');
			
			if(D('Admin')->create($info, 2)){
				D('Admin')->where('userid='.$userid)->save($info);
				$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminManage'));
			}else{
				$this->ajaxReturn(array('statusCode'=>300,'message' => D('Admin')->getError()));
			}
		}else{
	
			$this->Detail = D('Admin')->where('userid='.$userid)->find();
	
			//获取角色
			$this->roles = S('role') ? S('role') : D('AdminRole')->get_role_list();
			$this->display();
		}
	}
	
	/**
	 * 管理员添加
	 */
	public function adminAdd(){
		if(IS_POST){
			
			$info['username'] 	= I('post.username');
			$info['nickname'] 	= I('post.nickname');
			$info['roleid'] 	= I('post.roleid');
			$info['password'] 	= hash_hmac('sha256', '1q2w3e4', $info['username']);	//生成默认密码
			$info['card']		= 0;
			$info['lang']		= 0;
			
			if(D('Admin')->create($info, 1)){
				D('Admin')->add($info);
				$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminManage'));
			}else{
				$this->ajaxReturn(array('statusCode'=>300,'message' => D('Admin')->getError()));
			}
			
		}else{
	
			//获取角色
			$this->roles = S('role') ? S('role') : D('AdminRole')->get_role_list();
			$this->display('adminEdit');
		}
	}
	
	/**
	 * 默认角色admin不能删除
	 */
	public function adminDelete(){
		$userids = I('get.userid');
		$userids = explode(',', $userids);
		foreach ($userids as $userid){
			//过滤不需要删除的 角色 ID
			if($userid == 1)
				continue;
			//判断权限
			
			//删除角色,
			D('admin')->deleteUser($userid);
		}
	
		$this->ajaxReturn(array('statusCode'=>200,'message'=>'管理员撤销成功'));
	}
	
	/**
	 * 系统设置-管理员设置-重置密码   
	 */
	public function adminResetPassword(){
		
		$userid = I('get.userid','','intval');
		//不能修改超级管理员
		if($userid == 1){
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'该对象不可更改'));
		}
		//自己不能修改自己的角色
		if($userid == session('userid')){
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'重置自己的密码有啥意思嘛！'));
		}
		//修改规则
		$username = D('Admin')->where('userid='.$userid)->getField('username');
		//设置默认密码
		$password = '1q2w3e4';
		$data['password'] = hash_hmac('sha256', $password, $username);
		$result = D('admin')->where('userid='.$userid)->save($data);
		if($result){
			$this->ajaxReturn(array('statusCode'=>200,'message'=>'重置密码为:'.$password,'tabid'=>'System_adminUserLists'));
		}else{
			$this->ajaxReturn(array('statusCode'=>300,'message'=>'重置失败,可能密码重置前就是:'.$password));
		}
		
	}
    /**
     * 系统设置-角色列表 
     */
    public function adminRoleList(){
    	$DB=M('admin_role');
    	//检索条件
    	
    	//分页相关
    	$page['pageCurrent']=max(1,I('post.pageCurrent',0,'intval'));
    	$page['pageSize']=I('post.pageSize',20,'intval');
    	$totalCount = $DB->count();
    	$page['totalCount']= $totalCount ? $totalCount : 0;
    	
    	//取数据
    	$str=intval($page['pageCurrent']-1)*$page['pageSize'];
    	$roleList = $DB->page($page['pageCurrent'], $page['pageSize'])->select();
    	
    	$this->assign('page_list', $roleList);
    	$this->assign('page', $page);
    	$this->display();
    }
    /**
     * 系统设置-角色列表-添加角色 
     */
    public function adminRoleAdd(){
    	if(IS_POST){
    		$info = I('post.info');
    		
    		$info['disabled']=1;
    		$result = D('AdminRole')->add($info);
    		if(!$result){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>'添加角色失败，请重试。ErrorNo:0001'));
    		}
    		//更新角色缓存
    		D('AdminRole')->get_role_list();
    		$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminRoleList'));
    	}
    	$this->display('adminRoleEdit');
    }
    /**
     * 系统设置-角色列表-编辑角色 
     */
    public function adminRoleEdit(){
    	$roleid = I('get.roleid','','intval');
    	if(IS_POST){
    		$info = I('post.info');
    		$result = D('AdminRole')->where('roleid=' . $roleid)->save($info);
    		if(!$result){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>'保存角色信息失败，请重试。ErrorNo:0001'));
    		}

    		//更新角色缓存
    		D('AdminRole')->get_role_list();
    		$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminRoleList'));
    	}else{
	    	$this->Detail = D('AdminRole')->where('roleid='.$roleid)->find();
	    	$this->display();
    	}
    }
    /**
     * 系统设置-角色列表-删除角色 
     */
    public function adminRoleDelete(){
    	$DB = M();
    	$roleid = I('get.roleid','','intval');
    	//不允许删除超级管理员
    	if($roleid == 1)
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'不允许删除超级管理员'));
    	$result = D('AdminRole')->where('roleid='.$roleid)->delete();
    	
    	if(!$result){
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'删除角色失败，请重试。ErrorNo:0001'));
    	}

    	//删除权限表
    	M('admin_role_priv')->where('roleid='.$roleid)->delete();

    	//更新角色缓存
    	D('AdminRole')->get_role_list();
    	$this->ajaxReturn(array('statusCode'=>200,'tabid'=>'System_adminRoleList'));
    }
    
    /**
     * 系统设置-角色列表-禁用角色 
     */
    public function adminRoleForbid(){
    	$DB = M('admin_role');
    	$roleid = I('get.roleid','','intval');
    	$detail_role = D('AdminRole')->where('roleid='.$roleid)->find();
    	
    	if(!$roleid)
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'参数错误，请重试'));
    	$status = $detail_role['status'] ? 0 : 1;
    	//更新状态
    	$result = $DB->where('roleid='.$roleid)->save(array('status'=>$status));

    	if(!$result){
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'变更状态失败'));
    	}
    	$this->ajaxReturn(array('statusCode'=>200,'tabid'=>'System_adminRoleList'));
    }
    
    
    /**
     * 角色权限设置
     */
    public function adminPrivSetting(){
    	
    	$this->roleid = $roleid = I('get.roleid','','intval');
    	$array_menu = M('admin_menu')->index('id')->order('listorder, id')->select();
    	if(IS_POST){
    		//删除旧权限
    		//$this->ajaxReturn(array('statusCode'=>300,'message'=>'sadfasdf'));
			M('admin_role_priv')->where('roleid='.$roleid)->delete();
			$ids = I('post.ids');
			$data['roleid'] = $roleid;
			if($ids){
				$ids = explode(',', $ids);
				foreach ($ids as $menu_id){
					//取出id的设置
					$detail = $array_menu[$menu_id];
					$data['menuid'] = $detail['id'];
					$data['m'] = strtolower($detail['m']);
					$data['c'] = strtolower($detail['c']);
					$data['a'] = strtolower($detail['a']);
					M('admin_role_priv')->add($data);
				}
			}
			//这里用html里写的js来post， 结果也是在js里写的。
			//$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','message'=> 'haha', 'tabid'=>'System_adminRoleList'));
    	}else{

    		
    		//$menus = list_to_tree($array_menu, 'id' ,'parentid', 'children');
    		$map['roleid'] = $roleid;
    		foreach ($array_menu as $menu_id => $menu){
    		    unset($menu['icon']);
    			$json_priv[$menu_id] = $menu;
    			//判断该权限是否拥有
    			$map['menuid'] = $menu['id'];
    			$exist_priv = M('admin_role_priv')->where($map)->find();
    			if($exist_priv){
    				$json_priv[$menu_id]['checked'] = true;
    			}
    		}
    		$menus = list_to_tree($json_priv, 'id' ,'parentid', 'children');
    		$this->json_priv = json_encode($menus);
    		//var_dump($menus);exit;
    		/* $priv_data = M('admin_role_priv')->select(); //获取权限表数据
    		$modules = 'admin,announce,vote,system';
    		foreach ($result as $n=>$t) {
    			$result[$n]['cname'] = $t['name'];
    			$result[$n]['checked'] = ($this->op->is_checked($t,$roleid,$priv_data))? ' checked' : '';
    			$result[$n]['level'] = $this->op->get_level($t['id'],$result);
    			$result[$n]['parentid_node'] = ($t['parentid'])? ' data-tt-parent-id="'.$t['parentid'].'"' : '';
    		}
    		$str  = "<tr data-tt-id='\$id' \$parentid_node>
						<td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$cname</td>
					</tr>";
    			
    		$menu->init($result);
    		$this->categorys = $menu->get_tree(0, $str); */
    		
    		
    		$this->display();
    	}
    }
    
    /**
     * 菜单显示列表
     */
    public function adminNodeLists(){
		$DB = M();
    	$tree = new \Lain\Phpcms\tree();
    	$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
    	$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
    	
    	$result = $DB->table('db_admin_menu')->order('listorder ASC,id ASC')->select();
    	$array = array();
    	foreach($result as $r) {
    		$r['icon'] = $r['icon'] ? '<i class="fa fa-'.$r['icon'].'"></i>' : '';
    		$r['cname'] = '<span title="'. $r['c'] .'/'. $r['a'] .'">' . $r['name'] . '</span>';
    		$r['display_icon'] = $r['display'] ? '' : ' <img src ="/Public/images/gear_disable.png" title="不在菜单显示">';
    		$r['str_manage'] = '<a href="'.U('System/adminNodeAdd?parentid='.$r['id']).' " class="btn btn-green" data-toggle="dialog" data-width="520" data-height="290" data-id="dialog-mask" data-mask="true">添加下级菜单</a> <a class="btn btn-green" href="'.U('System/adminNodeEdit?id='.$r['id']).'" data-toggle="dialog" data-width="520" data-height="290" data-id="dialog-mask" data-mask="true">修改</a> <a href="'.U('System/adminNodeDelete?id='.$r['id']).'" class="btn btn-red" data-toggle="doajax" data-confirm-msg="确定要删除该行信息吗？">删</a> ';
    		$array[] = $r;
    	}
    	
    	$str  = "<tr data-id='\$id'>
			    	<td>\$id</td>
			    	<td>\$spacer\$cname \$display_icon</td>
			    	<td>\$listorder</td>
			    	<td>\$icon</td>
    				<td align='center'>\$str_manage</td>
    	</tr>";
    	$tree->init($array);
    	$this->categorys = $tree->get_tree(0, $str);
    	$this->display();
    }
    
    public function adminNodeListsNew(){
    	//取出菜单列表
    	$menu_list = D('AdminMenu')->field('id, name, parentid, c, a, icon, display, listorder')->order('listorder ASC,id ASC')->select();
    	$node_lists[0] = array('id' => 0, 'name' => '后台菜单', 'font' => array('color' => 'red'), 'open'=>true);
    	if($menu_list){
    		foreach ($menu_list as $key => $menu){
    			//第一级展开, 其余的收起
    			if($menu['parentid'] == 0){
    				$menu['open'] = true;
    			}else{
    				$menu['open'] = false;
    			}
    			$menu['id'] = (int)$menu['id'];
    			$menu['pId'] = (int)$menu['parentid'];
    			$menu['faicon'] = $menu['icon'] ? $menu['icon'] : 'cog';
    			//隐藏的显示灰色
    			if($menu['display'] != 1)
    				$menu['font'] = array('color' => '#999999');	
    			$node_lists[] = $menu;
    		}
    	}
    	
    	//$js_node_lists = '[{"id":1,"pid":0,"faicon":"rss","faiconClose":"cab","name":"表单元素","level":0,"tId":"ztree1_1","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":true,"isLastNode":false,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false},{"id":10,"pid":1,"url":"form-button.html","tabid":"form-button","faicon":"bell","pId":1,"name":"按钮","level":0,"tId":"ztree1_2","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":false,"isLastNode":false,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false},{"id":11,"pid":1,"url":"form-input.html","tabid":"form-input","faicon":"info-circle","pId":1,"name":"文本框","level":0,"tId":"ztree1_3","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":false,"isLastNode":false,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false},{"id":12,"pid":1,"url":"form-select.html","tabid":"form-select","faicon":"ellipsis-v","pId":1,"name":"下拉选择框","level":0,"tId":"ztree1_4","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":false,"isLastNode":false,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false},{"id":13,"pid":1,"url":"form-checkbox.html","tabid":"table","faicon":"soccer-ball-o","pId":1,"name":"复选、单选框","level":0,"tId":"ztree1_5","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":false,"isLastNode":false,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false},{"id":14,"pid":1,"url":"form.html","tabid":"form","faicon":"comments","pId":1,"name":"表单综合演示","level":0,"tId":"ztree1_6","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":false,"isLastNode":false,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false},{"id":2,"pid":0,"name":"表格","level":0,"tId":"ztree1_7","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":false,"isLastNode":false,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false},{"id":20,"pid":2,"url":"table.html","tabid":"table","faicon":"signal","pId":2,"name":"普通表格","level":0,"tId":"ztree1_8","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":false,"isLastNode":false,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false},{"id":21,"pid":2,"url":"table-fixed.html","tabid":"table-fixed","faicon":"rss-square","pId":2,"name":"固定表头表格","level":0,"tId":"ztree1_9","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":false,"isLastNode":false,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false},{"id":22,"pid":2,"url":"table-edit.html","tabid":"table-edit","faicon":"bookmark-o","pId":2,"name":"可编辑表格","level":0,"tId":"ztree1_10","parentTId":null,"open":false,"isParent":false,"zAsync":true,"isFirstNode":false,"isLastNode":true,"isAjaxing":false,"checked":false,"checkedOld":false,"nocheck":false,"chkDisabled":false,"halfCheck":false,"check_Child_State":-1,"check_Focus":false,"isHover":false,"editNameFlag":false}]';
    	$this->assign('js_node_lists', json_encode($node_lists));
    	$this->display();
    }
    
    //菜单编辑
    public function ajax_nodeEdit(){
    	if(IS_POST){
    		$id = I('post.id', '', 'intval');
    		
    		$info['name'] 		= I('post.name');
    		$info['c'] 			= I('post.class');
    		$info['a'] 			= I('post.action');
    		$info['icon'] 		= I('post.icon');
    		$info['listorder'] 	= I('post.listorder', '', 'intval');
    		$info['display'] 	= I('post.display');
    		
    		$result = D('AdminMenu')->where('id='.$id)->save($info);
    		if($result){
    			$this->ajaxReturn(array('flag' => true));
    		}else {
    			$this->ajaxReturn(array('flag' => false));
    		}
    	}
    }
    
    //菜单添加
    public function ajax_nodeAdd(){
    	if(IS_POST){
    		$parentid = I('post.pid', '', 'intval');
    		
    		//添加菜单
    		$data['parentid'] 	= $parentid;
    		$data['name'] 		= '新增菜单';
			$data['m']			= 'Admin';
    		$data['c'] 			= I('post.c');
    		$data['display'] 	= 1;
    		$data['listorder']	= 0;
    		$id = D('AdminMenu')->add($data);
    		
    		if($id){
    			$data['id'] = $id;
    			$data['faicon'] = 'cog';	//默认图标
    			$return['flag'] = true;
    			$return['data'] = $data;
    		}else{
    			$return['flag'] = false;
    		}
    		
    		$this->ajaxReturn($return);
    	}
    }
    
    //菜单移动
    public function ajax_nodeDrag(){
    	if(IS_POST){
    		$nodes = I('post.treeNodes');
            $target_node = I('post.targetNode');
            $move_type = I('post.moveType');
    		
    		$ids = '';
            if($nodes){
                foreach ($nodes as $value) {
                    $ids .= $ids ? ','.$value['id'] : $value['id'];
                }
            }
            $return['flag'] = false;
            switch ($move_type) {
                case 'prev':
                case 'next':
                    //更改排序
                    $menu_list = D('AdminMenu')->nodeDrag($move_type, $target_node['parentid'], $ids, $target_node['id']);
                    if($menu_list){
                        foreach ($menu_list as $key => $value) {
                            M('AdminMenu')->where(array('id' => $value))->save(array('listorder' => $key, 'parentid' => $target_node['parentid']));
                        }
                    }
                    $return['flag'] = true;
                    break;
                case 'inner':
                    $menu_list = D('AdminMenu')->nodeDrag($move_type, $target_node['id'], $ids);
                    if($menu_list){
                        foreach ($menu_list as $key => $value) {
                            M('AdminMenu')->where(array('id' => $value))->save(array('listorder' => $key, 'parentid' => $target_node['id']));
                        }
                    }
                    $return['flag'] = true;
                    break;
                default:
                    //# code...
                    break;
            }
            
            $this->ajaxReturn($return);
    	}
    }
    
    //菜单删除
    public function ajax_nodeDelete(){
    	if(IS_POST){
    		$id = I('post.id', '', 'intval');
    		
    		$result = D('AdminMenu')->where('id='.$id)->delete();
    		if($result){
    			$this->ajaxReturn(array('flag' => true));
    		}else {
    			$this->ajaxReturn(array('flag' => false));
    		}
    	}
    }
    
    //图标,查找带回
    public function adminNodeIcon(){
    	//取出图标集
    	//检索条件
		if(I('post.name')){
			$this->name = $name = I('post.name');
			$map['name'] = array('like', "%$name%");
		}
		
		//分页相关
		$page['pageCurrent'] = max(1 , I('post.pageCurrent'));
		$page['pageSize']= I('post.pageSize') ? I('post.pageSize') : 30 ;
		$page['totalCount'] = M('admin_icon')->where($map)->count();
    	$icons = M('admin_icon')->where($map)->select();
    	
    	$this->assign('page', $page);
    	$this->assign('page_list', $icons);
    	$this->display();
    }
    /**
     * 系统设置-节点设置-增加节点  
     */
    public function adminNodeAdd(){
    	if(IS_POST){
    		$info = I('post.info');
    		$info['icon'] = I('post.icon');
			$info['m'] = 'Admin'; // 增加节点时默认为后台.
    		
    		if(!D('AdminMenu')->create($info)){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>D('AdminMenu')->getError()));
    		}else{
	    		D('AdminMenu')->add($info);
				$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminNodeLists'));
    		}
    	}else{
    		//取出父级菜单信息
    		$parentid = I('get.parentid','','intval');
    		if($parentid)
    			$this->Detail = D('AdminMenu')->where('id='.$parentid)->field('c, display, icon')->find();
	    	$tree = new \Lain\Phpcms\tree();
	    	$result = D('AdminMenu')->select();
	    	$array = array();
	    	foreach($result as $r) {
	    		$r['cname'] = $r['name'];
	    		$r['selected'] = $r['id'] == $parentid ? 'selected' : '';
	    		$array[] = $r;
	    	}
	    	$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
	    	$tree->init($array);
	    	$this->select_categorys = $tree->get_tree(0, $str);
	    	$this->display('adminNodeEdit');
    	}
    }
    /**
     * 系统设置-节点设置-编辑节点 
     */
    public function adminNodeEdit(){
    	$DB = M('admin_menu');
    	$id = I('get.id','','intval');
    	if(IS_POST){
    		$info = I('post.info');
    		//新增图标
    		if(I('post.icon')){
    			$info['icon'] = I('post.icon');
    		}
    		if(!$DB->create($info)){
    			$this->ajaxReturn(array('statusCode'=>300,'message'=>$DB->getError()));
    		}else{
	    		$DB->where('id='.$id)->save($info);
	    		$this->ajaxReturn(array('statusCode'=>200,'closeCurrent'=>'true','tabid'=>'System_adminNodeLists'));
    		}
    		
    	}else{
	    	$this->Detail = $DB->where('id='.$id)->find();
	    	$tree = new \Lain\Phpcms\tree();
	    	$result = $DB->select();
	    	foreach($result as $r) {
	    		$r['cname'] = $r['name'];
	    		$r['selected'] = $r['id'] == $this->Detail['parentid'] ? 'selected' : '';
	    		$array[] = $r;
	    	}
	    	$str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
	    	$tree->init($array);
	    	$this->select_categorys = $tree->get_tree(0, $str);
	    	$this->display();
    	}
    }
    /**
     * 系统设置-节点设置-删除节点 
     */
    public function adminNodeDelete(){
    	$DB = D('AdminMenu');
    	$id = I('get.id','','intval');
    	$result = $DB->where('id='.$id)->delete();
    	if(!$result){
    		$this->ajaxReturn(array('statusCode'=>300,'message'=>'删除节点失败，请重试。ErrorNo:0001'));
    	}
    	$this->ajaxReturn(array('statusCode'=>200,'tabid'=>'System_adminNodeLists'));
    }
    
}