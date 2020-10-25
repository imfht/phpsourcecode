<?php
require APPPATH.'/libraries/htmlpurifier/library/HTMLPurifier.auto.php';
  class Home extends CI_Controller
  {
    function __construct(){
      parent::__construct();
      $this->load->database();
      $this->load->library('template');
      $this->load->library('auth_verification');
      $this->load->library('session');

    }
    public function index()
    {
      $this->login();
//      $config = HTMLPurifier_Config::createDefault();
//
//// configuration goes here:
//      $config->set('Core.Encoding', 'UTF-8'); // replace with your encoding
//      $config->set('HTML.Doctype', 'XHTML 1.0 Transitional'); // replace with your doctype
//
//      $purifier = new HTMLPurifier($config);
//
//// untrusted input HTML
//      $html = '<b>Simple and short';
//
//      $pure_html = $purifier->purify($html);
//
//      echo '<pre>' . htmlspecialchars($pure_html) . '</pre>';
//      echo $pure_html;
//      $this->load->model('Note_model', 'note');
//      $this->note->change_note(1, 2, array('content' => $pure_html));
//      $note = $this->note->get_note(1, 2);
//      echo $note['content'];
    }

    /**
     * 用户登录
     */
    public function login()
    {
      //如果没有登录,则进入主页
      if( !$this->isLogined() )
      {
        //$isRun返回一个bool值,表单验证通过才为true
        $isRun = $this->auth_verification->login_verification();

        //登录表单验证已通过
        if( $isRun )
        {
          redirect('ng');
        }
        //登录表单验证未通过或者是首次进入主页面(登录页面)
        else
        {
          //加载主页(登录页)视图
          $data['title'] = "主页";
          $this->template->output('home', $data);
        }

      } //end of check_isLogined
      //已经登录
      else
      {
        redirect('ng');
      }
    }

    /**
     * 用户注册
     */
    public function signin()
    {
      //没有设置session
      if( !$this->isLogined() )
      {
        $this->load->library('captcha');

        //$isRun返回一个bool值,表单验证通过才为true
        $isRun = $this->auth_verification->signin_verification();

        //注册表单验证已通过
        if( $isRun )
        //if($this->signin_verification())
        {
          //加载模型
          $this->load->model('User_model', 'signin');

          //往数据库添加用户并设置session
          $this->signin->add_user();

          //加载模型
          $this->load->model('Notebook_model', 'notebook');
          $this->load->model('Note_model', 'note');
          $user_id = $this->session->userdata('userId');
          //添加一个默认笔记本
          $default_notebook_id = $this->notebook->add_default_notebook($user_id);
          if($default_notebook_id){
            $this->note->add_note($user_id, $default_notebook_id, '我的笔记', '笔记中的内容');
          }


          //将这个笔记本更改为默认笔记本
//          $this->notebook->change_default_notebook($this->session->userdata('userId'),
//            null, $notebook_id, true);


          redirect('ng');

        }
        //注册表单验证未通过或者是首次进入注册页面
        else
        {

          //加载注册页视图
          $data['title'] = '注册';
          $data['captcha'] = $this->captcha->build();
          $this->template->output('signin', $data);
        }

      }
      //已经设置了session,就跳转到登录后的页面
      else
      {
        redirect('ng');
      }
    }


    /**
     * 检查是否已经登录(是否已经设置session)
     */
    private function isLogined()
    {
      return  $this->session->userdata('userId') && $this->session->userdata('username');
    }

    /**
     * 用户登出
     */
    public function logout()
    {
      //删除session
      $this->session->unset_userdata( array('userId', 'username') );
      redirect('home');
    }




    public function check_cap(){
      $this->load->library('captcha');
      return $this->captcha->isValidCaptcha($this->input->post('captcha'));
    }

    //判断用户和密码是否有效
    public function check_user(){
      $this->load->model('User_model', 'user');
      return $this->user->validate();
    }


  }