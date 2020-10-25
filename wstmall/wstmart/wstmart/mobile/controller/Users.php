<?php
namespace wstmart\mobile\controller;
use wstmart\mobile\model\Users as M;
use wstmart\mobile\model\Messages;
use wstmart\common\model\LogSms;
use wstmart\common\model\Users as MUsers;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 用户控制器
 */
class Users extends Base{
	// 前置方法执行列表
    protected $beforeActionList = [
          'checkAuth' =>  ['except'=>'index,checklogin,checkloginbyphone,login,register,registerbyaccount,getverify,toregister,forgetpass,forgetpasst,forgetpasss,findpass,getfindphone,resetpass,getphoneverifycode,getphoneverifycode2,checkuserphone']// 访问这些except下的方法不需要执行前置操作
    ];
    /**
     * 会员登录页
     */
    public function login(){
    	//如果已经登录了则直接跳去用户中心
    	$USER = session('WST_USER');
    	if(!empty($USER) && $USER['userId']!=''){
    		$this->redirect("users/index");
    	}
        $loginType = explode(',',WSTConf('CONF.mobileLoginType'));
        $type = ((int)input('logintype',1)==1)?1:2;
        $type = (in_array($type,$loginType))?$type:$loginType[0];
        $this->assign('type', $type);
    	return $this->fetch('login');
    }
    /**
     * 会员登录（账号登录）
     */
    public function checkLogin(){
        if(strpos(WSTConf('CONF.mobileLoginType'),'1')===false)return WSTReturn("非法登录！",-1);
    	$m = new M();
    	$rs =  $m->checkLogin(2);
    	$rs['url'] = session('WST_MO_WlADDRESS');
    	return $rs;
    }
    /**
     * 会员登录（手机登录）
     */
    public function checkLoginByPhone(){
        if(strpos(WSTConf('CONF.mobileLoginType'),'2')===false)return WSTReturn("非法登录！",-1);
        $m = new M();
        $rs =  $m->checkLoginByPhone(2);
        $rs['url'] = session('WST_MO_WlADDRESS');
        return $rs;
    }

