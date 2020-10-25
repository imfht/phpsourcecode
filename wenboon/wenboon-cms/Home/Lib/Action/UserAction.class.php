<?php
    class UserAction extends Action {
        Public function _initialize(){
            $this->site=M('site')->find(1);
            if(strtolower($Think.ACTION_NAME)=='register') return;
            if(strtolower($Think.ACTION_NAME)=='login') return;
            if(strtolower($Think.ACTION_NAME)=='logout') return;
            
            if(session('muid')==null){
                $this->redirect('User/login');
                exit();
            }
        }
        public function register(){
            if(I('username')&&I('userpass')){
                $db=M('member');
                $data=$db->create();
                $count=M('member')->where(array('username'=>I('username')))->count();
                if($count) $this->error('用户名已存在!');
                $data['userpass']=md5($data['userpass']);
                $db->add($data);
                $this->success('注册成功！',U('login'));
            }
            else{
                $this->display();
            }
        }
        public function login(){
            if(I('username')&&I('userpass')){
                $user=M('member')->where(array('username'=>I('username'),'userpass'=>md5(I('userpass'))))->find();
                  if(empty($user))
                  {
                    $this->error('用户或密码错误！');
                  }
                  unset($user['userpass']);
                  session('muid',$user['id']);
                  session('muser',$user);
                  $this->redirect('User/index');
            }
            else{
                $this->display();
            }
        }
        Public function logout()
        {
            session('muid',null);
            session('muser',null);
            $this->redirect('Index/index');
        }
        public function index(){
            $this->display();
        }
        public function mydiscuss(){
            $this->display();
        }
    }