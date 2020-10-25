<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后端模块管理控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Mode extends Admin_Controller {

    var $mode_id = false;

    var $model_name='mode';

    var $controller_name='mode';

	function __construct() {
        parent::__construct ();
		$this->load->model ( 'mode_model' );
		$this->lang->load ( $this->controller_name );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 模块列表
	 */
	function index() {
		$data ['title'] = $this->check_power ( lang ( 'mode_list' ) );
        $this->load->helper('my_text');
        $rows = 20;
        $page = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['datas'] = $this->mode_model->get_all_page($this->model_name,$rows,$page);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . $this->controller_name. '?show';
        $config['total_rows'] = $this -> mode_model -> get_count_num($this->model_name);
        $config['per_page']			= $rows;
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();
		$this->load->view ( $this->config->item ( 'admin_folder' ) . 'modes', $data );
	}
	
	// ------------------------------------------------------------------------
	
	/*
	 * 模块表单
	 */
	function form($id = false) {
		$this->check_power ( lang ( 'mode_add_title' ) );
		$this->load->helper ( 'form' );
		$this->load->library ( 'form_validation' );


		$data ['id'] = '';
		$data ['name'] = '';
		$data ['rank'] = '';
		$data ['remark'] = '';
		$data ['status'] = '';
		
		if ($id) {
            $this->mode_id = $id;
			$data ['title'] = lang ( 'mode_edit_title' );
			$mode = $this->mode_model->get_one ( $this->model_name, array (
					'id' => $id 
			) );
			if (! $mode) {
				$this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$data ['id'] = $mode ['id'];
			$data ['name'] = $mode ['name'];
			$data ['rank'] = $mode ['rank'];
            $data ['remark'] = $mode ['remark'];
			$data ['status'] = $mode ['status'];
		} else {
            $data ['title'] = lang ( 'mode_add_title' );
		}

        $this->form_validation->set_rules ( 'name', 'lang:mode_name', 'trim|required|alpha_chinese_dash_bias|max_length[20]|callback_check_name['.$id.']');
		$this->form_validation->set_rules ( 'rank', 'lang:mode_rank', 'trim|integer' );
        $this->form_validation->set_rules ( 'remark', 'lang:mode_remark', 'trim|max_length[200]' );
		$this->form_validation->set_rules ( 'status', 'lang:mode_status', 'trim|integer|max_length[2]' );
		
		if ($this->form_validation->run () == FALSE) {
            if(!$id){
                $data ['name'] = $this->input->post ( 'name' );
            }
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'mode_form', $data );
		} else {
			$save ['name'] = $this->input->post ( 'name' );
            $save ['remark'] = $this->input->post ( 'remark' );

			if ($id) {
				$save ['status'] = $this->input->post ( 'status' );
                $action = $this->mode_model->update ( $this->model_name, $save, array (
						'id' => $id 
				) );
                $change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->mode_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_update_data_table_span' ) .'('.$this->model_name.'),'. lang ( 'loggin_manager_update_data_span' ) . $change_str );
				unset ( $change_str );
			} else {
                $rank = $this->db->select_max ( 'rank' )->from ( $this -> db -> dbprefix($this->model_name) )->get ()->row_array ();
                $save ['rank'] = $rank ['rank'] + 1;
                $save ['status']=1;
                $action = $this->mode_model->insert ( $this->model_name, $save );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->mode_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_add_data_table_span' ) .'('.$this->model_name.'),' . lang ( 'loggin_manager_add_data_span' ) . $change_str );
				unset ( $change_str );
			}
			
			if ($action) {
				$this->session->set_flashdata ( 'message', lang ( 'save_success' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			
			$this->session->set_flashdata ( 'error', lang ( 'save_fail' ) );
			redirect ( $this->config->item ( 'admin_folder' ) . 'mode/form/' . $id, $data );
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 检查名称
	 */
	function check_name($str,$id=0) {
        $name = $this->mode_model->check_table_field ( $this->model_name,'name',$str,$id);
		if ($name) {
			$this->form_validation->set_message ( 'check_name', lang ( 'error_mode_name_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// ------------------------------------------------------------------------
	/**
	 * 删除模块
	 */
	public function delete($id) {
		$data ['title'] = $this->check_power ( lang ( 'mode_list' ) );
		if ($this->input->is_ajax_request ()) {
			if ($id = 1) {
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$del_data = $this->mode_model->get_one ( $this->model_name, array (
					'id' => $id 
            ) );
			if ($del_data) {
				if ($del_data && ! $this->mode_model->get_all ( 'slide', array (
						'mode_id' => $del_data ['id']
				) ) && $this->mode_model->delete ( $this->model_name, array (
						'id' => $del_data ['id'] 
				) )) {
					$this->session->set_flashdata ( 'message', lang ( 'delete_success' ) );
					$change_str = '';
					foreach ( $del_data as $str ) {
						$change_str = $change_str . '[' . $str . ']';
					}
					$this->mode_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) .'('.$this->model_name.'),'  . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
	 * 删除模块 用于ajax
	 */
	public function del() {
		$data ['title'] = $this->check_power ( lang ( 'mode_list' ) );
		if ($this->input->is_ajax_request ()) {
			$del_data = $this->mode_model->get_one ( $this->model_name, array (
					'id' => $this->uri->segment ( 3 ) 
			) );
            if($del_data){
			if ($del_data && ! $this->mode_model->get_all ( 'slide', array (
					'mode_id' => $del_data ['id']
			) ) && $this->mode_model->delete ( $this->model_name, array (
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
				$this->mode_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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

// ------------------------------------------------------------------------

/* End of file mode.php */
/* Location: ./app/admin/controllers/mode.php */
