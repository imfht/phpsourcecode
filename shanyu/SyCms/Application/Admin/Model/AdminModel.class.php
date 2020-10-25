<?php
namespace Admin\Model;
use Think\Model;

class AdminModel extends Model{
	//自动验证(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
	//验证条件(0:存在字段验证|默认,1:必须验证,2:值不为空验证)
	//验证时间(1:新增验证,2:修改验证,3:全部验证|默认)
    protected $_validate = array(
        //添加
        array('username', '6,20', '账号长度不符,请保持在6-20个字符之内',1, 'length',3),
        array('username','','该账号已被使用',1,'unique',1),
        array('password', '6,30', '密码长度不符,请保持在6-30个字符之内',1,'length',1),

        //修改
        array('username', '6,20', '账号长度不符,请保持在6-20个字符之内',1, 'length',2),
        array('password', '6,30', '密码长度不符,请保持在6-30个字符之内',2,'length',2),

        //通用
        array('realname','/^[\x{4e00}-\x{9fa5}]+$/u','请填写中文的姓名',1,'regex',3),
        array('mobile','/^\d{11}$/','请正确填写手机号码',1,'regex',3),
        array('email','email','请正确填写邮箱地址',1,'regex',3),
    );

	//内容过滤/填充(完成字段1,完成规则,[完成条件,附加规则,函数参数])
	//完成条件(1:新增时候处理,2:修改时候处理,3:全部时候处理|默认)
	protected $_auto = array (
		array('password','getPassword',3,'callback'),
        array('encrypt','getEncrypt',3,'callback'),
	);

	public function login($username,$password,$remember=0){
        //登陆错误_检测
        if(!$this->loginError('check')) $this->error('您登陆错误次数超过5次,请10分钟后再尝试登陆.');

        //管理员登陆操作
        $admin=M('Admin')->where(array('username|mobile'=>$username))->find();
        if(!$admin){
            action_log('login',1);
            $this->loginError('add');
            $this->error='账号不存在';
            return false;
        }
        
        //验证管理员状态
        if(!$admin['status']){
            action_log('login',2);
            $this->loginError('add');
            $this->error='账号已被禁用';
            return false;
        };

        //验证密码
        $pass=$this->getPassword($password,$admin['encrypt']);
        if($pass != $admin['password']){
            action_log('login',3);
            $this->loginError('add');
            $this->error='密码错误';
            return false;
        }

        //添加认证记录
        $this->adminInfo($admin);

        //添加持久登录
        if($remember) $this->adminToken('add',$admin);

        //成功登陆
        action_log('login',0,$admin['id']);
        $this->loginError('del');
        return $admin;
	}

    //添加session认证
    public function adminInfo($admin=array()){

        //单点登录删除其他地点session认证
        if($admin['session_id'] != session_id() ){
            $this->where("id={$admin['id']}")->setField('session_id',session_id());
            session_id($admin['session_id']);
            session_destroy();
            session_start();
        }

        $admin_info=array(
            'id'=>$admin['id'],
            'realname'=>$admin['realname'],
            'mobile'=>$admin['mobile'],
            'email'=>$admin['email'],
        );
        session('admin_info',$admin_info);
        
    }

    //添加cookie持久认证
    public function adminToken($type='check',$admin=array()){
        switch ($type) {
            case 'add':
                if(empty($admin)) return false;
                //生成cookie持久认证信息
                $token=array();
                $token['token_time']=time() + 3600 * 24 * 7;
                $token['token']=md5($admin['username'].$token['token_time']);
                if( $this->where("id={$admin['id']}")->save($token) ){
                    cookie('admin_token', $token['token'], 3600 * 24 * 7);
                }else{
                    return false;
                }
            break;
            case 'check':
                $token=cookie('admin_token');
                if(!$token){ return false; }

                //强制清除无效/过期cookie持久认证
                $admin=$this->where("token='{$token}'")->find();
                if( !$admin || $admin['token_time'] < time() ){ cookie('admin_token',null);return false; }

                //重新添加session认证
                $this->adminInfo($admin);
                return $admin['id'];
            break;
        }
        return true;
    }

    //登录错误操作
    private function loginError($type='check'){
        $login_error=session('login_error');
        switch ($type) {
            case 'check':
                if(isset($login_error['time']) && $login_error['time'] > time() ) return false;
                elseif($login_error['time'] < time()) session('login_error',null);//登录次数过期后删除
                break;

            case 'add':
                $error['num'] = isset($login_error['num']) ? $login_error['num']+1 : 1;
                if($error['num'] > 5) $error['time']=time() + 60*10;
                session('login_error',$error);
                break;

            case 'del':
                session('login_error',null);
                break;
        }
        return true;

    }

    //自动完成所需回调方法
	public function getPassword($str='',$encrypt=''){
        if(empty($str)) return false;
		if(empty($encrypt)){
            $this->encrypt=$encrypt=\Org\Util\String::randString(6);
        }
		return md5(sha1($str) . $encrypt);
	}
    public function getEncrypt(){
        if(empty($this->encrypt)) return false;
        return $this->encrypt;
    }

    public function getIpInfo(){
        $ip = get_client_ip();
        $IpLocation = new \Org\Net\IpLocation(); 
        $info = $IpLocation->getlocation($ip);
        return $info['country'];
    }

}