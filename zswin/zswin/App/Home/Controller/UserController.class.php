<?php
namespace Home\Controller;
use User\Api\UserApi;
require_once APP_PATH . 'User/Conf/config.php';

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends HomeController
{

	

    /* 注册页面 */
    public function register($username = '', $nickname = '', $password = '',$repassword = '', $email = '', $verify = '', $type = 'start')
    {
        $type = op_t($type);
        
        if (!C('USER_ALLOW_REGISTER')) {
            $this->error('注册已关闭');
        }
      
        $verifyarr=explode(',', C('VERIFY_OPEN'));
        if(in_array('1', $verifyarr)){
            		$this->assign('isverify',1);
            	}else{
            		
            		$this->assign('isverify',0);
         }
        if (IS_POST) { //注册用户
            
        /* 检测验证码 TODO: */
        	 if(in_array('1', $verifyarr)){
           if(!$this->check_verify($verify)){
            $this->error('验证码输入错误！');
           } 
        	 }
        	
        	
        	
           if ($password != $repassword) {
              
                    $this->error('两次密码输入不一致');
                
            }
            /* 调用注册接口注册用户 */
            $User = new UserApi;
            $uid = $User->register($username, $nickname, $password, $email);
            if (0 < $uid) { //注册成功
            	
            	sendMessage($uid, 0, '注册成功', '恭喜您！您已经注册成功，请尽快<a href="'.U('Ucenter/yzmail').'">验证邮箱地址</a>,第一时间获取网站动态！', 0);
                $uid = $User->login($username, $password);//通过账号密码取到uid
                
                D('Member')->login($uid, false);//登陆
                
          
                asyn_sendmail($email,2);
                setuserscore($uid, C('REGSCORE'));
                $this->success('注册成功并登陆！',cookie('referurl'));
            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($uid));
            }
        } else { //显示注册表单
            if (is_login()) {
                redirect(cookie('referurl'));
            }
            if(cookie('referurl')==''){
           cookie('referurl',$_SERVER['HTTP_REFERER']);
            }
            
            $this->display();
        }
    }

 

    /* 登录页面 */
    public function login($username = '', $password = '', $verify = '', $remember = '')
    {
       
  	    $verifyarr=explode(',', C('VERIFY_OPEN'));
        if(in_array('2', $verifyarr)){
            		$this->assign('isverify',1);
            	}else{
            		
            		$this->assign('isverify',0);
         }
        if (IS_POST) { //登录验证
        /* 检测验证码 TODO: */
        	 if(in_array('2', $verifyarr)){
           if(!$this->check_verify($verify)){
            $this->error('验证码输入错误！');
           } 
        	 }

            /* 调用UC登录接口登录 */
            $user = new UserApi;
            
            if(preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',$username)){
            	$type=2;
            }else{
            	$type=1;
            }
            
            $uid = $user->login($username, $password,$type);
          
            if (0 < $uid) { //UC登录成功
            	
                /* 登录用户 */
                $Member = D('Member');
               
                if ($Member->login($uid, $remember == 'on')) { //登录用户
                    //TODO:跳转到登录前页面
                   
                    $this->success('登陆成功', cookie('referurl'));
                   
                   // $this->success($html, get_nav_url(C('AFTER_LOGIN_JUMP_URL')));
                } else {
                    $this->error($Member->getError());
                }
                

            } else { //登录失败
                switch ($uid) {
                    case -1:
                        $error = '用户不存在或被禁用！';
                        break; //系统级别禁用
                    case -2:
                        $error = '密码错误！';
                        break;
                    default:
                        $error = $uid;
                        break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }

        } else { //显示登录表单
            if (is_login()) {
                redirect(cookie('referurl'));
            }
            if(cookie('referurl')==''){
           cookie('referurl',$_SERVER['HTTP_REFERER']);
            }
           
            $this->display();
        }
    }

    /* 退出登录 */
    public function logout()
    {
        if (is_login()) {
            D('Member')->logout();
            
            
            cookie('referurl',null);
           
             $this->success('退出成功！',U('User/login'));	
           
           
        } else {
            $this->redirect('User/login');
        }
    }

    /* 验证码，用于登录和注册 */
    public function verify()
    {
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

    /* 用户密码找回首页 */
    public function mi()
    {
        $username = I('username');
        $email = I('email');

        if (IS_POST) { //登录验证
            //检测验证码


            //根据用户名获取用户UID
            $user = D('User/UcenterMember')->where(array('username' => $username, 'email' => $email, 'status' => 1))->find();
            $uid = $user['id'];
            if (!$uid) {
                $this->error("用户名或邮箱错误");
            }
            $verify = think_encrypt($uid,'',3000);
            $url = "http://$_SERVER[HTTP_HOST]". U('Home/User/reset',array('uid'=>$uid,'verify'=>$verify));
            $body = C('USER_RESPASS') . "<br/>" . $url . "<br/>" . C('WEB_SITE') ."系统自动发送--请勿直接回复<br/>" . date('Y-m-d H:i:s', TIME()) . "</p>";
            $subject=C('WEB_SITE') . "密码找回";
            
            
            send_mail($email,$subject,$body);
          
            $this->success('密码找回邮件发送成功',U('Home/User/login'));
        } else {
            if (is_login()) {
                redirect(U('Index/index'));
            }

            $this->display();
        }
    }

    /**
     * 重置密码
     */
    public function reset()
    {
        //检查参数
        $uid = I('uid');
        $verify = I('verify');
        if (!$uid || !$verify) {
            $this->error("参数错误", U('Home/User/mi'));
        }

        //确认邮箱验证码正确
        $expectVerify = think_decrypt($verify);
        if ($expectVerify != $uid) {
            $this->error($expectVerify, U('Home/User/mi'));
        }

        $data['password']='123456';
         $Api = new UserApi();
            $res = $Api->updateInfo($uid,'admin',$data);
       
        $this->success('密码重置成功,新密码为123456', U('Home/User/login'));
    }

  

    private function getResetPasswordVerifyCode($uid)
    {
        $user = D('User/UcenterMember')->where(array('id' => $uid))->find();
        $clear = implode('|', array($user['uid'], $user['username'], $user['last_login_time'], $user['password']));
        $verify = thinkox_hash($clear, UC_AUTH_KEY);
        return $verify;
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
                $error = '签名长度必须在50个字符以内！';
            case -35:
                $error = '不要注册的太频繁哦！';
                break;
            default:
                $error = '未知错误24';
        }
        return $error;
    }


    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function profile()
    {
        if (!is_login()) {
            $this->error('您还没有登陆', U('User/login'));
        }
        if (IS_POST) {
            //获取参数
            $uid = is_login();
            $password = I('post.old');
            $repassword = I('post.repassword');
            $data['password'] = I('post.password');
            empty($password) && $this->error('请输入原密码');
            empty($data['password']) && $this->error('请输入新密码');
            empty($repassword) && $this->error('请输入确认密码');

            if ($data['password'] !== $repassword) {
                $this->error('您输入的新密码与确认密码不一致');
            }

            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if ($res['status']) {
                $this->success('修改密码成功！');
            } else {
                $this->error($res['info']);
            }
        } else {
            $this->display();
        }
    }
    
 public function changucenter()
    {
        if (!is_login()) {
            $this->error('您还没有登陆', U('User/login'));
        }
      
            //获取参数
            $uid = is_login();
            $oldpassword = I('post.oldpassword');
            $repassword = I('post.repassword');
            $password = I('post.password');
           // $data['email'] = I('post.email');
            
            if($password!=$repassword){
            	 $this->error('两次密码输入不一致');
            }
            if(!empty($password)){
            	
            	
            	$data['password']=$password;
            }
            
           $data['id']=$uid;
         

           

            $Api = new UserApi();
            $res = $Api->updateInfo($uid,$oldpassword , $data);
            if ($res['status']) {
            
                $this->success('修改成功！');
            } else {
                $this->error($res['info']);
            }
    
    }
    
   public function changeinfo()
    {
        if (!is_login()) {
            $this->error('您还没有登陆', U('User/login'));
        }
       
            if(false===!$data=D('member')->create()){
           
             $res=D('member')->save($data);
             if ($res) {
             	
             	
                $this->success('修改成功！');
            } else {
                $this->error('修改失败');
            }
            	
            }else{
            	
            	$this->error($this->showRegError(D('member')->getError()));
            }
            
            
            
           
           
       
    }

}