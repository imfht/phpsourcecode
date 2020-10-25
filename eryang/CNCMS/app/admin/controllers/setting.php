<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后台系统设置控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Setting extends Admin_Controller {
	/**
	 * 构造函数
	 */
	function __construct() {
		parent::__construct ();
		$this->lang->load ( 'setting' );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 站点设置
	 */
	function web() {
		$this->the_setting ( lang ( 'set_web' ), 'set_web', 'config_web' );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 后台设置
	 */
	function admin() {
		$this->the_setting ( lang ( 'set_admin' ), 'set_admin', 'config_admin' );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 公共代码
	 *
	 * @access private
	 * @param
	 *        	string 权限名称
	 * @param
	 *        	string 控制器名
	 * @param
	 *        	string 操作表名
	 */
	private function the_setting($power_title, $controller, $table) {
		$data ['title'] = $this->check_power ( $power_title );
		$data['email_to_user']='';
        if(SITE_ADMIN_EMAIL_PASSWORD){
            $data['email_password']=substr(SITE_ADMIN_EMAIL_PASSWORD,0,1).'********'.substr(SITE_ADMIN_EMAIL_PASSWORD,strlen(SITE_ADMIN_EMAIL_PASSWORD)-1,strlen(SITE_ADMIN_EMAIL_PASSWORD)+1);
        }else{
            $data['email_password']='';
        }
		if (strtolower ( $_SERVER ['REQUEST_METHOD'] ) == 'post') {
			$post = $this->input->post ();
			$this->load->library ( 'form_validation' );
			/* 公共验证部分 */
			if ($post ['tab'] == 'filename') { // 文件名设置
				$this->form_validation->set_rules ( 'resources', 'lang:set_resources', 'trim|required|max_length[50]|alpha_numeric' );
				$this->form_validation->set_rules ( 'css', 'lang:set_css', 'trim|required|max_length[50]|alpha_numeric' );
				$this->form_validation->set_rules ( 'js', 'lang:set_js', 'trim|required|max_length[50]|alpha_numeric' );
                $this->form_validation->set_rules ( 'img', 'lang:set_img', 'trim|required|max_length[50]|alpha_numeric' );
                $this->form_validation->set_rules ( 'editor', 'lang:set_editor', 'trim|required|max_length[50]|alpha_numeric' );
				$this->form_validation->set_rules ( 'art', 'lang:set_art', 'trim|required|max_length[50]|alpha_numeric' );
				$this->form_validation->set_rules ( 'valicode', 'lang:set_valicode', 'trim|required|max_length[50]|alpha_numeric' );
				$this->form_validation->set_rules ( 'uploads', 'lang:set_uploads', 'trim|required|max_length[50]|alpha_numeric' );
			} elseif ($post ['tab'] == 'upload') { // 上传大小
				$this->form_validation->set_rules ( 'upload_image_size', 'lang:set_upload_image_size', 'trim|max_length[10]|number_positive_integer' );
				$this->form_validation->set_rules ( 'upload_flash_size', 'lang:set_upload_flash_size', 'trim|max_length[10]|number_positive_integer' );
				$this->form_validation->set_rules ( 'upload_media_size', 'lang:set_uploads_media_size', 'trim|max_length[10]|number_positive_integer' );
				$this->form_validation->set_rules ( 'upload_file_size', 'lang:set_uploads_file_size', 'trim|max_length[10]|number_positive_integer' );
			} elseif ($post ['tab'] == 'theme') { // 主题设置
				$this->form_validation->set_rules ( 'theme', 'lang:set_theme_name', 'trim|max_length[50]|alpha_numeric' );
			}elseif ($post ['tab'] == 'email') { // 邮件设置
                if($post ['email_status']==1){
                    $this->form_validation->set_rules ( 'email_smtp', 'lang:set_email_smtp', 'trim|required|max_length[20]' );
                    $this->form_validation->set_rules ( 'email_port', 'lang:set_email_smtp_port', 'trim|required|max_length[2]|number_positive_integer' );
                    $this->form_validation->set_rules ( 'email_user', 'lang:set_email_user', 'trim|required|max_length[50]|valid_email' );
                    if(SITE_ADMIN_EMAIL_PASSWORD){
                        $this->form_validation->set_rules ( 'email_password', 'lang:set_email_password', 'trim|max_length[50]' );
                    }else{
                        $this->form_validation->set_rules ( 'email_password', 'lang:set_email_password', 'trim|required|max_length[50]' );
                    }
                    $this->form_validation->set_rules ( 'email_title', 'lang:set_email_title', 'trim|required|max_length[50]' );
                    $this->form_validation->set_rules ( 'email_username', 'lang:set_email_username', 'trim|required|max_length[50]' );
                }else{
                $this->form_validation->set_rules ( 'email_status', 'lang:set_email_status', 'trim|max_length[2]|number_positive_integer' );
                $this->form_validation->set_rules ( 'email_smtp', 'lang:set_email_smtp', 'trim|max_length[20]' );
                $this->form_validation->set_rules ( 'email_port', 'lang:set_email_smtp_port', 'trim|max_length[2]|number_positive_integer' );
                $this->form_validation->set_rules ( 'email_user', 'lang:set_email_user', 'trim|max_length[50]|valid_email' );
                $this->form_validation->set_rules ( 'email_password', 'lang:set_email_password', 'trim|max_length[50]' );
                $this->form_validation->set_rules ( 'email_title', 'lang:set_email_title', 'trim|max_length[50]' );
                $this->form_validation->set_rules ( 'email_username', 'lang:set_email_username', 'trim|max_length[50]' );
                }
            }elseif ($post ['tab'] == 'email_test') { // 邮件测试
                if(SITE_ADMIN_EMAIL_STATUS!=1){
                    if ($table == 'config_web') {
                        $controller = 'setting/web';
                    } else {
                        $controller = 'setting/admin';
                    }
                    $this->session->set_flashdata ( 'error', lang ( 'set_email_is_close' ) );
                    redirect ( $this->config->item ( 'admin_folder' ) . $controller );
                }
                $this->form_validation->set_rules ( 'email_user', 'lang:set_email_user', 'trim|max_length[50]|valid_email' );
                $this->form_validation->set_rules ( 'email_to_user', 'lang:set_email_to_user', 'trim|required|max_length[50]|valid_email' );
                $this->form_validation->set_rules ( 'email_content', 'lang:set_email_content', 'trim|max_length[50]' );
            }elseif ($post ['tab'] == 'encryption_key') { // 密钥设置
				$this->form_validation->set_rules ( 'encryption_key_begin', 'lang:set_encryption_key_begin', 'trim|required|max_length[20]|alpha_numeric' );
				$this->form_validation->set_rules ( 'encryption_key_end', 'lang:set_encryption_key_end', 'trim|required|max_length[20]|alpha_numeric' );
			}
			/* 站点设置 */
			elseif ($post ['tab'] == 'web_basic') { // 基本设置
				$this->form_validation->set_rules ( 'name', 'lang:set_web_name', 'trim|required|max_length[50]' );
				$this->form_validation->set_rules ( 'logo', 'lang:set_web_logo', 'trim|max_length[50]|alpha_dash_bias_filename' );
				$this->form_validation->set_rules ( 'icp', 'lang:set_web_icp', 'trim|max_length[50]' );
				$this->form_validation->set_rules ( 'statistical_code', 'lang:set_web_statistical_code', 'trim' );
				$this->form_validation->set_rules ( 'share_code', 'lang:set_web_share_code', 'trim' );
				$this->form_validation->set_rules ( 'keywords', 'lang:set_web_keywords', 'trim|required|max_length[200]' );
				$this->form_validation->set_rules ( 'description', 'lang:set_web_description', 'trim|required|max_length[200]' );
			} elseif ($post ['tab'] == 'web_status') { // 站点状态
				$this->form_validation->set_rules ( 'status', 'lang:set_web_status', 'trim' );
				$this->form_validation->set_rules ( 'close_reason', 'lang:set_web_close_reason', 'trim|max_length[200]' );
			} elseif ($post ['tab'] == 'web_reg_agreement') { // 注册协议
				$this->form_validation->set_rules ( 'reg_agreement', 'lang:set_web_reg_agreement', 'trim' );
			} 			/* 后台设置 */
                elseif ($post ['tab'] == 'admin_basic') { // 基本设置
				$this->form_validation->set_rules ( 'name', 'lang:set_admin_name', 'trim|required|max_length[50]' );
				$this->form_validation->set_rules ( 'logo', 'lang:set_admin_logo', 'trim|max_length[50]' );
                }
            if ($this->form_validation->run () == FALSE) {
				$this->load->helper ( 'form' );
            } else {
                if ($table == 'config_web') {
                    $controller = 'setting/web';
                } else {
                    $controller = 'setting/admin';
                }
                if($post ['tab'] == 'email_test'){//邮件测试
                   // $msg=$post['email_content'];
                    //2833725009@qq.com
                    $this->load->library ( 'email' );
                    $this->load->config ( 'email' );
                    $this->email->from ( $this->config->item('smtp_user'),$this->config->item('username') );
                    $this->email->to ( $post['email_to_user'] );
                    $this->email->subject ( $this->config->item('title') );
                    $this->email->message (html_entity_decode(SITE_ADMIN_EMAIL_CONTENT));
                    //echo $this->email->print_debugger ();
                    $data_email ['type'] = 2;
                    $data_email ['title'] = $this->config->item('title');
                    $data_email ['content'] = SITE_ADMIN_EMAIL_CONTENT;
                    $data_email ['email'] = $post['email_to_user'];
                    $data_email ['addtime'] = now();
                    $data_email ['sendtime'] = now();
                    $data_email ['addip'] = $this->input->ip_address ();
                    if($this->email->send ()){
                        $data_email ['status'] = 1;
                        $this->base_model->save_manager_activity_logging ( $this->_manager, lang ( 'set_email_test' ).$post['email_to_user'].lang ( 'set_email_send_succ' ) );
                        $this->session->set_flashdata ( 'message', lang ( 'set_email_send_succ' ) );
                    }else{
                        $data_email ['status'] = 2;
                        $this->session->set_flashdata ( 'error', lang ( 'set_email_send_fail' ) );
                    }
                    $this->base_model->insert('email_log',$data_email);
                    redirect ( $this->config->item ( 'admin_folder' ) . $controller );
                }
                if($post ['tab']==encryption_key ){
                    if(encryption_key_begin&&encryption_key_end){
                        $this->session->set_flashdata ( 'error', lang ( 'set_encryption_error' ) );
                        redirect ( $this->config->item ( 'admin_folder' ) . $controller );
                    }
                }
				unset ( $post ['tab'] );
                $post['email_smtp']=strtolower($post['email_smtp']);
                $post['email_user']=strtolower($post['email_user']);
                if($post['email_password']){
                    if($post['email_password']==$data['email_password']){
                        $post['email_password']=SITE_ADMIN_EMAIL_PASSWORD;
                    }else{
                        $post['email_password']=$post['email_password'];
                    }
                }else{
                    $post['email_password']=SITE_ADMIN_EMAIL_PASSWORD;
                }
				$action = $this->base_model->update ( $table, $post );
				if ($action) {
					$change_str = '';
					foreach ( $post as $str ) {
						$change_str = $change_str . '[' . $str . ']';
					}
					$this->base_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_update_data_table_span' ) . '(' . $table . '),' . lang ( 'loggin_manager_update_data_span' ) . $change_str );
					unset ( $change_str );
					$this->session->set_flashdata ( 'message', lang ( 'save_success' ) );
				} else {
					$this->session->set_flashdata ( 'error', lang ( 'save_fail' ) );
                }
				redirect ( $this->config->item ( 'admin_folder' ) . $controller );
			}
		}
		$this->load->view ( $this->config->item ( 'admin_folder' ) . $controller, $data );
	}
	
	// ------------------------------------------------------------------------
}

/* End of file setting.php */
/* Location: ./app/admin/controllers/setting.php */
