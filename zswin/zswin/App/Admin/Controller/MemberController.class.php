<?php
namespace Admin\Controller;
use User\Api\UserApi;


// 后台用户模块
class MemberController extends CommonController {
	
	
	
	function _filter(&$map) {
		//$map['id'] = array('egt', 2);
		$map['nickname'] = array('like', "%" . $_POST['nickname'] . "%");
	}
    function hasRole($ids) {
    	$condition = array ('user_id' => array ('in', explode ( ',', $ids ) ) );
 	  
 	    $rs = D('role_user')->where($condition )->getField('role_id');
        
        if (isset($rs)) {
            return true;
        }
        return false;
    }
   
    
    //重置密码
	public function resetPwd() {
		    $id = I('post.id');
           
            $newpassword = I('post.password');
          
            $data['password'] = $newpassword;
           
            empty($newpassword) && $this->mtReturn(300,'请输入新密码');
           
       
		if (strlen($newpassword) < 6) {
			$this->mtReturn(300, '密码长度必须大于6个字符！');
			
		}
		
	    $Api = new UserApi();
            $res = $Api->updateInfo($id, 'admin', $data);
            if ($res['status']) {
               // $this->success('修改密码成功！');
               
			    $this->mtReturn(200,'密码修改成功！');
			  
            } else {
            	
			    $this->mtReturn(300,$res['info']);
			    
               
            }
		
	}
	public function password()
	{
		$this->display();
	}

    public function outxls(){
    
     	
     	
       
	   $filename='用户列表';
       $map=$this->_search();
       
       if($_REQUEST ['ids']!='all'){
       	 $map['id'] = array('in', explode(',', $_REQUEST ['ids']));
       }
        $volist = D('Member')->where($map)->field('nickname,reg_time')->select();
       foreach ($volist as $key =>$vo){
			
			
			$volist[$key]['create_time']=date("Y-m-d",$vo['reg_time']);
			
			
		}
		
		$headArr=array("账号","创建日期");
    
		$this->xlsout($filename,$headArr,$volist);
        
		
    }

	
	
	protected function addRole($userId,$roleId) {
		//新增用户自动加入相应权限组
		$RoleUser = M("RoleUser");
		$RoleUser->user_id = $userId;
		$RoleUser->role_id = $roleId;
		$RoleUser->add();
	}
	protected function editRole($userId,$roleId) {
		//新增用户自动加入相应权限组
		$RoleUser = M("RoleUser");
	   $data['role_id'] = $roleId;
		
		
		if(!$RoleUser->where("user_id=$userId")->save($data)){
			$data['user_id'] = $userId;
			$RoleUser->add($data);
		}
	}
protected function addMrole($userId,$roleId) {
		//新增用户自动加入相应权限组
		$RoleUser = M("MroleUser");
		$RoleUser->user_id = $userId;
		$RoleUser->role_id = $roleId;
		$RoleUser->add();
	}
	protected function editMrole($userId,$roleId) {
		//新增用户自动加入相应权限组
		$RoleUser = M("MroleUser");
		$data['role_id'] = $roleId;
		
		
		if(!$RoleUser->where("user_id=$userId")->save($data)){
			$data['user_id'] = $userId;
			$RoleUser->add($data);
		}
	}
	public function _before_add() {
		$role = D("Role");
		$classTree = $role->field('id,name,pid')->select();
		$list = list_to_tree($classTree,'id','pid','_child',0);
		$this->assign('list', $list);
		$Mrole = D("Mrole");
		$mclassTree = $Mrole->field('id,name,pid')->select();
		$mlist = list_to_tree($mclassTree,'id','pid','_child',0);
		$this->assign('mlist', $mlist);
		
	}
public function rolelist(){
	
	    $role = D("Role");

        $map=$this->_search('Role');
	    
	   
	    $map['status']=1;
	   
		$this->_list($role,$map);
		$this->display();
	
}


