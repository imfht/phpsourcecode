<?php
/**
* 用户中心接口
*/
namespace Restful\Controller;

use Common\Model\FollowModel;
use Think\Controller\RestController;
use User\Api\UserApi;
require_once APP_PATH . 'User/Conf/config.php';

class UserController extends BaseController
{
	protected $allowMethod    = array('get','post','put'); // REST允许的请求类型列表
    protected $allowType      = array('html','xml','json'); // REST允许请求的资源类型列表
	protected $userModel;
	protected $codeModel;

	public function _initialize()
    {   
    	parent::_initialize();
        $this->userModel= D('Restful/User');
        $this->codeModel= D('Restful/Code');  //返回码及信息
    }

	/**
	 * 获取用户基本信息
	 * @return [type] [description]
	 */
    public function index(){
		switch ($this->_method){

		case 'get': //get请求处理代码

			$aUid = I('get.uid',0,'intval');
			if($aUid){
				$map['uid'] = $aUid;
				$userData=M('member')->where($map)->find();
				if($userData){
					$data = query_user(array('uid','nickname','sex','birthday','reg_ip','last_login_ip','last_login_time','avatar32','avatar128','mobile','email','username','title','signature','score','score1','score2','score3','score4'), $aUid);
					$data['avatar32'] = get_http_https() . $_SERVER['SERVER_NAME'] . $data['avatar32'];
					$data['avatar64'] = get_http_https() . $_SERVER['SERVER_NAME'] . $data['avatar64'];
					$data['avatar128'] = get_http_https() . $_SERVER['SERVER_NAME'] . $data['avatar128'];
					$data['avatar256'] = get_http_https() . $_SERVER['SERVER_NAME'] . $data['avatar256'];
					$data['avatar512'] = get_http_https() . $_SERVER['SERVER_NAME'] . $data['avatar512'];
					
					$result = $this->codeModel->code(200);
					$result['data'] = $data;
				}else{
					$result = $this->codeModel->code(1004); //不存在的用户
				}
			}else{
				$result = $this->codeModel->code(1004); 
			}
			
			$this->response($result,$this->type);
		
		break;
		case 'post'://post请求处理代码
			//post用来修改用户基本信息
			$uid = $this->_needLogin(); //必须登录后操作

			//$uid = is_login();
			$mobile = I('mobile',0,'intval');
			$email = I('email','','text');
			$verify = I('verify',0,'intval');
			$nickname = I('nickname','','text');
			$sex = I('sex',2,'intval');
			$signature = I('signature','','text');
			if($uid){
				$udata['id'] = $uid;
				//验证更新手机
				if($mobile) {
					//判断手机格式
					if(!preg_match("/^1[34578]\d{9}$/", $mobile)){
					    //手机号码格式错误
					    $result = $this->codeModel->code(3000);
						$this->response($result,$this->type);
					}
					//验证验证码
					$ret = D('Verify')->checkVerify($mobile,'mobile',$verify);
		            if(!$ret){//验证码错误
		            	$result = $this->codeModel->code(3003);
						$this->response($result,$this->type);	
		            }
		            //验证超时
					$time = time();
					$send_time = S('Verify_'.$mobile);
					$resend_time =  modC('SMS_RESEND','60','USERCONFIG');
		            if($time > $send_time+$resend_time ){//验证码超时
		                $result = $this->codeModel->code(3001);
						$this->response($result,$this->type);
		            }
		            $udata['mobile'] = $mobile;
				}
				//验证更新邮箱
				if($email){
					$ret = D('Verify')->checkVerify($email,'email',$verify);
					if(!$ret){
						$result = $this->codeModel->code(1005);
						$result['info'] = '邮箱和验证不匹配';
						$this->response($result,$this->type);
					}
					$udata['email'] = $email;
				}
				
				$mdata['uid'] = $uid;
				if($nickname){
					$mdata['nickname'] = $nickname;
				}
				if($sex===1 || $sex===2 || $sex===0){
					$mdata['sex'] = $sex;
				}
				if($signature){
					$mdata['signature'] = $signature;
				}

				$User = M("Member"); // 实例化User对象
				if (!$User->create($mdata)){
					// 如果创建失败 表示验证没有通过 输出错误提示信息
					$result = $this->codeModel->code(10000);
					$result['info'] = $User->getError();
					$this->response($result,$this->type);
				}else{
					 // 验证通过 可以进行其他数据操作
					$User->save($mdata);
				}

				$Ucmember = UCenterMember();
				if (!$Ucmember->create($udata)){
					// 如果创建失败 表示验证没有通过 输出错误提示信息
					$result = $this->codeModel->code(10000);
					$result['info'] = $Ucmember->getErrorMessage($error_code = $Ucmember->getError());
					$this->response($result,$this->type);
				}else{
					 // 验证通过 可以进行其他数据操作
					$Ucmember->save($udata);
				}
				clean_query_user_cache($uid,array('nickname','mobile','email','sex','signature'));
				$result = $this->codeModel->code(200,'更新完成');
				$this->response($result,$this->type);
			}
		break;
	    }
    }
	
