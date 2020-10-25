<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\logic;

/**
 * 会员逻辑
 */
class User extends LogicBase
{
    
    // 会员模型
    public static $memberModel = null;
    
    /**
     * 构造方法
     */
    public function __construct()
    {
        
        parent::__construct();
        
        self::$memberModel = model($this->name);
    }
    
    /**
     * 获取会员信息
     */
    public function getMemberInfo($where = [], $field = true)
    {
    	//return self::$memberModel->getInfo($where,'m.*,doccon.id as did', [['doccon','m.id=doccon.uid and doccon.status>0','LEFT']]);
    	 
        return self::$memberModel->getInfo($where, $field);
    }
    
    /**
     * 获取会员列表
     */
    public function getMemberList($where = [], $field = true, $order = '')
    {
    	
    	//return self::$memberModel->getList($where, 'm.*,count(doccon.id) as doccount', $order,0,[['doccon','m.id=doccon.uid','LEFT']],'');
    	
        return self::$memberModel->getList($where, $field, $order);
    }
    
    /**
     * 获取会员列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
             if(!empty($data['status'])&&$data['status']!='all'){
        	
        	$where['status'] = $data['status'];
        }
        
        
        !empty($data['search_data']) && $where['nickname|username|usermail|mobile'] = ['like', '%'.$data['search_data'].'%'];
        
        
        if (!is_administrator()) {
            
/*             $member = session('member_info');
            
            if ($member['is_share_member']) {
                
                $ids = $this->getInheritMemberIds(MEMBER_ID);
                
                $ids[] = MEMBER_ID;
                
                $where['leader_id'] = ['in', $ids];
                
            } else {
                
                $where['leader_id'] = MEMBER_ID;
            } */
        }
      
        return $where;
    }
    
    /**
     * 获取存在继承关系的会员ids
     */
    public function getInheritMemberIds($id = 0, $data = [])
    {
        
        $member_id = self::$memberModel->getValue(['id' => $id, 'is_share_member' => DATA_NORMAL], 'leader_id');
        
        if (empty($member_id)) {
            
            return $data;
        } else {
            
            $data[] = $member_id;
            
            return $this->getInheritMemberIds($member_id, $data);
        }
    }
    
    /**
     * 会员添加到用户组
     */
    public function addToGroup($data = [])
    {
        
        if (SYS_ADMINISTRATOR_ID == $data['id']) : return [RESULT_ERROR, '管理员不能授权哦~']; endif;
        
        $model = model('AuthGroupAccess');
        
        $where = ['member_id' => ['in', $data['id']]];
        
        $model->deleteInfo($where, true);
        
        $url = url('memberList');
        
        if (empty($data['group_id'])) : return [RESULT_SUCCESS, '会员授权成功', $url]; endif;
        
        $add_data = [];
        
        foreach ($data['group_id'] as $group_id) {
            
            $add_data[] = ['member_id' => $data['id'], 'group_id' => $group_id,'create_time'=>TIME_NOW,'update_time'=>TIME_NOW];
        }
        
        \think\Cache::clear('authgroupaccessauthgroup');
        
        return $model->setList($add_data) ? [RESULT_SUCCESS, '会员授权成功', $url] : [RESULT_ERROR, $model->getError()];
    }
    
    /**
     * 会员添加
     */
    public function memberAdd($data = [])
    {
       
       
        $validate = validate($this->name);
        
        $validate_result = $validate->scene('add')->check($data);
        
        if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
        
        $url = url('memberList');
        
        $data['nickname']  = $data['username'];
        $data['leader_id'] = MEMBER_ID;
        $data['is_inside'] = DATA_NORMAL;
        //$data['regtime'] = TIME_NOW;
        $data['userip'] = CLIENT_IP;
       
        $salt = generate_password(18);
        $data['salt'] = $salt;
        $data['password'] = md5($data['password'].$salt);
       
        return self::$memberModel->setInfo($data) ? [RESULT_SUCCESS, '会员添加成功', $url] : [RESULT_ERROR, self::$memberModel->getError()];
    }
    /**
     * 会员添加
     */
    public function memberEdit($data = [],$info)
    {
    	 
    	 
    	$validate = validate($this->name);
    
    	$validate_result = $validate->scene('edit')->check($data);
    
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    
    	$url = url('memberList');
    	
    
    	if(!empty($data['password'])){
    		$password=$data['password'];
    		$repassword=$data['password_confirm'];
    		if($password!=$repassword){
    			return  [RESULT_ERROR, '两次密码输入不一致'];
    		}
    		$md5pass=md5($password.$info['salt']);
    		if($md5pass==$info['password']){
    			return  [RESULT_ERROR, '密码未更改'];
    		}else{
    			$data['password']=$md5pass;
    		}
    		
    	}else{
    		unset($data['password']);
    	}
    	unset($data['password_confirm']);
    	
    	$data['nickname']  = $data['username'];
    	$data['leader_id'] = MEMBER_ID;
    	$data['is_inside'] = DATA_NORMAL;
    
    	return self::$memberModel->setInfo($data) ? [RESULT_SUCCESS, '会员编辑成功', $url] : [RESULT_ERROR, '1'];
    }
    /**
     * 会员认证
     */
    public function memberRz($data = [],$info)
    {

    	if(!empty($data['statusdes'])){
    		$data['status'] = 3;
    	}else{
    		$data['status'] = 1;
    	}
    	
    
    	return self::$memberModel->setInfo($data) ? [RESULT_SUCCESS, '会员认证成功'] : [RESULT_ERROR, '1'];
    }
    /**
     * 设置会员信息
     */
    public function setMemberValue($where = [], $field = '', $value = '')
    {
        
        return self::$memberModel->setFieldValue($where, $field, $value);
    }
    /**
     * 修改会员密码
     */
    public function setMemberPassword($data = [],$info)
    {
    	
    		$oldpass=$data['old_password'];
    		$password=$data['password'];
    		$confirm_password=$data['confirm_password'];
    	
    		if($info['password']!=md5($oldpass.$info['salt'])){
    			return  [RESULT_ERROR, '原密码不正确'];
    		}
    		if($password==''){
    			return  [RESULT_ERROR, '新密码为空'];
    		}
    		if($password!=$confirm_password){
    			return  [RESULT_ERROR, '两次新密码输入不一致'];
    		}
    		if($info['password']==md5($password.$info['salt'])){
    			return  [RESULT_ERROR, '未更改密码'];
    		}
    		
    
    	return self::$memberModel->setFieldValue(['id'=>$info['id']], 'password', md5($password.$info['salt'])) ? [RESULT_SUCCESS, '密码修改成功'] : [RESULT_ERROR, '密码修改失败'];
    }
    /**
     * 会员批量删除
     */
    public function memberAlldel($ids)
    {
    	
    if(in_array(SYS_ADMINISTRATOR_ID, $ids)||in_array(MEMBER_ID, $ids)){
    	return [RESULT_ERROR, '不能删除自己和管理员~'];
    }
    return self::$memberModel->deleteAllInfo(['id'=>array('in',$ids)]) ? [RESULT_SUCCESS, '会员删除成功'] : [RESULT_ERROR, self::$memberModel->getError()];
    }  
    /**
     * 会员删除
     */
    public function memberDel($where = [])
    {
        
        if (SYS_ADMINISTRATOR_ID == $where['id'] || MEMBER_ID == $where['id']) : return [RESULT_ERROR, '天神和自己不能删除哦~']; endif;
        
        return self::$memberModel->deleteInfo($where) ? [RESULT_SUCCESS, '会员删除成功'] : [RESULT_ERROR, self::$memberModel->getError()];
    }
   
    /**
     * 前台登录处理
     */
    public function loginHandle($username = '', $password = '', $verify = '')
    {
    	 
    	if (empty($username) || empty($password)) : return [RESULT_ERROR, '账号或密码不能为空']; endif;
    	$yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录
    	if(in_array(2, $yzm_list)){
    		 
    		if (empty($verify)) : return [RESULT_ERROR, '验证码不能为空']; endif;
    		if (!captcha_check($verify)) : return [RESULT_ERROR, '验证码输入错误']; endif;
    		 
    	}
    	 
    	 
    
    
    	
    
    	$member = $this->getMemberInfo(['username' => $username]);
    
    	if (empty($member)) : return [RESULT_ERROR, '用户不存在']; endif;
    
    
    	 
    	// 验证用户密码
    	if (md5($password.$member['salt']) === $member['password']) {
    		
    		$data['last_login_ip'] = CLIENT_IP;
    		
    		$data['last_login_time'] = TIME_NOW;
    		
    		$data['id'] = $member['id'];
    		
    		self::$memberModel->setInfo($data);
    		
    		//$this->setMemberValue(['id' => $member['id']], 'last_login_time', TIME_NOW);
    		 
    		$auth = ['member_id' => $member['id'], 'last_login_time' => TIME_NOW];
    		 
    		point_controll($member['id'],'login',0);//登录增加经验值
    		
    		//$auth = ['member_id' => $member['id'], 'last_login_time' => $member['last_login_time']];
    		session('member_info', $member);
    		session('member_auth', $auth);
    		session('member_auth_sign', data_auth_sign($auth));
    
    		return [RESULT_SUCCESS, '登录成功', url('Index/index')];
    
    	} else {
    
    		return [RESULT_ERROR, '密码输入错误'];
    	}
    }
    /**
     * 注销当前用户
     */
    public function logout()
    {
    	session('member_info', null);
    	session('member_auth', null);
    	session('member_auth_sign', null);
    	session('[destroy]');
    	cookie('sys_key',null);
    	return [RESULT_SUCCESS, '注销成功', url('user/login')];
    }
    
    
    /**
     * 前台注册处理
     */
    public function regHandle($username = '', $password = '',$repassword = '',$usermail = '', $verify = '')
    {
    	 
    	if (empty($username) || empty($password) || empty($usermail)) : return [RESULT_ERROR, '注册信息不能为空']; endif;
    	$yzm_list = parse_config_array('yzm_list');//1\注册2\登录3\忘记密码4\后台登录
    	if(in_array(1, $yzm_list)){
    		 
    		if (empty($verify)) : return [RESULT_ERROR, '验证码不能为空']; endif;
    		if (!captcha_check($verify)) : return [RESULT_ERROR, '验证码输入错误']; endif;
    		 
    	}
    	if ($password!=$repassword) : return [RESULT_ERROR, '两次密码输入不一致']; endif;
    	$validate = validate($this->name);
    	$data['username']  = $username;
    	$data['usermail']  = $usermail;
    	$data['password']  = $password;

    	$validate_result = $validate->scene('add')->check($data);
    	
    	if (!$validate_result) : return [RESULT_ERROR, $validate->getError()]; endif;
    	
    	 
    	// 用户密码
    	$data['nickname']  = $username;
    	
    	$data['leader_id'] = 0;
    	$data['is_inside'] = 0;
    	//$data['regtime'] = TIME_NOW;
    	$data['userip'] = CLIENT_IP;
    	 
    	$salt = generate_password(18);
    	$data['salt'] = $salt;
    	$data['password'] = md5($password.$salt);
    	 
    	return self::$memberModel->setInfo($data) ? [RESULT_SUCCESS, '注册成功'] : [RESULT_ERROR, self::$memberModel->getError()];
    	
    	
    }
    
}
