<?php
/**
 * 登陆首页
 */
class LoginAction extends Action {
    Public function index(){
            if(!session('?uid')&&!session('?un')){
                $this->display();
            }
            else{
                $this->redirect('Index/index');
            }
    }
/**
*  登陆验证
* 
*/
    Public function login(){
      $ver=strtoupper(I('userver'));
      if(!IS_POST) halt("页面不存在!");
      /*if(md5($ver)!=session('verify')){
            $this->error('验证码错误',U('Login/index'));
        }*/
      $user=M('user')->where(array('username'=>I('username'),'userpass'=>md5(I('userpass'))))->find();
      if(empty($user))
      {
        $this->error('用户或密码错误！',U('Login/index'));
      }
    
     if($user['lock']!=0)
      {
        $this->error('该账号被锁定！',U('Login/index'));
      }
      
      $data=array(
        'id'=>$user['id'],
        'loginip'=>get_client_ip(0,true),
        'logintime'=>time()
      );
      
      M('user')->save($data);
      session(C('USER_AUTH_KEY'),$user['id']);
      session('un',$user['username']);
      session('rn',$user['rename']);
      session('up',$user['userpass']);
      session('lt',date('Y-m-d H:i:s',$user['logintime']));
      session('li',$user['loginip']);
      session('tg',$user['tager']);
      
      if($user['username']==C('RBAC_SUPERADMIN')){
        session(C('ADMIN_AUTH_KEY'),true);
      }
      import('ORG.Util.RBAC');
      RBAC::saveAccessList();
      $this->redirect('Index/index');
    }
    /**
     * 退出登陆
     * 
     */
    Public function logout()
    {
        session(null);
        $this->redirect('index');
    }
    /**
     * 取得验证码
     * 
     */
    Public function verify(){
        import('ORG.Util.Image');
        Image::buildImageVerify('4','1','png','','20');
        //Image::buildImageVerify('4','1','png');
    }
}
?>