	/**
	 * 登录提交页面
	 * @return [type] [description]
	 */
    public function login()
    {
		switch ($this->_method){
			case 'get': //get请求处理代码
				$result['info'] = '无GET方法';
			break;
			case 'post'://post请求处理代码
				$aUsername = $username = I('post.account', '', 'text');
		        $aPassword = I('post.password', '', 'text');

		        /* 调用UC登录接口登录 */
		        check_username($aUsername, $email, $mobile, $aUnType);
		        if (!check_reg_type($aUnType)) {
		        	$result = $this->codeModel->code(403);
		            $res['info']=L('_INFO_TYPE_NOT_OPENED_').L('_PERIOD_');
		        }
		        //根据用户账号密码获取用户ID或返回错误码
		        $code = $uid = UCenterMember()->login($username, $aPassword, $aUnType);
		        if($uid>0){
		        	//根据ID登陆用户
					$rs = $this->userModel->login($uid, 1); //登陆
		        }
		        //判断是否登陆成功
				if ($rs) {
					$token = $this->userModel->getToken($uid);
					$user_info = query_user(array('uid','nickname','sex','avatar32','mobile','email','title','last_login_ip','last_login_time',), $uid);
					$result = $this->codeModel->code(200,'登陆成功');
					$result['token']=$token; //用户持久登录token
					$result['data'] = $user_info;
				}else{
					if($code==-2){
						$result = $this->codeModel->code(1001);
					}
					if($code==-1){
						$result = $this->codeModel->code(1000);
					}
					if($code==0){
						$result = $this->codeModel->code(10000);
					}
				}
				$this->response($result,$this->type);
			break;
		}
    }
    /**
     * 通过手机号与验证码快速登陆
     * @return [type] [description]
     */
    public function quickLogin(){
    	switch ($this->_method){

		case 'post': //post请求处理代码
			$mobile = I('post.mobile','','text');
			$verify = I('post.verify','','text');//接收到的验证码

			//检查验证码是否正确
			
			$ret = D('Verify')->checkVerify($mobile,'mobile',$verify,0);
			if(!$ret){//验证码错误
	        	$result = $this->codeModel->code(3003);
	            $this->response($result,$this->type);
	        }
			$resend_time =  modC('SMS_RESEND','60','USERCONFIG');
	        if(time() > session('verify_time')+$resend_time ){//验证超时
	            $result = $this->codeModel->code(3001);
	            $this->response($result,$this->type);
	        }
	        
	        //验证通过后获取用户UID
	        $uid = UCenterMember()->where(array('mobile' => $mobile))->getField('id');
			//根据ID登陆用户
			$rs = $this->userModel->login($uid, 1); //登陆
			//判断是否登陆成功
			if ($rs) {
				$token = $this->userModel->getToken($uid);
				$user_info = query_user(array('uid','nickname','sex','avatar32','mobile','email','title','last_login_ip','last_login_time',), $uid);
				$result = $this->codeModel->code(200,'登陆成功');
				$result['token']=$token; //用户持久登录token
				$result['data'] = $user_info;
			}else{
				$result = $this->codeModel->code(10000);
			}
			$this->response($result,$this->type);
		break;
		}
    } 
	/**
	 * 用户注册
	 * @return [type] [description]
	 */
	public function register()
    {
        //获取参数
        $email = I('post.email','','text');
        $mobile = I('post.mobile','','text');
        $aRegType = I('post.reg_type', 'mobile', 'text');//注册类型，email mobile
        $aNickname = I('post.nickname', '', 'text');
        $aPassword = I('post.password', '', 'text');
        $aRegVerify = I('post.reg_verify', '', 'text');
        $aRole = I('post.role', 1, 'intval'); //初始角色

        if($aRegType == 'email'){
        	$aUsername = $username = $email;
        }
        if($aRegType == 'mobile'){
        	$aUsername = $username = $mobile;
        }
        if(empty($aNickname)){ //昵称为空，昵称等于注册的手机或邮箱
        	$aNickname = $aUsername;
        }
        
        //注册开关关闭，直接返回错误
        if (!modC('REG_SWITCH', '', 'USERCONFIG')) {
        	$result = $this->codeModel->code(403); 
			$result['info'] = L('_ERROR_REGISTER_CLOSED_');
			$this->response($result,$this->type);
        }

        if (IS_POST) {
            //注册用户
            $return = check_action_limit('reg', 'ucenter_member', 1, 1, true);
            if ($return && !$return['state']) {
            	$result = $this->codeModel->code(403); 
				$result['info'] = $return['info'];
				$this->response($result,$this->type);
            }
            if (!$aRole) {
				$result['info'] = L('_ERROR_ROLE_SELECT_').L('_PERIOD_');
				$result = $this->codeModel->code(403); 
				$this->response($result,$this->type);
            }
            //手机或邮箱的验证
            if (($aRegType == 'mobile' && modC('MOBILE_VERIFY_TYPE', 0, 'USERCONFIG') == 1) || (modC('EMAIL_VERIFY_TYPE', 0, 'USERCONFIG') == 2 && $aRegType == 'email')) {
                if (!D('Verify')->checkVerify($aUsername, $aRegType, $aRegVerify, 0)) {
                    $str = $aRegType == 'mobile' ? L('_PHONE_') : L('_EMAIL_');
					$result = $this->codeModel->code(3003); //验证失败
					$this->response($result,$this->type);	
                }
            }

            $aUnType = 0;
            //获取注册类型
            check_username($aUsername, $email, $mobile, $aUnType);
            if ($aRegType == 'email' && $aUnType != 2) {
                $result = $this->codeModel->code(403);
				$result['info'] = L('_ERROR_EMAIL_FORMAT_');
				$this->response($result,$this->type);
            }
            if ($aRegType == 'mobile' && $aUnType != 3) {
            	$result = $this->codeModel->code(403);
				$result['info'] = L('_ERROR_PHONE_FORMAT_');
				$this->response($result,$this->type);
            }
            if ($aRegType == 'username' && $aUnType != 1) {
                $result = $this->codeModel->code(403);
				$result['info'] = L('_ERROR_USERNAME_FORMAT_');
				$this->response($result,$this->type);
            }
            if (!check_reg_type($aUnType)) {
                $result = $this->codeModel->code(403);
				$result['info'] = L('_ERROR_REGISTER_NOT_OPENED_').L('_PERIOD_');
				$this->response($result,$this->type);
            }
            //exit;
            /* 注册用户 */
            $error_code = $uid =UCenterMember()->register($aUsername, $aNickname, $aPassword, $email, $mobile, $aUnType);
            
            if (0 < $uid) { //注册成功
                //$this->initInviteUser($uid, $aCode, $aRole);
                UCenterMember()->initRoleUser($aRole, $uid); //初始化角色用户
                $uid = UCenterMember()->login($username, $aPassword, $aUnType); //通过账号密码取到uid
                
                $rs = $this->userModel->login($uid, 1, $aRole); //登陆
                if($rs){//注册成功并登陆成功后返回的数据
                	$user_info = query_user(array('uid','nickname','avatar32','avatar64','avatar128','mobile','email','title'), $uid);
	                //组装返回的数据
	                $result = $this->codeModel->code(200,'注册成功');
					$result['token'] = $this->userModel->getToken($uid);
					$result['data'] = $user_info;
					$this->response($result,$this->type);
                }else{//注册成功未登陆成功返回的数据
                	$result = $this->codeModel->code(200,'注册成功');
                	$this->response($result,$this->type);
                }
                
            } else { //注册失败，显示错误信息
            	$result = $this->codeModel->code(10000);
				$result['info'] = $this->showRegError($error_code);
				$this->response($result,$this->type);	
            }
        }	
    }
	/**
	 * 退出登录
	 * @return [type] [description]
	 */
	public function logout()
    {
        //调用退出登录的API
        D('Member')->logout();
        $html='';
        if(UC_SYNC && is_login() != 1){
            include_once './api/uc_client/client.php';
            $html = uc_user_synlogout();
        }
        $result = $this->codeModel->code(200,L('_SUCCESS_LOGOUT_').L('_PERIOD_'));
		$this->response($result,$this->type);
    }
    /**
     * 密码类接口，如修改密码、找回密码
     * @return [type] [description]
     */
    public function password(){
    	switch ($this->_method){

		case 'post': //post请求处理代码
		$action = I('post.action','','text');//动作类型：找回密码：find 修改密码：change
			if($action === 'find'){//找回密码的执行过程
				$account = I('post.account','','text');
				$type = I('post.type','','text');
				$verify = I('post.verify','','text');//接收到的验证码
				$password = I('post.password','','text');//新密码设置
				//传入数据判断
				if(empty($account) || empty($type) || empty($password) || empty($verify)){
					$result = $this->codeModel->code(400);
		            $this->response($result,$this->type);
				}
				//检查验证码是否正确
				$ret = D('Verify')->checkVerify($account,$type,$verify,0);
				if(!$ret){//验证码错误
		        	$result = $this->codeModel->code(3003);
		            $this->response($result,$this->type);
		        }
				$resend_time =  modC('SMS_RESEND','60','USERCONFIG');
		        if(time() > session('verify_time')+$resend_time ){//验证超时
		            $result = $this->codeModel->code(3001);
		            $this->response($result,$this->type);
		        }
		        //获取用户UID
		        switch ($type) {
            		case 'mobile':
		        	$uid = UCenterMember()->where(array('mobile' => $account))->getField('id');
		        	break;
		        	case 'email':
		        	$uid = UCenterMember()->where(array('email' => $account))->getField('id');
		        	break;
		        }
		        //设置新密码
		        $password = think_ucenter_md5($password, UC_AUTH_KEY);
		        $data['id'] = $uid;
		        $data['password'] = $password;
		        //dump($data);exit;
		        $ret = UCenterMember()->save($data);
		        if($ret){
		        	//返回成功信息前处理
	        		clean_query_user_cache($uid, 'password');//删除缓存
	        		D('user_token')->where('uid=' . $uid)->delete();
	        		//返回数据
					$result = $this->codeModel->code(200); 
					$result['info'] = '新密码写入成功';
					$this->response($result,$this->type);
		        }else{
		        	$result = $this->codeModel->code(415); 
		        	$result['info'] = '新密码写入失败';
					$this->response($result,$this->type);
		        }
			}

			if($action == 'change'){//修改密码执行过程
				$this-> _needLogin();
				$old_password = I('post.old_password','','text');
				$new_password = I('post.new_password','','text');
				$uid = is_login();


				//检查旧密码是否正确
				$ret = UcenterMember()->verifyUser($uid,$old_password);
				if($ret){
					//重置用户密码
					$rs =  UcenterMember()->changePassword($old_password, $new_password);
					if($rs){
						$result = $this->codeModel->code(200,'密码修改成功'); 
						$this->response($result,$this->type);
					}else{
						$result = $this->codeModel->code(403); 
						$result['info'] = '密码修改失败';
						$this->response($result,$this->type);
					}
				}else{
					$result = $this->codeModel->code(403); 
					$result['info'] = '旧密码错误';
					$this->response($result,$this->type);
				}
			}
			$result = $this->codeModel->code(400); 
			$result['info'] = '参数错误';
			$this->response($result,$this->type);
		break;
		}
    }
    /**
     * 上传用户头像
     * @return [type] [description]
     */
    public function uploadAvatar(){

    	$aUid = $this->_needLogin(); //必须登录后操作
    	mkdir ("./Uploads/Avatar/".$aUid);

        $files = $_FILES;

        $setting  = C('PICTURE_UPLOAD');

        $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
        $driver = check_driver_is_exist($driver);
        $uploadConfig = get_upload_config($driver);

        /* 上传文件 */
        $setting['rootPath'] = './Uploads/Avatar';
        $setting['saveName'] = array('uniqid', '/'.$aUid.'/');
        $setting['savepath'] = '';
        $setting['subName'] = '';
        $setting['replace'] = true;

        $Upload = new \Think\Upload($setting, $driver, $uploadConfig);
        $info = $Upload->upload($files);

        if ($info) { //文件上传成功，不记录文件
        	//写入数据库
        	$data = array(
        		'uid' => $aUid, 
        		'status' => 1, 
        		'is_temp' => 0, 
        		'path' => $info['file']['savename'],
        		'driver'=> $driver, 
        		'create_time' => time()
        	);
        	$res = M('avatar')->where(array('uid' => $aUid))->save($data);
	        if (!$res) {
	            M('avatar')->add($data);
	        }
	        clean_query_user_cache($aUid, array('avatars','avatars_html'));
            /*适用于自动表单的图片上传方式*/
            $result = $this->codeModel->code(200); 
            $result['info'] = 'Success';
            $result['data'] = $info['file'];
        } else {
            $result = $this->codeModel->code(400); 
            $result['info'] = $Upload->getError();
        }
		$this->response($result,$this->type);

    }