    public function toRegister(){
        $registerType = explode(',',WSTConf('CONF.mobileRegisterType'));
        $type = ((int)input('registertype',1)==1)?1:2;
        $type = (in_array($type,$registerType))?$type:$registerType[0];
        $this->assign('type', $type);
        $rs = model('common/articles')->getById(300);
        $this->assign('data',$rs);
    	return $this->fetch('register');
    }
    /**
     * 会员注册（手机注册）
     */
    public function register(){
        if(strpos(WSTConf('CONF.mobileRegisterType'),'2')===false)return WSTReturn("非法注册！",-1);
    	$m = new M();
    	$rs =  $m->regist(2);
    	$rs['url'] = session('WST_MO_WlADDRESS');
    	return $rs;
    }
    /**
     * 会员注册（账号注册）
     */
    public function registerByAccount(){
        if(strpos(WSTConf('CONF.mobileRegisterType'),'1')===false)return WSTReturn("非法注册！",-1);
        $m = new M();
        $rs =  $m->registByAccount(2);
        $rs['url'] = session('WST_MO_WlADDRESS');
        return $rs;
    }
    /**
     * 手机号码是否存在
     */
    public function checkUserPhone(){
    	$userPhone = input("post.userPhone");
    	$m = new M();
    	$rs = $m->checkUserPhone($userPhone,(int)session('WST_USER.userId'));
    	if($rs["status"]!=1){
    		return WSTReturn("手机号已注册",-1);
    	}else{
    		return WSTReturn("",1);
    	}
    }
    /**
     * 获取验证码
     */
    public function getPhoneVerifyCode(){
    	$userPhone = input("post.userPhone");
    	$rs = array();
    	if(!WSTIsPhone($userPhone)){
    		return WSTReturn("手机号格式不正确!");
    	}
    	$m = new M();
    	$rs = $m->checkUserPhone($userPhone,(int)session('WST_USER.userId'));
    	if($rs["status"]!=1){
    		return WSTReturn("手机号已存在!");
    	}
    	$phoneVerify = rand(100000,999999);
    	$tpl = WSTMsgTemplates('PHONE_USER_REGISTER_VERFIY');
    	if( $tpl['tplContent']!='' && $tpl['status']=='1'){
    		$params = ['tpl'=>$tpl,'params'=>['MALL_NAME'=>WSTConf("CONF.mallName"),'VERFIY_CODE'=>$phoneVerify,'VERFIY_TIME'=>10]];
    		$m = new LogSms();
    		$rv = $m->sendSMS(0,$userPhone,$params,'getPhoneVerifyCode',$phoneVerify);
    	}
    	if($rv['status']==1){
			session('VerifyCode_userPhone',$userPhone);
			session('VerifyCode_userPhone_Verify',$phoneVerify);
			session('VerifyCode_userPhone_Time',time());
    	}
    	return $rv;
    }
    /**
     * 获取验证码(手机登录)
     */
    public function getPhoneVerifyCode2(){
        $userPhone = input("post.userPhone");
        if(!WSTIsPhone($userPhone)){
            return WSTReturn("手机号格式不正确!");
        }
        $m = new M();
        $rs = $m->checkUserPhone($userPhone);
        if($rs["status"]==1){
            return WSTReturn("手机号还没注册!");
        }
        $phoneVerify = rand(100000,999999);
        $rv = ['status'=>-1,'msg'=>'短信发送失败'];
        $tpl = WSTMsgTemplates('PHONE_COMMON_VERFIY');
        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
            $params = ['tpl'=>$tpl,'params'=>['VERFIY_CODE'=>$phoneVerify]];
            $m = new LogSms();
            $rv = $m->sendSMS(0,$userPhone,$params,'getPhoneVerify',$phoneVerify);
        }
        if($rv['status']==1){
            session('VerifyCode_userPhone2',$userPhone);
            session('VerifyCode_userPhone_Verify2',$phoneVerify);
            session('VerifyCode_userPhone_Time2',time());
        }
        return $rv;
    }
	/**
	 * 会员中心
	 */
	public function index(){
		$userId = (int)session('WST_USER.userId');
		if($userId>0){
            $m = new M();
            $user = $m->getById($userId);
            if($user['userName']=='')$user['userName']=$user['loginName'];
        }else{
		    $user['userName'] = '游客';
            $user['userPhone'] = '';
		    $user['userPhoto'] = '';
		    $user['userType'] = 0;
		    $user['userMoney'] = 0;
		    $user['userScore'] = 0;
        }
        $user['userId'] = $userId;
		$this->assign('user', $user);
		//商城未读消息的数量 及 各订单状态数量
		$data = model('index')->getSysMsg('msg','order','follow','history');
		$this->assign('data',$data);
		return $this->fetch('users/index');
	}

	/**
	 * 个人信息
	 */
	public function edit(){
		$userId = session('WST_USER.userId');
		$m = new M();
		$user = $m->getById($userId);
		$this->assign('user', $user);
		return $this->fetch('users/edit');
	}
	/**
	 * 编辑个人信息
	 */
	public function editUserInfo(){
    	$m = new M();
    	return $m->edit();
	}
	/**
	 * 账户安全
	 */
	public function security(){
		$m = new M();
		$userId = (int)session('WST_USER.userId');
		$user = $m->getById($userId);
		$payPwd = $user['payPwd'];
		$userPhone = $user['userPhone'];
		$loginPwd = $user['loginPwd'];
		$user['loginPwd'] = empty($loginPwd)?0:1;
		$user['payPwd'] = empty($payPwd)?0:1;
		$user['userPhone'] = empty($userPhone)?0:1;
		$this->assign('user', $user);
		session('Edit_userPhone_Time', null);
		return $this->fetch('users/security/index');
	}
	/**
	 * 修改登录密码
	 */
	public function editLoginPass(){
		$m = new M();
		$userId = (int)session('WST_USER.userId');
		$user = $m->getById($userId);
		$loginPwd = $user['loginPwd'];
		$user['loginPwd'] = empty($loginPwd)?0:1;
		$this->assign('user', $user);
		return $this->fetch('users/security/user_login_pass');
	}
	public function editloginPwd(){
		$m = new M();
		$userId = (int)session('WST_USER.userId');
		return $m->editPass($userId);
	}
	/**
	 * 修改支付密码
	 */
	public function editPayPass(){
		$m = new M();
		$userId = (int)session('WST_USER.userId');
		$user = $m->getById($userId);
		$payPwd = $user['payPwd'];
		$user['payPwd'] = empty($payPwd)?0:1;
		$this->assign('user', $user);
		return $this->fetch('users/security/user_pay_pass');
	}
	public function editpayPwd(){
		$m = new M();
		$userId = (int)session('WST_USER.userId');
		return $m->editPayPass($userId);
	}
	/**
	 * 忘记支付密码
	 */
	public function backPayPass(){
		$m = new M();
		$userId = (int)session('WST_USER.userId');
		$user = $m->getById($userId);
		$userPhone = $user['userPhone'];
		$user['userPhone'] = WSTStrReplace($user['userPhone'],'*',3);
		$user['phoneType'] = empty($userPhone)?0:1;
		$backType = (int)session('Type_backPaypwd');
		$timeVerify = session('Verify_backPaypwd_Time');
		$user['backType'] = ($backType==1 && time()<floatval($timeVerify)+10*60)?1:0;
		$this->assign('user', $user);
		return $this->fetch('users/security/user_back_paypwd');
	}
	/**
	 * 忘记支付密码：发送短信
	 */
	public function backpayCode(){
		$m = new MUsers();
		$data = $m->getById(session('WST_USER.userId'));
		$userPhone = $data['userPhone'];
		$phoneVerify = rand(100000,999999);
		$rv = ['status'=>-1,'msg'=>'短信发送失败'];
		$tpl = WSTMsgTemplates('PHONE_FOTGET_PAY');
		if( $tpl['tplContent']!='' && $tpl['status']=='1'){
			$params = ['tpl'=>$tpl,'params'=>['LOGIN_NAME'=>$data['loginName'],'VERFIY_CODE'=>$phoneVerify,'VERFIY_TIME'=>10]];
			$m = new LogSms();
			$rv = $m->sendSMS(0,$userPhone,$params,'getPhoneVerifyt',$phoneVerify);
		}
		if($rv['status']==1){
			$USER = [];
			$USER['userPhone'] = $userPhone;
			$USER['phoneVerify'] = $phoneVerify;
			session('Verify_backPaypwd_info',$USER);
			session('Verify_backPaypwd_Time',time());
			return WSTReturn('短信发送成功!',1);
		}
		return $rv;
	}
	/**
	 * 忘记支付密码：验证短信
	 */
	public function verifybackPay(){
		$phoneVerify = input("post.phoneCode");
		$timeVerify = session('Verify_backPaypwd_Time');
		if(!session('Verify_backPaypwd_info.phoneVerify') || time()>floatval($timeVerify)+10*60){
			return WSTReturn("校验码已失效，请重新发送！");
			exit();
		}
		if($phoneVerify==session('Verify_backPaypwd_info.phoneVerify')){
			session('Type_backPaypwd',1);
			return WSTReturn("验证成功",1);
		}
		return WSTReturn("校验码不一致，请重新输入！");
	}
	/**
	 * 忘记支付密码：重置密码
	 */
	public function resetbackPay(){
		$m = new M();
		return $m->resetbackPay();
	}
	/**
	 * 修改手机
	 */
	public function editPhone(){
		$m = new M();
		$userId = (int)session('WST_USER.userId');
		$user = $m->getById($userId);
		$userPhone = $user['userPhone'];
		$user['userPhone'] = WSTStrReplace($user['userPhone'],'*',3);
		$user['phoneType'] = empty($userPhone)?0:1;
		$this->assign('user', $user);
		session('Edit_userPhone_Time', null);
		return $this->fetch('users/security/user_phone');
	}
	/**
	 * 绑定手机：发送短信验证码
	 */
	public function sendCodeTie(){
		$userPhone = input("post.userPhone");
        if(!WSTIsPhone($userPhone)){
            return WSTReturn("手机号格式不正确!");
            exit();
        }
        $rs = array();
        $m = new MUsers();
        $rs = WSTCheckLoginKey($userPhone,(int)session('WST_USER.userId'));
        if($rs["status"]!=1){
            return WSTReturn("手机号已存在!");
            exit();
        }
        $data = $m->getById(session('WST_USER.userId'));
        $phoneVerify = rand(100000,999999);
        $rv = ['status'=>-1,'msg'=>'短信发送失败'];
        $tpl = WSTMsgTemplates('PHONE_BIND');
        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
            $params = ['tpl'=>$tpl,'params'=>['LOGIN_NAME'=>$data['loginName'],'VERFIY_CODE'=>$phoneVerify,'VERFIY_TIME'=>10]];
            $m = new LogSms();
            $rv = $m->sendSMS(0,$userPhone,$params,'sendCodeTie',$phoneVerify);
        }
        if($rv['status']==1){
            $USER = [];
            $USER['userPhone'] = $userPhone;
            $USER['phoneVerify'] = $phoneVerify;
            session('Verify_info',$USER);
            session('Verify_userPhone_Time',time());
            return WSTReturn('短信发送成功!',1);
        }
        return $rv;
	}
	/**
	 * 绑定手机
	 */
	public function phoneEdit(){
		$phoneVerify = input("post.phoneCode");
        $timeVerify = session('Verify_userPhone_Time');
        if(!session('Verify_info.phoneVerify') || time()>floatval($timeVerify)+10*60){
            return WSTReturn("校验码已失效，请重新发送！");
            exit();
        }
        if($phoneVerify==session('Verify_info.phoneVerify')){
            $m = new M();
            $rs = $m->editPhone((int)session('WST_USER.userId'),session('Verify_info.userPhone'));
            return $rs;
        }
        return WSTReturn("校验码不一致，请重新输入！");
	}
	/**
	 * 修改手机：发送短信验证码
	 */
	public function sendCodeEdit(){
    	$m = new MUsers();
        $data = $m->getById(session('WST_USER.userId'));
        $userPhone = $data['userPhone'];
        $phoneVerify = rand(100000,999999);
        $rv = ['status'=>-1,'msg'=>'短信发送失败'];
        $tpl = WSTMsgTemplates('PHONE_EDIT');
        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
            $params = ['tpl'=>$tpl,'params'=>['LOGIN_NAME'=>$data['loginName'],'VERFIY_CODE'=>$phoneVerify,'VERFIY_TIME'=>10]];
            $m = new LogSms();
            $rv = $m->sendSMS(0,$userPhone,$params,'getPhoneVerifyt',$phoneVerify);
        }
        if($rv['status']==1){
            $USER = [];
            $USER['userPhone'] = $userPhone;
            $USER['phoneVerify'] = $phoneVerify;
            session('Verify_info2',$USER);
            session('Verify_userPhone_Time2',time());
            return WSTReturn('短信发送成功!',1);
        }
        return $rv;
	}
	/**
	 * 修改手机
	 */
	public function phoneEdito(){
		$phoneVerify = input("post.phoneCode");
        $timeVerify = session('Verify_userPhone_Time2');
        if(!session('Verify_info2.phoneVerify') || time()>floatval($timeVerify)+10*60){
            return WSTReturn("校验码已失效，请重新发送！");
            exit();
        }
        if($phoneVerify==session('Verify_info2.phoneVerify')){
            session('Edit_userPhone_Time',time());
            return WSTReturn("验证成功",1);
            return $rs;
        }
        return WSTReturn("校验码不一致，请重新输入！",-1);
	}
	public function editPhoneo(){
        $m = new M();
        $userId = (int)session('WST_USER.userId');
        $user = $m->getById($userId);
        $userPhone = $user['userPhone'];
        $user['userPhone'] = WSTStrReplace($user['userPhone'],'*',3);
        $timeVerify = session('Edit_userPhone_Time');
        if(time()>floatval($timeVerify)+15*60){
            $user['phoneType'] = 1;
        }else{
            $user['phoneType'] = 0;
        }
        $this->assign('user', $user);
        return $this->fetch('users/security/user_phone');
    }
	/**
	 * 用户退出
	 */
	public function logout(){
		session('WST_USER',null);
		setcookie("loginPwd", null);
		session('WST_MO_WlADDRESS',null);
		return WSTReturn("",1);
	}

	/************************************************* 忘记密码 ********************************************************/
    // 页面过期/失效
    protected function expire($msg=''){
        $this->assign('button',['text'=>'重新操作','url'=>url('mobile/users/forgetpass')]);
        $this->assign('message','对不起，页面已失效');
        return $this->fetch('error_sys');
    }
	 /**
     * 忘记密码
     */
    public function forgetPass(){
        $this->assign('loginName',input('loginName',''));
    	return $this->fetch('forget_pass');
    }
    public function forgetPasst(){
    	if(time()<floatval(session('findPass.findTime'))+30*60){
	    	$userId = session('findPass.userId');
	    	$m = new M();
	    	$info = $m->getById($userId);
	    	if($info['userPhone']!='')$info['userPhone'] = WSTStrReplace($info['userPhone'],'*',3);
	    	if($info['userEmail']!='')$info['userEmail'] = WSTStrReplace($info['userEmail'],'*',2,'@');
	    	$this->assign('forgetInfo',$info);
	    	return $this->fetch('forget_pass2');
    	}else{
    		return $this->expire();
    	}
    }

    /**
    * 重置密码
    */
    public function resetPass(){
         if(!session('findPass')){
            return $this->expire();
         }
         return $this->fetch('forget_pass3');
    }
    public function forgetPasss(){
        if(!session('findPass')){
            return $this->expire();
         }
    	$USER = session('findPass');
    	if(empty($USER) && $USER['userId']!=''){
    		$this->expire('请在同一浏览器操作！');
    	}
        $uId = session('findPass.userId');
        $key = session("findPass.key");
        // 验证邮箱中的验证码
        $secretCode = input('secretCode');
        if($key==$secretCode){
            session('REST_userId',$uId);
            session('REST_success','1');
            return WSTReturn('验证成功',1);
        }else{
            return WSTReturn('校验码错误',-1);
        }
    	
    }
    /**
     * 找回密码
     */
    public function findPass(){
    	//禁止缓存
    	header('Cache-Control:no-cache,must-revalidate');
    	header('Pragma:no-cache');
    	$code = input("post.verifyCode");
    	$step = input("post.step/d");
    	switch ($step) {
    		case 1:#第一步，验证身份
    			if(!WSTVerifyCheck($code)){
    				return WSTReturn('验证码错误!',-1);
    			}
    			$loginName = input("post.loginName");
    			$rs = WSTCheckLoginKey($loginName);
    			if($rs["status"]==1){
    				return WSTReturn("用户名不存在!");
    				exit();
    			}
    			$m = new M();
    			$info = $m->checkAndGetLoginInfo($loginName);
    			if ($info != false) {
    				session('findPass',array('userId'=>$info['userId'],'loginName'=>$loginName,'userPhone'=>$info['userPhone'],'userEmail'=>$info['userEmail'],'loginSecret'=>$info['loginSecret'],'findTime'=>time()));
    				return WSTReturn("操作成功",1);
    			}else return WSTReturn("用户名不存在!");
    			break;
    		case 2:#第二步,验证方式
    			if (session('findPass.loginName') != null ){
    				if(input("post.modes")==1){
    					if ( session('findPass.userPhone') == null) {
    						return WSTReturn('你没有预留手机号码，请通过邮箱方式找回密码！',-1);
    					}
    					$phoneVerify = input("post.Checkcode");
    					if(!$phoneVerify){
    						return WSTReturn('校验码不能为空!',-1);
    					}
    					return $this->checkfindPhone($phoneVerify);
    				}else{
    					if (session('findPass.userEmail')==null) {
    						return WSTReturn('你没有预留邮箱，请通过手机号码找回密码！',-1);
    					}
    					if(!WSTVerifyCheck($code)){
    						return WSTReturn('验证码错误!',-1);
    					}
    					return $this->getfindEmail();
    				}
    			}else return $this->expire();
    			break;
    		case 3:#第三步,设置新密码
    			$resetPass = session('REST_success');
    			if($resetPass != 1)return $this->expire();
    			$loginPwd = input("post.loginPwd");
    			$repassword = input("post.repassword");
    			$decrypt_data = WSTRSA($loginPwd);
    			$decrypt_data2 = WSTRSA($repassword);
    			if($decrypt_data['status']==1 && $decrypt_data2['status']==1){
    				$loginPwd = $decrypt_data['data'];
    				$repassword = $decrypt_data2['data'];
    			}else{
    				return WSTReturn('设置失败');
    			}
    			if ($loginPwd == $repassword) {
    				$m = new M();
    				$rs = $m->resetPass();
    				if($rs['status']==1){
    					return $rs;
    				}else{
    					return $rs;
    				}
    			}else return WSTReturn('两次密码不同！',-1);
    			break;
    		default:
    			return $this->expire();
    			break;
    	}
    }
    /**
     * 手机验证码获取
     */
    public function getfindPhone(){
        session('WST_USER',session('findPass.userId'));
        if(session('findPass.userPhone')==''){
            return WSTReturn('你没有预留手机号码，请通过邮箱方式找回密码！',-1);
        }
        $phoneVerify = rand(100000,999999);
        session('WST_USER',null);
        $rv = ['status'=>-1,'msg'=>'短信发送失败'];
        $tpl = WSTMsgTemplates('PHONE_FOTGET');
        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
            $params = ['tpl'=>$tpl,'params'=>['VERFIY_CODE'=>$phoneVerify,'VERFIY_TIME'=>10]];
            $m = new LogSms();
            $rv = $m->sendSMS(0,session('findPass.userPhone'),$params,'getPhoneVerify',$phoneVerify);
        }
        if($rv['status']==1){
            // 记录发送短信的时间,用于验证是否过期
            session('REST_Time',time());
            $USER = [];
            $USER['phoneVerify'] = $phoneVerify;
            $USER['time'] = time();
            session('findPhone',$USER);
            return WSTReturn('短信发送成功!',1);
        }
        return $rv;
    }
    /**
     * 手机验证码检测
     * -1 错误，1正确
     */
    public function checkfindPhone($phoneVerify){
    	if(!session('findPhone.phoneVerify') || time()>floatval(session('findPhone.time'))+10*60){
    		return WSTReturn("校验码已失效，请重新发送！");
    		exit();
    	}
    	if (session('findPhone.phoneVerify') == $phoneVerify ) {
    		$fuserId = session('findPass.userId');
    		if(!empty($fuserId)){
                session('REST_userId',$fuserId);
                session('REST_success','1');
    			$rs['status'] = 1;
    			$rs['url'] = url('mobile/users/resetPass');
    			return $rs;
    		}
    		return WSTReturn('无效用户',-1);
    	}
    	return WSTReturn('校验码错误!',-1);
    }
    /**
     * 发送验证邮件/找回密码
     */
    public function getfindEmail(){
        $code = rand(0,999999);
        $sendRs = ['status'=>-1,'msg'=>'邮件发送失败'];
        $tpl = WSTMsgTemplates('EMAIL_FOTGET');
        if( $tpl['tplContent']!='' && $tpl['status']=='1'){
            $find = ['${LOGIN_NAME}','${SEND_TIME}','${VERFIY_CODE}','${VERFIY_TIME}'];
            $replace = [session('findPass.loginName'),date('Y-m-d H:i:s'),$code,30];
            $sendRs = WSTSendMail(session('findPass.userEmail'),'密码重置',str_replace($find,$replace,$tpl['content']));
        }
        if($sendRs['status']==1){
            $uId = session('findPass.userId');
            session("findPass.key", $code);
            // 发起重置密码的时间;
            session('REST_Time',time());
            return WSTReturn("发送成功",1);
        }else{
            return WSTReturn($sendRs['msg'],-1);
        }
    }
    public function userSet(){
        return $this->fetch('users/userset/list');
    }
    public function aboutUs(){
        return $this->fetch('users/userset/about');
    }
}
