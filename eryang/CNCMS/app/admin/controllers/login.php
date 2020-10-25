<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 用户登录/退出控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Login extends MY_Controller {

	
	/**
	 * 构造函数
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		parent::__construct ();
		$this->load->library ( 'auth' );
		$this->lang->load ( 'login' );
		$this->load->helper ( array (
				'my_valicode',
				'date' 
		) );
		$this->load->model ( array (
				'check_log_model' 
		) );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 默认入口
	 */
	function index() {
		// 判断是否已经登录
		$redirect = $this->auth->is_logged_in ( false, false );
		if ($redirect) {
			// 得到管理员信息
			$manager_result = $this->auth->get_one_manager ( $this->auth->CI->manager_session->userdata ( 'manager' ) );
			if ($manager_result) {
				// 记录管理员登录信息
				$this->auth->save_one_manager_logging ( $manager_result );
				// 更新管理员登录时间

				$this->auth->update_one_manager ( $manager_result );
			}
			redirect ( $this->config->item ( 'admin_folder' ) . 'main' );
		} elseif ($redirect == 1) {
			// 得到管理员信息
			$manager_result = $this->auth->get_one_manager ( $this->auth->CI->manager_session->userdata ( 'manager' ) );
			if ($manager_result) {
				// 记录管理员登录信息
				$this->auth->save_one_manager_logging ( $manager_result );
				// 更新管理员登录时间
				$this->auth->update_one_manager ( $manager_result );
			}
            redirect ( $this->config->item ( 'admin_folder' ) . 'main' );
		}
		$this->load->helper ( 'form' );
		$submitted = $this->input->post ( 'submitted' );
		// 表单提交
		if ($submitted) {
			// 短时间登录次数
			$num = $this->check_log_model->get_logs_by_ip ( $this->input->ip_address () );
			if ($num < 5) { // 小于5次
			    // 用户名
				$username = strtolower($this->input->post ( 'username' ));
				// 密码
				$password = $this->input->post ( 'password' );
				// 是否记住密码
				$remember = $this->input->post ( 'remember' );
				// 验证码
				$valicode = $this->input->post ( 'valicode' );
				
				if ($username == NULL) { // 用户名为空
					$this->session->set_flashdata ( 'error', lang ( 'error_username_null' ) );
				} elseif ($password == NULL) { // 密码为空
					$this->session->set_flashdata ( 'error', lang ( 'error_password_null' ) );
				}
				if ($valicode) { // 有验证码
					if ($valicode == NULL) { // 验证为空
						$this->session->set_flashdata ( 'error', lang ( 'error_valicode_null' ) );
						redirect ( $this->config->item ( 'admin_folder' ) . 'login' );
					} elseif (strtolower ( $valicode ) != strtolower ( $this->session->userdata ( 'valicode' ) )) { // 验证码不正确
						$this->session->set_flashdata ( 'error', lang ( 'error_valicode' ) );
						redirect ( $this->config->item ( 'admin_folder' ) . 'login' );
					}
				}
				// 检查用户和密码
				$login = $this->auth->login_manager ( $username, $password, $remember );
				if ($login) {
					$this->session->unset_userdata ( 'check_flag' );
					$this->session->unset_userdata ( 'valicode' );
					// 得到管理员信息
					$manager_result = $this->auth->get_one_manager ( $this->auth->CI->manager_session->userdata ( 'manager' ) );
					if ($manager_result->status == 1) {
						// 记录管理员登录信息
						$this->auth->save_one_manager_logging ( $manager_result );
						// 更新管理员登录时间
						$this->auth->update_one_manager ( $manager_result );
						$redirect = $this->auth->CI->manager_session->userdata ( 'redirect' );
						if ($redirect) {
							redirect ( $this->config->item ( 'admin_folder' ) . $redirect );
						}
						redirect ( $this->config->item ( 'admin_folder' ) . 'main' );
					} else { // 管理员被禁用
						/* 销毁manager_session */
						$this->auth->logout ();
						$this->session->set_flashdata ( 'error', lang ( 'manager_status' ) );
						redirect ( $this->config->item ( 'admin_folder' ) . 'login' );
					}
				} else {
					// 添加登录错误次数
					$data_check_log ['error_content'] = lang ( 'error_authentication_failed' );
					$data_check_log ['ip_address'] = $this->input->ip_address ();
					$data_check_log ['add_time'] = now ();
					$this->base_model->insert ( 'check_log', $data_check_log );
					
					$this->session->set_flashdata ( 'error', lang ( 'error_authentication_failed' ) );
					$this->session->set_userdata ( 'check_flag', '0' );
					redirect ( $this->config->item ( 'admin_folder' ) . 'login' );
				}
			} else { // 15分钟内错误次数过多
				$this->session->set_flashdata ( 'error', lang ( 'error_more_num' ) );
			}
		}
		
		if ($this->session->userdata ( 'check_flag' ) == NULL) { // 无验证码登录
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'login' );
		} elseif ($this->session->userdata ( 'check_flag' ) == 0) { // 验证码登录
			$vals = array (
					'img_path' => '..' . SITE_ADMIN_VALICODE . '/' . date ( 'Y-m-d' ) . '/',
					'img_url' => SITE_ADMIN_VALICODE . '/' . date ( 'Y-m-d' ) . '/',
					'img_class' => 'span3 center',
					'img_width' => '120',
					'img_height' => '30',
					'expiration' => 120 
			);
			$img = create_captcha ( $vals );
			// 生成验证码
			$this->session->set_userdata ( 'valicode', $img ['word'] );
			$data ['img'] = $img;
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'login_vali', $data );
		} else {
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'login' );
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 退出
	 *
	 * @access public
	 * @return void
	 */
	function logout() {
		$manager_result = $this->auth->get_one_manager ( $this->auth->CI->manager_session->userdata ( 'manager' ) );
		if ($manager_result) {
			$this->base_model->save_manager_activity_logging ( $manager_result, lang ( 'loggin_message_logged_out' ) );
			/* 销毁manager_session */
			$this->auth->logout ();
			/* 销毁session */
			$this->session->unset_userdata ( 'valicode' );
			// $this -> session -> sess_destroy();
			// 退出成功信息
			$this->session->set_flashdata ( 'message', lang ( 'message_logged_out' ) );
		}
		// 登录页面
		redirect ( $this->config->item ( 'admin_folder' ) . 'login' );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 点击更新验证码
	 */
	function show_valicode() {
		if ($this->input->is_ajax_request ()) {
			$vals = array (
					'img_path' => '..' . SITE_ADMIN_VALICODE . '/' . date ( 'Y-m-d' ) . '/',
					'img_url' => SITE_ADMIN_VALICODE . '/' . date ( 'Y-m-d' ) . '/',
					'img_class' => 'span3 center',
					'img_width' => '120',
					'img_height' => '30',
					'expiration' => 120 
			);
			$img = create_captcha ( $vals );
			$this->session->set_userdata ( 'valicode', $img ['word'] );
			echo json_encode ( $img ['image'] );
		} else {
			show_404 ();
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 更换主题皮肤
	 */
	function show_skin() {
		if ($this->input->is_ajax_request ()) {
			$manager = $this->auth->CI->manager_session->userdata ( 'manager' );
			if ($manager) {
				$data ['skin'] = $this->input->post ( 'skin' );
				$this->base_model->update ( 'manager', $data, array (
						'id' => $manager ['id'] 
				) );
				$manager_result = $this->auth->get_one_manager ( $this->auth->CI->manager_session->userdata ( 'manager' ) );
				$this->lang->load ( 'admin_common' );
				$this->base_model->save_manager_activity_logging ( $manager_result, lang ( 'mannager_logging_change_skin' ) . ',' . lang ( 'loggin_manager_update_data_span' ) . '[' . $data ['skin'] . ']' );
			} else {
				// 登录页面
				redirect ( $this->config->item ( 'admin_folder' ) . 'login' );
			}
		} else {
			show_404 ();
		}
	}
	
	// ------------------------------------------------------------------------

    /**
     * $this->load->library ( 'mymd5' );
    echo $this->mymd5->md5_encrypt('1fsdfasdf23fsdffffffffffff456fsdfsssss','123456');

    echo '<br/>';
    //字符串转换成专用url
    echo rawurlencode($this->mymd5->md5_encrypt('1fsdfasdf23fsdffffffffffff456fsdfsssss','123456'));
    echo '<br/>';
    //url转换成字符串
    echo rawurldecode($this->mymd5->md5_encrypt('1fsdfasdf23fsdffffffffffff456fsdfsssss','123456'));
    echo '<br/>';
    echo $this->mymd5->md5_decrypt($this->mymd5->md5_encrypt('12345689489849','123456'),'123456');
    exit;
     *
     * $this->db->set('status','status+1',FALSE);
    $this->db->where('id','1');
    $this->db->update('manager');
     * 忘记密码
     */
    function forgot_password($type=FALSE){
        $this->session->unset_userdata ( 'manager_result');
        $this->session->unset_userdata ( 'by_email_result');
            if($type=='activate_email'){//激活邮箱
                $data['title']	= lang('activate_email');
            }else{//忘记密码
                $data['title']	= lang('forgot_password');
            }
        $data['username']='';
        $this->load->helper ( 'form' );
        $submitted = $this->input->post('submitted');
        if ($submitted){
            $this->load->helper('string');
            $this->load->model ('manager_model');
            $this->load->library ( 'form_validation' );

            $this->form_validation->set_rules ( 'username', 'lang:login_username', 'trim|required|alpha_numeric|min_length[5]|max_length[50]' );

            if ($this->form_validation->run () == FALSE) {
                //$this->load->view ( $this->config->item ( 'admin_folder' ) . 'forgot_password', $data );
            }else{
                $manager_result = $this->manager_model->get_manager_by_username(strtolower($this->input->post('username')));
                if ($manager_result){
                    $this->session->set_userdata ( 'manager_result', $manager_result->username );

                    if($type=='activate_email'){//激活邮箱
                        redirect($this->config->item ( 'admin_folder' ) .'login/check_username/activate_email');
                    }else{//忘记密码
                        redirect($this->config->item ( 'admin_folder' ) .'login/check_username');
                    }
                }else{
                    $this->session->set_flashdata('error', lang('data_not_find'));
                    if($type=='activate_email'){//激活邮箱
                        redirect($this->config->item ( 'admin_folder' ) .'login/forgot_password/activate_email');
                    }else{//忘记密码
                        redirect($this->config->item ( 'admin_folder' ) .'login/forgot_password');
                    }

                }
            }
        }
        $this->load->view($this->config->item ( 'admin_folder' ) .'forgot_password', $data);
    }
    // ------------------------------------------------------------------------

    /**
     * setcookie('ceshi','测试');
     * print_r($_COOKIE['ceshi']);
     * 检查用户名
     */
    function check_username($type=FALSE){
        $this->session->unset_userdata ( 'by_email_result');
        $manager_username= $this->session->userdata ( 'manager_result');
        if($manager_username){
            $this->load->helper('string');
            $this->load->model ('manager_model');
            $manager_result=$this->manager_model->get_manager_by_username(strtolower($manager_username));
            if($manager_result){
                if($manager_result->email){
                    $data['email']=$this->_get_email_dim($manager_result->email);
                }else{
                    $this->session->set_flashdata('error', lang('not_email'));
                    redirect($this->config->item ( 'admin_folder' ) .'login/forgot_password');
                }
                if($type=='activate_email'){//激活邮箱
                    $data['title']	= lang('activate_email');
                    $data['btn_span']=lang('activate_email');
                }else{//忘记密码
                    $data['title']=lang('find_password_by_email');
                    $data['btn_span']=lang('reset_password');
                }
                $data['username']=$manager_result->username;
                $data['register_email']='';
                $this->load->helper ( 'form' );
                $submitted = $this->input->post('submitted');
                if ($submitted){
                    $this->load->library ( 'form_validation' );
                    $this->form_validation->set_rules ( 'register_email', 'lang:register_email', 'trim|required|valid_email|max_length[50]' );

                    if ($this->form_validation->run () == FALSE) {
                        //$this->load->view ( $this->config->item ( 'admin_folder' ) . 'forgot_password', $data );
                    }else{
                        if (strtolower($this->input->post('register_email'))==$manager_result->email){
                            //发送邮件
                            if($this->_allow_find_by_email()){
                                if($type=='activate_email'){//激活邮箱
                                    if($this->_send_email($manager_result->email,2)){
                                        $this->session->set_userdata ( 'by_email_result', $manager_result->email );
                                        redirect($this->config->item ( 'admin_folder' ) .'login/by_email/activate_email');
                                    }else{//邮件发送失败
                                        $this->session->set_flashdata('error', lang('email_send_fail'));
                                        redirect($this->config->item ( 'admin_folder' ) .'login/check_username/activate_email');
                                    }
                                }else{//忘记密码
                                    if($this->_send_email($manager_result->email,1)){
                                        $this->session->set_userdata ( 'by_email_result', $manager_result->email );
                                        redirect($this->config->item ( 'admin_folder' ) .'login/by_email');
                                    }else{//邮件发送失败
                                        $this->session->set_flashdata('error', lang('email_send_fail'));
                                        redirect($this->config->item ( 'admin_folder' ) .'login/check_username');
                                    }
                                }
                            }else{
                                $this->session->set_flashdata('error', lang('email_not_send'));
                                if($type=='activate_email'){//激活邮箱
                                    redirect($this->config->item ( 'admin_folder' ) .'login/check_username/activate_email');
                                }else{//忘记密码
                                    redirect($this->config->item ( 'admin_folder' ) .'login/check_username');
                                }
                             }
                        }else{
                            $this->session->set_flashdata('error', lang('email_error'));
                            if($type=='activate_email'){//激活邮箱
                                redirect($this->config->item ( 'admin_folder' ) .'login/check_username/activate_email');
                            }else{//忘记密码
                                redirect($this->config->item ( 'admin_folder' ) .'login/check_username');
                            }
                        }
                    }
                }
                $this->load->view($this->config->item ( 'admin_folder' ) .'check_username', $data);
            }else{
                $this->session->set_flashdata('error', lang('data_not_find'));
                if($type=='activate_email'){//激活邮箱
                    redirect($this->config->item ( 'admin_folder' ) .'login/forgot_password/activate_email');
                }else{//忘记密码
                    redirect($this->config->item ( 'admin_folder' ) .'login/forgot_password');
                }
            }
        }else{
            show_404 ();
        }
    }
    // ------------------------------------------------------------------------

    /**
    * 邮箱找回密码
    */
    function by_email($type=FALSE){
        $this->session->unset_userdata ( 'manager_result');
        $by_email_result= $this->session->userdata ( 'by_email_result');
        if($by_email_result){
            $this->load->helper ( 'form' );
            if($type=='activate_email'){//激活邮箱
                $data['title']	= lang('activate_email');
                $data['email_hint']=lang('activate_email_hint');
                $data['email_url_a_hint']='login/forgot_password/activate_email';
                $data['email_url_hint']=lang('activate_email');
                $data['hint_three_row']=lang('activate_hint_three_row');
            }else{//忘记密码
                $data['title']=lang('find_password_by_email');
                $data['email_hint']=lang('email_hint');
                $data['email_url_a_hint']='login/forgot_password';
                $data['email_url_hint']=lang('find_password');
                $data['hint_three_row']=lang('hint_three_row');
            }
            $data['email_url']=$this->_get_email_url($by_email_result);
            $this->load->view($this->config->item ( 'admin_folder' ) .'by_email', $data);
        }else{
            show_404 ();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * 邮箱找回密码
     */
    function reset_pwd(){
        if($this->uri->segment(3)==FALSE){
            show_404 ();
        }else{
            $check_url=$this->base_model->get_one('email_log',array('status'=>1,'url'=>$this->uri->segment(3)));
            if($check_url){
                if((time()-$check_url['sendtime'])>=60*60*24||$check_url['active_time']>0){//时间是否过期
                    $data['check']=TRUE;
                }else{
                $code=rawurldecode(rawurldecode(rawurldecode($this->uri->segment(3))));
                //检查status
                $manager_result=$this->_check_status(substr($code,8));
                //检查code
                if($manager_result){
                    $check_code_result=$this->_check_code(substr($code,0,8),$manager_result->email);
                    if($check_code_result){
                        $data['check']=FALSE;
                        $data['username']=$manager_result->username;
                        $data['submit_url']=$this->uri->segment(3);
                    }else{
                        $data['check']=TRUE;
                    }
                }else{
                    $data['check']=TRUE;
                }
                }
                $this->load->helper ( 'form' );
                $data['title']=lang('find_password_by_email');
                $this->load->view($this->config->item ( 'admin_folder' ) .'reset_pwd', $data);
            }else{
                show_404 ();
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * 重置密码
     */
    function reset_new_pwd(){
        // 表单提交
        $submitted = $this->input->post ( 'submitted' );
        if ($submitted) {
            $submit_url = $this->input->post ( 'submit_url' );
            $check_url=$this->base_model->get_one('email_log',array('status'=>1,'active_time'=>0,'url'=>$submit_url));

            if($check_url){
                if((time()-$check_url['sendtime'])>=60*60*24||$check_url['active_time']>0){//时间是否过期
                    $data['check']=TRUE;
                }else{
                $code=rawurldecode(rawurldecode(rawurldecode($submit_url)));
                //检查status
                $manager_result=$this->_check_status(substr($code,8));
                //检查code
                if($manager_result){
                    $check_code_result=$this->_check_code(substr($code,0,8),$manager_result->email);
                    if($check_code_result){
                        $data['check']=FALSE;
                        $data['username']=$manager_result->username;
                        $data['submit_url']=$submit_url;
                    }else{
                        $data['check']=TRUE;
                    }
                }else{
                    $data['check']=TRUE;
                }
                $this->load->library ( 'form_validation' );
                $this->form_validation->set_rules ( 'new_password', 'lang:new_password', 'trim|required|alpha_dash_bias|min_length[6]|max_length[16]' );
                $this->form_validation->set_rules ( 'new_password_fit', 'lang:new_password_fit', 'trim|required|matches[new_password]' );

                if ($this->form_validation->run () == FALSE) {
                   // $this->load->view ( $this->config->item ( 'admin_folder' ) . 'reset_pwd' );
                }else{
                    $save ['new_password']= $this->input->post ( 'new_password' );
                    $this->load->helper ( 'my_md5' );
                    $action = $this->base_model->update_equal_field ( 'manager', array (
                        'password' => str_md5 ( $save ['new_password'] )
                    ), array (
                        'username' => $manager_result->username
                    ) );
                    if($action){
                        $this->base_model->update ( 'email_log', array ( 'active_time' => time() ), array (
                            'id' => $check_url['id']
                        ) );
                        $this->session->set_flashdata ( 'message', lang ( 'change_password_succ' ) );
                    }else{
                        $this->session->set_flashdata ( 'error', lang ( 'change_password_fail' ) );
                    }
                    redirect ( $this->config->item ( 'admin_folder' ) . 'login' );
                }
                }
                $this->load->helper ( 'form' );
                $data['title']=lang('find_password_by_email');
                $this->load->view($this->config->item ( 'admin_folder' ) .'reset_pwd', $data);
            }else{
                show_404 ();
            }
        }else{
            show_404 ();
        }
    }

    // ------------------------------------------------------------------------

    //低级浏览器
    public function oops(){
        $data['title']='您的浏览器过于陈旧，需要升级';
        $this->load->view($this->config->item ( 'admin_folder' ) .'oops', $data);
    }

    // ------------------------------------------------------------------------

}

/* End of file login.php */
/* Location: ./app/admin/controllers/login.php */