    /**
     * 验证用户信息是否已存在
     * @return [type] [description]
     */
	 public function checkAccount()
    {
        $aAccount = I('post.account', '', 'text');
        $aType = I('post.type', '', 'text');
        if (empty($aAccount)) {
        	$result = $this->codeModel->code(415); 
			$return['info'] = L('_EMPTY_CANNOT_');
			$this->response($result,$this->type);
        }
        switch ($aType) {
            case 'mobile':
                
                $uid = UCenterMember()->where(array('mobile' => $aAccount))->getField('id');
                if ($uid) {
                	$result = $this->codeModel->code(1006); 
					$result['info'] = L('_ERROR_PHONE_EXIST_');//该手机号已经存在
					$this->response($result,$this->type);
                }else{
                	$result = $this->codeModel->code(200); 
					$result['info'] = L('_SUCCESS_VERIFY_');//不存在的手机
					$this->response($result,$this->type);
                }
                
                break;
            case 'email':
            	
                $uid = UCenterMember()->where(array('email' => $aAccount))->getField('id');
                //echo $uid;exit;
                if ($uid) {
                	$result = $this->codeModel->code(1006);
					$result['info'] = L('_ERROR_EMAIL_EXIST_');//该邮箱已经存在
					$this->response($result,$this->type);
                }else{
                	$result = $this->codeModel->code(200); 
					$result['info'] = L('_SUCCESS_VERIFY_');//不存在的邮箱
					$this->response($result,$this->type);
                }
                break;
        }
        //参数错误
        $result = $this->codeModel->code(400); 
		$this->response($result,$this->type);
    }
    /**
     * 用户位置坐标 未完善，下一版本继续完善
     * @return [type] [description]
     */
	public function location()
	{
		switch ($this->_method){

		case 'post': //post请求处理代码
			$this->_needLogin(); //必须登录后操作
			$aUid = I('post.uid',0,'intval');
			$alng = I('post.lng');
			$alat = I('post.lat');

			$data['uid'] = $aUid;
			$data['lng'] = $alng;
			$data['lat'] = $alat;
			
			M('member')->save($data); // 根据条件更新记录
			$result = $this->codeModel->code(200);
			$return['info'] = '用户定位更新完成';
			$this->response($result,$this->type);
		break;
		case 'get'://获取用户坐标
			$aUid = I('get.uid',0,'intval');

		break;
		}
	}
	/**
	 * 获取用户积分
	 * @return [type] [description]
	 */
	public function getScores(){
		$this->_needLogin(); //必须登录后操作
		$uid = is_login();

		$res = D('Ucenter/Score')->getAllScore($uid);

		$result = $this->codeModel->code(200);
		$result['data'] = $res;
		$this->response($result,$this->type);
	}
	
