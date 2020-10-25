<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后台管理员控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Manager extends Admin_Controller {

	var $manager_id = false;

    var $model_name='manager';

    var $controller_name='manager';
	
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'manager_model' );
		$this->lang->load ( $this->controller_name );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 管理员列表
	 */
	function index() {
		$data ['title'] = $this->check_power ( lang ( 'manager_list' ) );
        $rows=20;
        $page = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['datas'] = $this->manager_model->get_managers($rows,$page);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . $this->controller_name. '?show';
        $config['total_rows'] = $this -> manager_model -> get_count_num($this->model_name);
        $config['per_page']			= $rows;
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();

		$this->load->view ( $this->config->item ( 'admin_folder' ) . 'managers', $data );
	}
	
	// ------------------------------------------------------------------------
	
	/*
	 * 管理员表单
	 */
	function form($id = false) {
		$this->check_power ( lang ( 'manager_add_title' ) );
		$this->load->helper ( 'form' );
		$this->load->library ( 'form_validation' );
		
		$data ['roles'] = $this->manager_model->get_all ( 'role', array (), 'id,name' );
		
		$data ['id'] = '';
		$data ['role_id'] = '';
		$data ['username'] = '';
		$data ['password'] = '123456';
        $data ['password_confirm'] = '123456';
		$data ['nickname'] = '';
		$data ['phone'] = '';
        $data ['phone_status'] = '';
		$data ['email'] = '';
        $data ['email_status'] = '';
		$data ['status'] = '';
		
		if ($id && $id != 1) {
			$this->manager_id = $id;
			$data ['title'] = lang ( 'manager_edit_title' );
			$manager = $this->manager_model->get_one ( $this->model_name, array (
					'id' => $id 
			) );
			if (! $manager) {
				$this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$data ['id'] = $manager ['id'];
			$data ['role_id'] = $manager ['role_id'];
			$data ['username'] = $manager ['username'];
			$data ['nickname'] = $manager ['nickname'];
			$data ['phone'] = $manager ['phone'];
            $data ['phone_status'] = $manager ['phone_status'];
			$data ['email'] = $manager ['email'];
            $data ['email_status'] = $manager ['email_status'];
			$data ['status'] = $manager ['status'];
		} else {
			$data ['title'] = lang ( 'manager_add_title' );
		}
		$this->form_validation->set_rules ( 'role_id', 'lang:manager_manager_role_id', 'trim|required|integer|max_length[11]' );
		$this->form_validation->set_rules ( 'username', 'lang:manager_username', 'trim|required|alpha_numeric|min_length[6]|max_length[50]|callback_check_username' );
		$this->form_validation->set_rules ( 'nickname', 'lang:manager_nickname', 'trim|alpha_chinese_dash_bias|min_length[4]|max_length[50]|callback_check_nickname' );
		$this->form_validation->set_rules ( 'phone', 'lang:manager_phone', 'trim|max_length[11]|numeric_phone|callback_check_phone' );
		$this->form_validation->set_rules ( 'email', 'lang:manager_email', 'trim|required|valid_email|max_length[50]|callback_check_email' );
		$this->form_validation->set_rules ( 'status', 'lang:manager_status', 'trim|integer|max_length[2]' );
        $this->form_validation->set_rules ( 'phone_status', 'lang:phone_status', 'trim|integer|max_length[2]' );
        $this->form_validation->set_rules ( 'email_status', 'lang:email_status', 'trim|integer|max_length[2]' );

        if ($this->input->post('password') != '' || $this->input->post('password_confirm') != ''){
            $this->form_validation->set_rules ( 'password', 'lang:manager_password', 'trim|required|alpha_dash_bias|min_length[6]|max_length[16]' );
            $this->form_validation->set_rules('password_confirm', 'lang:manager_password_confirm', 'trim|required|matches[password]');
        }

		if ($this->form_validation->run () == FALSE) {
			if (!$id) {
				$data ['role_id'] = $this->input->post ( 'role_id' );
                $data ['username'] = $this->input->post ( 'username' );
                $data ['nickname'] = $this->input->post ( 'nickname' );
                $data ['phone'] = $this->input->post ( 'phone' );
                $data ['phone_status'] = $this->input->post ( 'phone_status' );
                $data ['email'] = $this->input->post ( 'email' );
                $data ['email_status'] = $this->input->post ( 'email_status' );
			}
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'manager_form', $data );
		} else {
			$this->load->helper ( 'my_md5' );
			$save ['role_id'] = $this->input->post ( 'role_id' );
            $save ['username'] = strtolower($this->input->post ( 'username' ));
			$save ['nickname'] = $this->input->post ( 'nickname' );
			$save ['phone'] = $this->input->post ( 'phone' );
            $save ['email'] = strtolower($this->input->post ( 'email' ));
            $save ['phone_status'] = $this->input->post ( 'phone_status' );
            if (empty($save ['phone_status'])) {
                $save ['phone_status'] = 2;
            }
            $save ['email_status'] = $this->input->post ( 'email_status' );
            if (empty($save ['email_status'])) {
                $save ['email_status'] = 2;
            }


            if ($this->input->post('password') != '') {
                $save ['password'] = str_md5 ($this->input->post('password') );
            } else {
                $save ['password'] = str_md5 ( $data['password'] );
            }
			
			if ($id) {
				$save ['status'] = $this->input->post ( 'status' );
				$action = $this->manager_model->update ( $this->model_name, $save, array (
						'id' => $id 
				) );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->manager_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_update_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_update_data_span' ) . $change_str );
				unset ( $change_str );
			} else {
                $save ['status']=1;
				$action = $this->manager_model->insert ($this->model_name, $save );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->manager_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_add_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_add_data_span' ) . $change_str );
				unset ( $change_str );
			}
			
			if ($action) {
				$this->session->set_flashdata ( 'message', lang ( 'save_success' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			
			$this->session->set_flashdata ( 'error', lang ( 'save_fail' ) );
			redirect ( $this->config->item ( 'admin_folder' ) . 'manager/form/' . $id, $data );
		}
	}
	
	// ------------------------------------------------------------------------


    /**
     * 更改密码
     */
    function pwd() {
        $data ['title'] = $this->check_power ( lang ( 'change_password' ) );
        $this->load->helper ( 'form' );
        $this->load->library ( 'form_validation' );

        $data['password']='';
        $data['new_password']='';
        $data['new_password_confirm']='';


        $this->form_validation->set_rules ( 'password', 'lang:change_password_now', 'trim|required|alpha_dash_bias|min_length[6]|max_length[16]' );
        $this->form_validation->set_rules ( 'new_password', 'lang:change_password_new', 'trim|required|alpha_dash_bias|min_length[6]|max_length[16]' );
        $this->form_validation->set_rules ( 'new_password_confirm', 'lang:change_password_new_confirm', 'trim|required|alpha_dash_bias|min_length[6]|max_length[16]' );

        if ($this->form_validation->run () == FALSE) {
            $this->load->view ( $this->config->item ( 'admin_folder' ) . 'manager_pwd', $data );
        } else {
            $this->load->helper ( 'my_md5' );

            $save ['password'] = $this->input->post ( 'password' );
            $save ['new_password'] = $this->input->post ( 'new_password' );
            $save ['new_password_confirm'] = $this->input->post ( 'new_password_confirm' );
            $data_manager = $this->manager_model->get_one ( 'manager', array (
                'id' => $this->_manager->id,
                'password' => str_md5 ( $save ['password'] )
            ) );
            if($data_manager){//当前密码正确
                   if( $save ['new_password']!=$save ['new_password_confirm']){//新密码不等于确认新密码
                       $this->session->set_flashdata ( 'error', lang ( 'new_password_unfit' ) );
                       redirect ( $this->config->item ( 'admin_folder' ) . 'manager/pwd' );
                   }elseif($save ['new_password']==$save ['password']){//新密码等于当前密码
                       $this->session->set_flashdata ( 'error', lang ( 'now_new_password_fit' ) );
                       redirect ( $this->config->item ( 'admin_folder' ) . 'manager/pwd' );
                   }
                $action = $this->manager_model->update ($this->model_name, array (
                    'password' => str_md5 ( $save ['new_password'] )
                ), array (
                    'id' => $this->_manager->id
                ) );
                if ($action) {
                    $this->manager_model->save_manager_activity_logging ( $this->_manager, lang ( 'change_password' ) . ',' . lang ( 'loggin_manager_update_data_span' ) . '[' . str_md5 ( $data ['new_password'] ) . ']' );
                    //$this->session->set_flashdata ( 'message', lang ( 'change_password_succ' ) );
                    $manager_result = $this->auth->get_one_manager ( $this->auth->CI->manager_session->userdata ( 'manager' ) );
                    if ($manager_result) {
                        $this->manager_model->save_manager_activity_logging ( $manager_result, lang ( 'loggin_message_logged_out' ) );
                        /* 销毁manager_session */
                        $this->auth->logout ();
                        // 退出成功信息
                        $this->session->set_flashdata ( 'message', lang ( 'change_password_succ_logout' ) );
                    }
                    // 登录页面
                    redirect ( $this->config->item ( 'admin_folder' ) . 'login' );
                  //  redirect ( $this->config->item ( 'admin_folder' ) . 'manager');
                } else {
                    $this->session->set_flashdata ( 'error', lang ( 'changer_password_fail' ) );
                    redirect ( $this->config->item ( 'admin_folder' ) . 'manager/pwd' );
                }
            }else{
                $this->session->set_flashdata ( 'error', lang ( 'now_password_error' ) );
                redirect ( $this->config->item ( 'admin_folder' ) . 'manager/pwd' );
            }

        }
    }
    // ------------------------------------------------------------------------

    /**
     * 删除管理员
     */
    public function delete($id) {
        $data ['title'] = $this->check_power ( lang ( 'manager_list' ) );
        if ($this->input->is_ajax_request ()) {
            if ($id = 1) {
                $this->session->set_flashdata ( 'error', lang ( 'manager_undelete_manager' ) );
                redirect ( $this->config->item ( 'admin_folder' ) . 'manager' );
            }elseif($this->_manager->id == $id){
                $this->session->set_flashdata ( 'error', lang ( 'manager_delete_error' ) );
                redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
            }
            $del_data = $this->manager_model->get_one ($this->model_name, array (
                'id' => $id
            ) );
            if ($del_data) {
                if ($del_data && $this->manager_model->delete ($this->model_name, array ('id' => $del_data ['id']
                    ) )&& $del_data ['id']!=1) {
                    $this->session->set_flashdata ( 'message', lang ( 'delete_success' ) );
                    $change_str = '';
                    foreach ( $del_data as $str ) {
                        $change_str = $change_str . '[' . $str . ']';
                    }
                    $this->manager_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) .'('.$this->model_name.'),'  . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
                    unset ( $change_str );
                } else {
                    $this->session->set_flashdata ( 'message', lang ( 'delete_fail' ) );
                }
            } else {
                $this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
            }
            redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
        } else {
            show_404 ();
        }
    }

    // ------------------------------------------------------------------------
    /**
     * 删除管理员 用于ajax
     */
    public function del() {
        $data ['title'] = $this->check_power ( lang ( 'manager_list' ) );
        if ($this->input->is_ajax_request ()) {
            if ($this->uri->segment ( 3 ) == 1) {
                $this->session->set_flashdata ( 'error', lang ( 'manager_undelete_manager' ) );
                redirect ( $this->config->item ( 'admin_folder' ) . 'manager' );
            }elseif($this->_manager->id == $this->uri->segment ( 3 )){
                $this->session->set_flashdata ( 'error', lang ( 'manager_delete_error' ) );
                redirect ( $this->config->item ( 'admin_folder' ) . 'manager' );
            }
            $del_data = $this->manager_model->get_one ( $this->model_name, array (
                'id' => $this->uri->segment ( 3 )
            ) );
            if($del_data){
                if ($del_data && $this->manager_model->delete ( $this->model_name, array (
                    'id' => $del_data ['id']
                ) )&& $del_data ['id']!=1) {
                $msg = array (
                    'msg' => 1,
                    'info' => lang ( 'delete_success' )
                );
                $change_str = '';
                foreach ( $del_data as $str ) {
                    $change_str = $change_str . '[' . $str . ']';
                }
                $this->manager_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
                unset ( $change_str );
            } else {
                $msg = array (
                    'msg' => 0,
                    'info' => lang ( 'delete_fail' )
                );
            }
           }else{
                $msg = array (
                    'msg' => 2,
                    'info' => lang ( 'data_not_found' )
                );
            }
            echo json_encode ( $msg );
        } else {
            show_404 ();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * 更改密码
     * 用于ajax请求
     *
     * @access public
     *
     */
    function change_pwd() {
        if ($this->input->is_ajax_request ()) {
            $data ['password'] = $this->input->post ( 'password' );
            $data ['new_password'] = $this->input->post ( 'new_password' );
            $data ['new_password_confirm'] = $this->input->post ( 'new_password_confirm' );
            if (trim ( $data ['password'] ) == '') {
                $msg = array (
                    'error' => 1,
                    'error_msg' => lang ( 'now_password_null' )
                );
            } elseif (trim ( $data ['new_password'] ) == '') {
                // $data['change_pwd_msg'] = lang('new_password_null');
                $msg = array (
                    'error' => 2,
                    'error_msg' => lang ( 'new_password_null' )
                );
            } elseif (trim ( $data ['new_password_confirm'] ) == '') {
                // $data['change_pwd_msg'] = lang('new_password_fit_null');
                $msg = array (
                    'error' => 3,
                    'error_msg' => lang ( 'new_password_fit_null' )
                );
            } elseif (strlen ( trim ( $data ['password'] ) ) < 6 || strlen ( trim ( $data ['password'] ) ) > 18) {
                // $data['change_pwd_msg'] = lang('now_password_length');
                $msg = array (
                    'error' => 4,
                    'error_msg' => lang ( 'now_password_length' )
                );
            } elseif (strlen ( trim ( $data ['new_password'] ) ) < 6 || strlen ( trim ( $data ['new_password'] ) ) > 18) {
                // $data['change_pwd_msg'] = lang('new_password_length');
                $msg = array (
                    'error' => 5,
                    'error_msg' => lang ( 'new_password_length' )
                );
            } elseif (strlen ( trim ( $data ['new_password_confirm'] ) ) < 6 || strlen ( trim ( $data ['new_password_confirm'] ) ) > 18) {
                // $data['change_pwd_msg'] = lang('new_password_fit_length');
                $msg = array (
                    'error' => 6,
                    'error_msg' => lang ( 'new_password_fit_length' )
                );
            } else {
                $this->load->helper ( 'my_md5' );
                $manger_data = $this->manager_model->get_one ( $this->model_name, array (
                    'id' => $this->_manager->id,
                    'password' => str_md5 ( $data ['password'] )
                ) );
                if ($manger_data) {
                    if ($data ['new_password'] != $data ['new_password_confirm']) {
                        // $data['change_pwd_msg'] = lang('new_password_unfit');
                        $msg = array (
                            'error' => 7,
                            'error_msg' => lang ( 'new_password_unfit' )
                        );
                    } elseif ($data ['new_password'] == $data ['password']) {
                        // $data['change_pwd_msg'] = lang('now_new_password_fit');
                        $msg = array (
                            'error' => 8,
                            'error_msg' => lang ( 'now_new_password_fit' )
                        );
                    } else {
                        $action = $this->manager_model->update ( $this->model_name, array (
                            'password' => str_md5 ( $data ['new_password'] )
                        ), array (
                            'id' => $this->_manager->id
                        ) );
                        if ($action) {
                            $this->manager_model->save_manager_activity_logging ( $this->_manager, lang ( 'change_password' ) . ',' . lang ( 'loggin_manager_update_data_span' ) . '[' . str_md5 ( $data ['new_password'] ) . ']' );
                            // $data['change_pwd_msg'] = lang('change_password_succ');
                            $msg = array (
                                'error' => 9,
                                'error_msg' => lang ( 'change_password_succ' )
                            );
                        } else {
                            // $data['change_pwd_msg'] = lang('changer_password_fail');
                            $msg = array (
                                'error' => 10,
                                'error_msg' => lang ( 'changer_password_fail' )
                            );
                        }
                    }
                } else {
                    // $data['change_pwd_msg'] = lang('now_password_error');
                    $msg = array (
                        'error' => 11,
                        'error_msg' => lang ( 'now_password_error' )
                    );
                }
            }
            echo json_encode ( $msg );
        } else {
            show_404 ();
        }
    }

    // ------------------------------------------------------------------------

    /**
     * 更改密码退出
     * 用于ajax请求
     *
     * @access public
     *
     */
    function change_pwd_logout() {
        if ($this->input->is_ajax_request ()) {
            $manager_result = $this->auth->get_one_manager ( $this->auth->CI->manager_session->userdata ( 'manager' ) );
            if ($manager_result) {
                $this->manager_model->save_manager_activity_logging ( $manager_result, lang ( 'loggin_message_logged_out' ) );
                /* 销毁manager_session */
                $this->auth->logout ();
                // 退出成功信息
                $this->session->set_flashdata ( 'message', lang ( 'change_password_succ_logout' ) );
            }
            // 登录页面
            redirect ( $this->config->item ( 'admin_folder' ) . 'login' );
        } else {
            show_404 ();
        }
    }
    // ------------------------------------------------------------------------
	/**
	 * 检查用户名
	 */
	function check_username($str) {
		$username = $this->manager_model->check_table_field ( $this->model_name, 'username',strtolower($str), $this->manager_id );
		if ($username) {
			$this->form_validation->set_message ( 'check_username', lang ( 'error_manager_username_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// ------------------------------------------------------------------------
	
	/**
	 * 检查昵称
	 */
	function check_nickname($str) {
		$nickname = $this->manager_model->check_table_field ( $this->model_name, 'nickname', $str, $this->manager_id );
		if ($nickname) {
			$this->form_validation->set_message ( 'check_nickname', lang ( 'error_manager_nickname_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// ------------------------------------------------------------------------
	
	/**
	 * 检查手机号码
	 */
	function check_phone($str) {
		$phone = $this->manager_model->check_table_field ( $this->model_name, 'phone', $str, $this->manager_id );
		if ($phone) {
			$this->form_validation->set_message ( 'check_phone', lang ( 'error_manager_phone_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// ------------------------------------------------------------------------
	
	/**
	 * 检查电子邮箱
	 */
	function check_email($str) {
		$email = $this->manager_model->check_table_field ( $this->model_name, 'email',strtolower($str), $this->manager_id );
		if ($email) {
			$this->form_validation->set_message ( 'check_email', lang ( 'error_manager_email_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}
    // ------------------------------------------------------------------------
}

/* End of file manager.php */
/* Location: ./app/admin/controllers/manager.php */
