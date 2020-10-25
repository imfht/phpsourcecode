<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后端角色管理控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Role extends Admin_Controller {

	var $role_id = false;

    var $model_name='role';

    var $controller_name='role';

	function __construct() {
		parent::__construct ();
		$this->load->model ( 'role_model' );
		$this->lang->load ( 'role' );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 角色列表
	 */
	function index() {
		$data ['title'] = $this->check_power ( lang ( 'role_list' ) );
        $rows  = 20;
        $page = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['datas'] = $this->role_model->role_datas($rows,$page);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . $this->controller_name. '?show';
        $config['total_rows'] = $this -> role_model -> get_count_num($this->model_name);
        $config['per_page']			= $rows;
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();

		$this->load->view ( $this->config->item ( 'admin_folder' ) . 'roles', $data );
	}
	
	// ------------------------------------------------------------------------
	
	/*
	 * 角色表单
	 */
	function form($id = false) {
		$this->check_power ( lang ( 'role_add_title' ) );
		$this->load->helper ( 'form' );
		$this->load->library ( 'form_validation' );
		
		$data ['power_datas'] = $this->power_datas ();
		
		$data ['id'] = '';
		$data ['name'] = '';
		$data ['powers'] = '';
		$data ['introduce'] = '';
		$data ['status'] = '';
		
		if ($id && $id != 1) {
			$this->role_id = $id;
			$data ['title'] = lang ( 'role_edit_title' );
			$role = $this->role_model->get_one ( $this->model_name, array (
					'id' => $id 
			) );
			if (! $role) {
				$this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$data ['id'] = $role ['id'];
			$data ['name'] = $role ['name'];
			$data ['powers'] = $role ['powers'];
			$data ['introduce'] = $role ['introduce'];
			$data ['status'] = $role ['status'];
		} else {
			$data ['title'] = lang ( 'role_add_title' );
		}
		
		$this->form_validation->set_rules ( 'name', 'lang:role_name', 'trim|required|alpha_chinese_dash_bias|max_length[50]|callback_check_name' );
		$this->form_validation->set_rules ( 'introduce', 'lang:role_introduce', 'trim|required|max_length[50]' );
		$this->form_validation->set_rules ( 'powers', 'lang:role_powers', 'required|max_length_checkbox[100]' );
		$this->form_validation->set_rules ( 'status', 'lang:role_status', 'trim|integer|max_length[2]' );
		
		if ($this->form_validation->run () == FALSE) {
			if ($this->input->post ( 'powers' )) {
				$data ['powers'] = implode ( ',', $this->input->post ( 'powers' ) );
			}
            if(!$id){
                $save ['name'] = $this->input->post ( 'name' );
                $save ['introduce'] = $this->input->post ( 'introduce' );
            }
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'role_form', $data );
		} else {
			$save ['name'] = $this->input->post ( 'name' );
			$save ['introduce'] = $this->input->post ( 'introduce' );
			$save ['powers'] = implode ( ',', $this->input->post ( 'powers' ) );
			
			if ($id) {
				$save ['status'] = $this->input->post ( 'status' );
				$action = $this->role_model->update ( $this->model_name, $save, array (
						'id' => $id 
				) );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->role_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_update_data_table_span' ) .'('.$this->model_name.'),'  . lang ( 'loggin_manager_update_data_span' ) . $change_str );
				unset ( $change_str );
			} else {
                $save ['status']=1;
				$action = $this->role_model->insert ( $this->model_name, $save );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->role_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_add_data_table_span' ) .'('.$this->model_name.'),'  . lang ( 'loggin_manager_add_data_span' ) . $change_str );
				unset ( $change_str );
			}
			
			if ($action) {
				$this->session->set_flashdata ( 'message', lang ( 'save_success' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			
			$this->session->set_flashdata ( 'error', lang ( 'save_fail' ) );
			redirect ( $this->config->item ( 'admin_folder' ) . 'role/form/' . $id, $data );
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 查询所有权限
	 *
	 * @access private
	 */
	private function power_datas() {
        $this->check_power ( lang ( 'role_list' ) );
		$this->load->model ( 'power_model' );
		$new_power_datas = array ();
		$parent_id = 0;
		$power_data = $this->power_model->power_datas ( array (
				'status' => 1 
		) );
		foreach ( $power_data as $data ) {
			if ($data ['level']) {
				$new_power_datas [$parent_id] ['children_datas'] [] = $data;
			} else {
				$new_power_datas [$data ['id']] = $data;
				$parent_id = $data ['id'];
			}
		}
		return $new_power_datas;
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 检查名称
	 */
	function check_name($str) {
		$name = $this->role_model->check_table_field ( $this->model_name,'name',$str, $this->role_id );
		
		if ($name) {
			$this->form_validation->set_message ( 'check_name', lang ( 'error_role_name_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// ------------------------------------------------------------------------
	/**
	 * 删除角色
	 */
	public function delete($id) {
		$data ['title'] = $this->check_power ( lang ( 'role_list' ) );
		if ($this->input->is_ajax_request ()) {
			if ($id = 1) {
				redirect ( $this->config->item ( 'admin_folder' ) .$this->controller_name );
			}
			$del_data = $this->role_model->get_one ($this->model_name, array (
					'id' => $id 
			) );
			if ($del_data) {
				if ($del_data && ! $this->role_model->get_one ( 'manager', array (
						'role_id' => $del_data ['id'] 
				) ) && $this->role_model->delete ( $this->model_name, array (
						'id' => $del_data ['id'] 
				) )) {
					$this->session->set_flashdata ( 'message', lang ( 'delete_success' ) );
					$change_str = '';
					foreach ( $del_data as $str ) {
						$change_str = $change_str . '[' . $str . ']';
					}
					$this->role_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
	 * 删除角色 用于ajax
	 */
	public function del() {
		$data ['title'] = $this->check_power ( lang ( 'role_list' ) );
		if ($this->input->is_ajax_request ()) {
			$del_data = $this->role_model->get_one ( $this->model_name, array (
					'id' => $this->uri->segment ( 3 ) 
			) );
            if($del_data){
			if ($del_data && ! $this->role_model->get_one ( 'manager', array (
					'role_id' => $del_data ['id'] 
			) ) && $this->role_model->delete ($this->model_name, array (
					'id' => $del_data ['id'] 
			) )) {
				$msg = array (
						'msg' => 1,
						'info' => lang ( 'delete_success' ) 
				);
				$change_str = '';
				foreach ( $del_data as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->role_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
}

/* End of file role.php */
/* Location: ./app/admin/controllers/role.php */
