<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后端权限管理控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Power extends Admin_Controller {

	var $power_id = false;

    var $model_name='power';

    var $controller_name='power';

	function __construct() {
		parent::__construct ();
		$this->load->model ( 'power_model' );
		$this->lang->load ( $this->controller_name );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 权限列表
	 */
	function index() {
		$data ['title'] = $this->check_power ( lang ( 'power_list' ) );
		$data ['datas'] = $this->power_model->power_datas ();
		$this->load->view ( $this->config->item ( 'admin_folder' ) . 'powers', $data );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 权限表单
	 */
	function form($id = false) {
		$this->check_power ( lang ( 'power_add_title' ) );
		$this->load->helper ( 'form' );
		$this->load->library ( 'form_validation' );
		
		// 获取父级下拉菜单数据
		$data ['pname'] = $this->power_model->get_pid_data ();
		$data ['id'] = '';
		$data ['pid'] = '';
		$data ['name'] = '';
		$data ['icon'] = '';
		$data ['url'] = '';
		$data ['status'] = '';
		$data ['rank'] = '';
		
		if ($id && $id != 1) {
			$this->power_id = $id;
			$data ['title'] = lang ( 'power_edit_title' );
			$power = $this->power_model->get_one ( $this->model_name, array (
					'id' => $id 
			) );
			if (! $power) {
				$this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$data ['id'] = $power ['id'];
			$data ['pid'] = $power ['pid'];
			$data ['name'] = $power ['name'];
			$data ['icon'] = $power ['icon'];
			$data ['url'] = $power ['url'];
			$data ['status'] = $power ['status'];
			$data ['rank'] = $power ['rank'];
		} else {
			$data ['title'] = lang ( 'power_add_title' );
		}

        $save ['pid'] = $this->input->post ( 'pid' );
        if (empty($save ['pid'])) {
            $save ['pid'] = 1;
        }
		$this->form_validation->set_rules ( 'name', 'lang:power_name', 'trim|required|alpha_chinese_dash_bias|max_length[50]|callback_check_name['.$save['pid'].']');
		$this->form_validation->set_rules ( 'icon', 'lang:power_icon', 'trim|alpha_dash_bias_icon|max_length[50]' );
		$this->form_validation->set_rules ( 'url', 'lang:power_url', 'trim|alpha_dash_bias_url|max_length[150]' );
		$this->form_validation->set_rules ( 'rank', 'lang:power_rank', 'trim|integer|max_length[11]' );
		$this->form_validation->set_rules ( 'status', 'lang:power_status', 'trim|integer|max_length[6]' );
		
		if ($this->form_validation->run () == FALSE) {
			// 无限下拉菜单 $post = $this->input->post ();
			// if ($post) {
			// $pid = $post ['pid'];
			// if (sizeof ( $pid ) > 0) {
			// $data ['pid'] = isset ( $post ['pid'] [count ( $post ['pid'] ) - 2] ) ? $post ['pid'] [count ( $post ['pid'] ) - 2] : 0;
			// }
			// }
			// unset ( $post );
			if (!$id) {
				$data ['pid'] = $this->input->post ( 'pid' );
                $data ['name'] = $this->input->post ( 'name' );
                $data ['icon'] = $this->input->post ( 'icon' );
                $data ['url'] = $this->input->post ( 'url' );
                $data ['rank'] = $this->input->post ( 'rank' );
			}
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'power_form', $data );
		} else {
			
			// 无限下拉菜单 $post = $this->input->post ();
			// $save ['pid'] = isset ( $post ['pid'] [count ( $post ['pid'] ) - 2] ) ? $post ['pid'] [count ( $post ['pid'] ) - 2] : 0;
			

            $save ['url'] = $this->input->post ( 'url' );
            $save ['rank'] = $this->input->post ( 'rank' );
            $save ['icon'] = $this->input->post ( 'icon' );

            if (empty($save ['url'])) {
                $save ['url'] = 'default_view';
            }
            if (empty($save ['rank'] )) {
                $save ['rank'] = 0;
            }
            if (empty($save ['icon'])) {
                $save ['icon'] = 'icon-home';
            }
			$save ['name'] = $this->input->post ( 'name' );
			
			if ($id) {
				$save ['status'] = $this->input->post ( 'status' );
				$action = $this->power_model->edit ( $save, $id );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->power_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_update_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_update_data_span' ) . $change_str );
				unset ( $change_str );
			} else {
                $save ['status']=1;
				$action = $this->power_model->add ( $save );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->power_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_add_data_table_span' ) .'('.$this->model_name.'),'  . lang ( 'loggin_manager_add_data_span' ) . $change_str );
				unset ( $change_str );
			}
			if ($action) {
				
				$this->session->set_flashdata ( 'message', lang ( 'save_success' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name  );
			}
			
			$this->session->set_flashdata ( 'error', lang ( 'save_fail' ) );
			redirect ( $this->config->item ( 'admin_folder' ) . 'power/form/' . $id, $data );
		}
	}
	
	// ------------------------------------------------------------------------
	/**
	 * 检查名称
	 */
	function check_name($str,$pid=1) {
		$name = $this->power_model->check_table_field ($this->model_name ,'name',$str, $this->power_id ,array('pid'=>$pid));
		if ($name) {
			$this->form_validation->set_message ( 'check_name', lang ( 'error_power_name_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// ------------------------------------------------------------------------
	/**
	 * 删除权限
	 */
	public function delete($id) {
		$data ['title'] = $this->check_power ( lang ( 'power_list' ) );
		if ($this->input->is_ajax_request ()) {
			$del_data = $this->power_model->get_one ($this->model_name, array (
					'id' => $id 
			) );
			if ($del_data) {
				if ($del_data && ! $this->power_model->get_one ($this->model_name, array (
						'pid' => $del_data ['id'] 
				) ) && $this->power_model->delete ( $this->model_name, array (
						'id' => $del_data ['id'] 
				) )) {
					$this->power_model->del ( $del_data ['id'] );
					$this->session->set_flashdata ( 'message', lang ( 'delete_success' ) );
					$change_str = '';
					foreach ( $del_data as $str ) {
						$change_str = $change_str . '[' . $str . ']';
					}
					$this->power_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) .'('.$this->model_name.'),'  . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
					unset ( $change_str );
				} else {
					$this->session->set_flashdata ( 'message', lang ( 'delete_fail' ) );
				}
			} else {
				$this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
			}
			redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name  );
		} else {
			show_404 ();
		}
	}
	
	// ------------------------------------------------------------------------
	/**
	 * 删除权限 用于ajax
	 */
	public function del() {
		$data ['title'] = $this->check_power ( lang ( 'power_list' ) );
		if ($this->input->is_ajax_request ()) {
			$del_data = $this->power_model->get_one ( $this->model_name, array (
					'id' => $this->uri->segment ( 3 ) 
			) );
            if($del_data){
			if ($del_data && ! $this->power_model->get_one ( $this->model_name, array (
					'pid' => $del_data ['id'] 
			) ) && $this->power_model->delete ($this->model_name, array (
					'id' => $del_data ['id'] 
			) )) {
				$this->power_model->del ( $del_data ['id'] );
				$msg = array (
						'msg' => 1,
						'info' => lang ( 'delete_success' ) 
				);
				$change_str = '';
				foreach ( $del_data as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->power_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
	 * 无级分类下拉列表
	 */
	public function get_cate() {
		$this->load->helper ( 'my_stepless_classification' );
		echo ajax_stepless_classification ( array (
				'table' => 'power',
				'field_name' => 'name' 
		) );
	}
	
	// ------------------------------------------------------------------------
}

/* End of file power.php */
/* Location: ./app/admin/controllers/admin/power.php */