	/**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    public function showRegError($code = 0)
    {
        switch ($code) {
            case -1:
                $error = L('').modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').L('_ERROR_LENGTH_2_').L('_EXCLAMATION_');
                break;
            case -2:
                $error = L('_ERROR_USERNAME_FORBIDDEN_').L('_EXCLAMATION_');
                break;
            case -3:
                $error = L('_ERROR_USERNAME_USED_').L('_EXCLAMATION_');
                break;
            case -4:
                $error = L('_ERROR_LENGTH_PASSWORD_').L('_EXCLAMATION_');
                break;
            case -5:
                $error = L('_ERROR_EMAIL_FORMAT_2_').L('_EXCLAMATION_');
                break;
            case -6:
                $error = L('_ERROR_EMAIL_LENGTH_').L('_EXCLAMATION_');
                break;
            case -7:
                $error = L('_ERROR_EMAIL_FORBIDDEN_').L('_EXCLAMATION_');
                break;
            case -8:
                $error = L('_ERROR_EMAIL_USED_2_').L('_EXCLAMATION_');
                break;
            case -9:
                $error = L('_ERROR_PHONE_FORMAT_2_').L('_EXCLAMATION_');
                break;
            case -10:
                $error = L('_ERROR_FORBIDDEN_').L('_EXCLAMATION_');
                break;
            case -11:
                $error = L('_ERROR_PHONE_USED_').L('_EXCLAMATION_');
                break;
            case -20:
                $error = L('_ERROR_USERNAME_FORM_').L('_EXCLAMATION_');
                break;
            case -30:
                $error = L('_ERROR_NICKNAME_USED_').L('_EXCLAMATION_');
                break;
            case -31:
                $error = L('_ERROR_NICKNAME_FORBIDDEN_2_').L('_EXCLAMATION_');
                break;
            case -32:
                $error =L('_ERROR_NICKNAME_FORM_').L('_EXCLAMATION_');
                break;
            case -33:
                $error = L('_ERROR_LENGTH_NICKNAME_1_').modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').L('_ERROR_LENGTH_2_').L('_EXCLAMATION_');;
                break;
            default:
                $error = L('_ERROR_UNKNOWN_');
        }
        return $error;
    }	
}