	function edit() {
		
		
		$id = $_REQUEST ['id'];
		clean_query_user_cache($id,array('nickname','email','username'));
		$vo=query_user(array('uid','nickname','username','email','score'), $id);
		
		$role = D("Role");
		$classTree = $role->field('id,name,pid')->select();
		$list = list_to_tree($classTree,'id','pid','_child',0);
		$RoleUser = M("RoleUser");
		$roleidList = $RoleUser->where('user_id='.$id)->find();
		$roleid = $roleidList['role_id'];
		$vo['roleid'] = $roleid;
		
		$mrole = D("Mrole");
		$mclassTree = $mrole->field('id,name,pid')->select();
		$mlist = list_to_tree($mclassTree,'id','pid','_child',0);
		$mroleUser = M("MroleUser");
		$mroleidList = $mroleUser->where('user_id='.$id)->find();
		$mroleid = $mroleidList['role_id'];
		$vo['mroleid'] = $mroleid;
		$this->assign('list', $list);
		$this->assign('mlist', $mlist);
		$this->assign('info', $vo);
		$this->display();
	}
	public function insert(){
		 $Api = new UserApi();
	     $username=I('post.username');
		 $nickname=I('post.nickname');
		 $email=I('post.email');
		 $mroleId=I('post.mroleId',0);
		 $roleId=I('post.roleId',0);
		 
		 
		 
		 $password=I('post.password');
         $uid = $Api->register($username,$nickname, $password, $email,'',false);
	     if (0 < $uid) { //注册成功
	     	   $this->after_insert($uid,$roleId,$mroleId);
               $this->mtReturn(200,'添加成功');
            } else { //注册失败，显示错误信息
            	$this->mtReturn(300,$this->showRegError($uid));
               
            }
         
        
		
	}
    public function update(){
		//$Api = new UserApi();
        $model = D('Member');
        $uid=I('post.uid');
        $username=I('post.username');
        $email=I('post.email');
        $nickname=I('post.nickname');
         $mroleId=I('post.mroleId',0);
		 $roleId=I('post.roleId',0);
		 $score=I('post.score',0);
        $data=array(
			'nickname'=>$nickname,
			'username'=>$username,
			'email'=>$email,
			'uid'=>$uid,
        'score'=>$score,
            'id'=>$uid
			);
			
        $res=callApi('Public/updateUser',array($uid,$data));
         
        if (!$res['success']) {
           
            $this->mtReturn(300,$res['message']);
        }else{
        	$this->after_update($uid,$roleId,$mroleId);
        	clean_query_user_cache($uid,array('nickname','email','username'));
        	$this->mtReturn(200,'编辑成功');
        }
       /* 
        
		if (false === $data1= $model->create()) {

			$this->mtReturn(300, $model->getError());
		}
		if(false !== D('Member')->save($data1)){
        	
			$data=array(
			'id'=>$uid,
			'username'=>$username,
			'email'=>$email
			
			);
			
         $res = $Api->updateInfo($uid, 'admin', $data);
        if ($res['status']) {
        	    $this->_after_update($uid);
        	    clean_query_user_cache($uid,array('nickname','email','username'));
                $this->mtReturn(200,'编辑成功');
            } else {
                $this->mtReturn(300,$res['info']);
            }
         
		}else{
			$this->mtReturn(300,'编辑失败');
		}*/
         
		 
	}
 public function after_insert($result,$roleId,$mroleId){
		
		
		if($roleId!=0){
			$this->addRole($result,$roleId);
		}
		
		
		if($mroleId!=0){
		$this->addMrole($result,$mroleId);
		}
	}
	
	function after_update($userid,$roleId,$mroleId) {
		
		
		if($roleId!=0){
		$this->editRole($userid,$roleId);
		}
		
		if($mroleId!=0){
		$this->editMrole($userid,$mroleId);
		}
		
	}
 /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0)
    {
        switch ($code) {
            case -1:
                $error = '用户名长度必须在4-16个字符以内！';
                break;
                
            case -2:
                $error = '用户名被禁止注册！';
                break;
            case -3:
                $error = '用户名被占用！';
                break;
            case -4:
                $error = '密码长度必须在6-30个字符之间！';
                break;
            case -5:
                $error = '邮箱格式不正确！';
                break;
            case -6:
                $error = '邮箱长度必须在4-32个字符之间！';
                break;
            case -7:
                $error = '邮箱被禁止注册！';
                break;
            case -8:
                $error = '邮箱被占用！';
                break;
            case -9:
                $error = '手机格式不正确！';
                break;
            case -10:
                $error = '手机被禁止注册！';
                break;
            case -11:
                $error = '手机号被占用！';
                break;
            case -20:
                $error = '用户名只能由数字、字母和"_"组成！';
                break;
            case -30:
                $error = '昵称被占用！';
                break;
            case -31:
                $error = '昵称被禁止注册！';
                break;
            case -32:
                $error = '昵称只能由数字、字母、汉字和"_"组成！';
                break;
            case -33:
                $error = '昵称长度必须在2-16个字符以内！';
                break;
             case -34:
                $error = '签名长度必须在100个字符以内！';
                break;
            case -35:
                $error = '不要注册的太频繁哦！';
                break;
            default:
                $error = '未知错误24';
        }
        return $error;
    }
   

	function before_foreverdelete($ids){
		
	        if ($this->hasRole($ids)){
	        	
	        	
				$this->mtReturn(300, '请在管理组中先解除权限组与所删除用户的关联关系！');

			}
		
	}
    
	function before_selectedDelete($ids){
		
	        if ($this->hasRole($ids)){
				$this->mtReturn(300, '请在管理组中先解除权限组与所删除用户的关联关系！');

			}
	      
		
	}
	function after_foreverdelete($ids){
		
	$condition = array ('id' => array ('in', explode ( ',', $ids ) ) );
	
	
				if (false !== M('ucenter_member')->where ( $condition )->delete ()) {
					$condition1 = array ('user_id' => array ('in', explode ( ',', $ids ) ) );
					if (false === M('MroleUser')->where ( $condition1 )->delete ()) {
						$this->mtReturn(300, '删除失败！');
					}
					

				} else {
					$this->mtReturn(300, '删除失败！');

				}
	       
		
	}
    
	function after_selectedDelete($ids){
		
		$condition = array ('id' => array ('in', explode ( ',', $ids ) ) );
				if (false !== M('ucenter_member')->where ( $condition )->delete ()) {
				    $condition1 = array ('user_id' => array ('in', explode ( ',', $ids ) ) );
					if (false === M('MroleUser')->where ( $condition1 )->delete ()) {
						$this->mtReturn(300, '删除失败！');
					}
					

				} else {
					$this->mtReturn(300, '删除失败！');

				}   
	      
		
	}
	


}

?>