<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后端友情链接管理控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Link extends Admin_Controller {

    var $link_id = false;

    var $model_name='link';

    var $controller_name='link';

	function __construct() {
        parent::__construct ();
		$this->load->model ( 'link_model' );
		$this->lang->load ( $this->controller_name );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 友情链接列表
	 */
	function index() {
		$data ['title'] = $this->check_power ( lang ( 'link_list' ) );
        $this->load->helper('my_text');
        $rows=20;
        $page = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['datas'] = $this->link_model->get_links($rows,$page);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . $this->controller_name. '?show';
        $config['total_rows'] = $this -> link_model -> get_count_num($this->model_name);
        $config['per_page']			= $rows;
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();
		$this->load->view ( $this->config->item ( 'admin_folder' ) . 'links', $data );
	}
	
	// ------------------------------------------------------------------------
	
	/*
	 * 友情链接表单
	 */
	function form($id = false) {
		$this->check_power ( lang ( 'link_add_name' ) );
		$this->load->helper ( 'form' );
		$this->load->library ( 'form_validation' );

		$data ['id'] = '';
		$data ['link_name'] = '';
		$data ['link_url'] = '';
        $data ['link_thumb'] = '';
        $data ['link_rank'] = '';
		$data ['status'] = '';

		
		if ($id) {
            $this->link_id = $id;
			$data ['title'] = lang ( 'link_edit_name' );
			$link = $this->link_model->get_one ( $this->model_name, array (
					'id' => $id 
			) );
            if (!$link) {
				$this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$data ['id'] = $link ['id'];
			$data ['link_name'] = $link ['name'];
            $data ['link_url'] = $link ['url'];
            $data ['link_thumb'] = $link ['thumb'];
            $data ['link_rank'] = $link ['rank'];
			$data ['status'] = $link ['status'];
		} else {
            $data ['title'] = lang ( 'link_add_name' );
		}


        $this->form_validation->set_rules ( 'name', 'lang:link_name', 'trim|required|alpha_chinese_dash_bias|max_length[50]|callback_check_name[]');
		$this->form_validation->set_rules ( 'url', 'lang:link_url', 'trim|required|prep_url|max_length[200]');
        $this->form_validation->set_rules ( 'thumb', 'lang:link_thumb', 'trim|max_length[100]');
        $this->form_validation->set_rules ( 'rank', 'lang:link_rank', 'trim|integer|max_length[6]' );
		$this->form_validation->set_rules ( 'status', 'lang:link_status', 'trim|integer|max_length[2]' );

        if ($this->form_validation->run () == FALSE) {
            if(!$id){
                $data ['link_name'] = $this->input->post ( 'name' );
                $data ['link_url'] = $this->input->post ( 'url' );
                $data ['link_thumb'] = $this->input->post ( 'thumb' );
                $data ['link_rank'] = $this->input->post ( 'rank' );
            }
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'link_form', $data );
		} else {
			$save ['name'] = $this->input->post ( 'name' );
            $save ['rank'] = $this->input->post ( 'rank' );
            $save ['url'] = $this->input->post ( 'url' );
            $save ['thumb'] = $this->input->post ( 'thumb' );

			if ($id) {
				$save ['status'] = $this->input->post ( 'status' );
                $save ['updatetime']=now();
                $save ['updateip']=$this->input->ip_address();
                $action = $this->link_model->update ( $this->model_name, $save, array (
						'id' => $id 
				) );
                $change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->link_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_update_data_table_span' ) . '('.$this->model_name.'),' . lang ( 'loggin_manager_update_data_span' ) . $change_str );
				unset ( $change_str );
			} else {
                $save ['status'] = 1;
                $save ['addtime']=now();
                $save ['addip']=$this->input->ip_address();
                $action = $this->link_model->insert ( $this->model_name, $save );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->link_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_add_data_table_span' ) . '('.$this->model_name.'),' . lang ( 'loggin_manager_add_data_span' ) . $change_str );
				unset ( $change_str );
			}
			
			if ($action) {
				$this->session->set_flashdata ( 'message', lang ( 'save_success' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			
			$this->session->set_flashdata ( 'error', lang ( 'save_fail' ) );
			redirect ( $this->config->item ( 'admin_folder' ) . 'link/form/' . $id, $data );
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 检查名称
	 */
	function check_name($str) {
		$name = $this->link_model->check_table_field ( $this->model_name,'name',$str, $this->link_id );
		
		if ($name) {
			$this->form_validation->set_message ( 'check_name', lang ( 'error_link_name_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * 删除友情链接
	 */
	public function delete($id) {
		$data ['title'] = $this->check_power ( lang ( 'link_list' ) );
		if ($this->input->is_ajax_request ()) {
			if ($id = 1) {
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$del_data = $this->link_model->get_one ( $this->model_name, array (
					'id' => $id 
            ) );
			if ($del_data) {
				if ($del_data && $this->link_model->update ( $this->model_name,array('status'=>-1), array (
                        'id' => $del_data ['id']
                    ) )) {
					$this->session->set_flashdata ( 'message', lang ( 'delete_success' ) );
					$change_str = '';
					foreach ( $del_data as $str ) {
						$change_str = $change_str . '[' . $str . ']';
					}
					$this->link_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) .  '('.$this->model_name.'),' . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
	 * 删除友情链接  用于ajax
	 */
	public function del() {
		$data ['title'] = $this->check_power ( lang ( 'link_list' ) );
		if ($this->input->is_ajax_request ()) {
			$del_data = $this->link_model->get_one ( $this->model_name, array (
					'id' => $this->uri->segment ( 3 ) 
			) );
            if($del_data){
			if ($del_data && $this->link_model->update ($this->model_name,array('status'=>-1), array (
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
				$this->link_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) .  '('.$this->model_name.'),' . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
     * 回收站
     */
    function recycle_bin() {
        $data ['title'] = $this->check_power ( lang ( 'link_recycle_bin' ) );
        $this->load->helper('my_text');
        $offset = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['datas'] = $this->link_model->get_links(-1,20,$offset);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . $this->controller_name. '?show';
        $config['total_rows'] = $this -> link_model -> get_count_num($this->model_name);
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();
        $this->load->view ( $this->config->item ( 'admin_folder' ) . 'links_recycle_bin', $data );
    }

    // ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------

/* End of file link.php */
/* Location: ./app/admin/controllers/link.php */
