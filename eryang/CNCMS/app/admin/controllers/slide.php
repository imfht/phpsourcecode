<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后端首页幻灯片管理控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Slide extends Admin_Controller {

    var $slide_id = false;

    var $model_name='slide';

    var $controller_name='slide';

	function __construct() {
        parent::__construct ();
		$this->load->model ( 'slide_model' );
		$this->lang->load ( $this->controller_name );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 首页幻灯片列表
	 */
	function index() {
		$data ['title'] = $this->check_power ( lang ( 'slide_list' ) );
        $this->load->helper('my_text');
        $rows=20;
        $page = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['datas'] = $this->slide_model->get_slides($rows,$page);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . $this->controller_name. '?show';
        $config['total_rows'] = $this -> slide_model -> get_count_num($this->model_name);
        $config['per_page']			= $rows;
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();
		$this->load->view ( $this->config->item ( 'admin_folder' ) . 'slides', $data );
	}
	
	// ------------------------------------------------------------------------
	
	/*
	 * 首页幻灯片表单
	 */
	function form($id = false) {
		$this->check_power ( lang ( 'slide_add_title' ) );
		$this->load->helper ( 'form' );
		$this->load->library ( 'form_validation' );

        // 获取子级类别下拉菜单数据
        $this->load->model ( 'mode_model' );
        $data ['modes'] = $this->mode_model->get_mode_data ( lang ( 'slide_name' ) );
		$data ['id'] = '';
		$data ['slide_title'] = '';
		$data ['slide_url'] = '';
        $data ['slide_thumb'] = '';
        $data ['slide_rank'] = '';
        $data ['slide_remark'] = '';
		$data ['status'] = '';
        $data ['mode_id'] = '';

		
		if ($id) {
            $this->slide_id = $id;
			$data ['title'] = lang ( 'slide_edit_title' );
			$slide = $this->slide_model->get_one ( $this->model_name, array (
					'id' => $id 
			) );
            if (!$slide) {
				$this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$data ['id'] = $slide ['id'];
			$data ['slide_title'] = $slide ['title'];
            $data ['slide_url'] = $slide ['url'];
            $data ['slide_thumb'] = $slide ['thumb'];
            $data ['slide_rank'] = $slide ['rank'];
            $data ['slide_remark'] = $slide ['remark'];
			$data ['mode_id'] = $slide ['mode_id'];
			$data ['status'] = $slide ['status'];
		} else {
            $data ['title'] = lang ( 'slide_add_title' );
		}

        $save ['mode_id'] = $this->input->post ( 'mode_id' );

        $this->form_validation->set_rules ( 'title', 'lang:slide_title', 'trim|required|alpha_chinese_dash_bias|max_length[50]|callback_check_title[]');
		$this->form_validation->set_rules ( 'url', 'lang:slide_url', 'trim|prep_url|max_length[50]');
        $this->form_validation->set_rules ( 'thumb', 'lang:slide_thumb', 'trim|max_length[100]');
        $this->form_validation->set_rules ( 'rank', 'lang:slide_rank', 'trim|integer|max_length[6]' );
        $this->form_validation->set_rules ( 'remark', 'lang:slide_remark', 'trim|max_length[200]' );
		$this->form_validation->set_rules ( 'status', 'lang:slide_status', 'trim|integer|max_length[2]' );
        $this->form_validation->set_rules ( 'mode_id', 'lang:slide_mode', 'trim|required|integer' );
		
        if ($this->form_validation->run () == FALSE) {
            if(!$id){
                $data ['slide_title'] = $this->input->post ( 'title' );
                $data ['slide_url'] = $this->input->post ( 'url' );
                $data ['slide_thumb'] = $this->input->post ( 'thumb' );
                $data ['slide_rank'] = $this->input->post ( 'rank' );
                $data ['slide_remark'] = $this->input->post ( 'remark' );
                $data ['mode_id'] = $this->input->post ( 'mode_id' );
            }
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'slide_form', $data );
		} else {
			$save ['title'] = $this->input->post ( 'title' );
            $save ['rank'] = $this->input->post ( 'rank' );
            $save ['url'] = $this->input->post ( 'url' );
            $save ['remark'] = $this->input->post ( 'remark' );
            $save ['mode_id'] = $this->input->post ( 'mode_id' );
            $save ['thumb'] = $this->input->post ( 'thumb' );

			if ($id) {
				$save ['status'] = $this->input->post ( 'status' );
                $save ['updatetime']=now();
                $save ['updateip']=$this->input->ip_address();
                $action = $this->slide_model->update ( $this->model_name, $save, array (
						'id' => $id 
				) );
                $change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->slide_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_update_data_table_span' ) . '('.$this->model_name.'),' . lang ( 'loggin_manager_update_data_span' ) . $change_str );
				unset ( $change_str );
			} else {
                $save ['status'] = 1;
                $save ['addtime']=now();
                $save ['addip']=$this->input->ip_address();
                $action = $this->slide_model->insert ( $this->model_name, $save );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->slide_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_add_data_table_span' ) . '('.$this->model_name.'),' . lang ( 'loggin_manager_add_data_span' ) . $change_str );
				unset ( $change_str );
			}
			
			if ($action) {
				$this->session->set_flashdata ( 'message', lang ( 'save_success' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			
			$this->session->set_flashdata ( 'error', lang ( 'save_fail' ) );
			redirect ( $this->config->item ( 'admin_folder' ) . 'slide/form/' . $id, $data );
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 检查名称
	 */
	function check_title($str) {
		$title = $this->slide_model->check_table_field ( $this->model_name,'title',$str, $this->slide_id );
		
		if ($title) {
			$this->form_validation->set_message ( 'check_title', lang ( 'error_slide_title_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// ------------------------------------------------------------------------
	/**
	 * 删除首页幻灯片
	 */
	public function delete($id) {
		$data ['title'] = $this->check_power ( lang ( 'slide_list' ) );
		if ($this->input->is_ajax_request ()) {
			if ($id = 1) {
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$del_data = $this->slide_model->get_one ( $this->model_name, array (
					'id' => $id 
            ) );
			if ($del_data) {
				if ($del_data && $this->slide_model->update ( $this->model_name,array('status'=>-1), array (
                        'id' => $del_data ['id']
                    ) )) {
					$this->session->set_flashdata ( 'message', lang ( 'delete_success' ) );
					$change_str = '';
					foreach ( $del_data as $str ) {
						$change_str = $change_str . '[' . $str . ']';
					}
					$this->slide_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) .  '('.$this->model_name.'),' . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
	 * 删除首页幻灯片 用于ajax
	 */
	public function del() {
		$data ['title'] = $this->check_power ( lang ( 'slide_list' ) );
		if ($this->input->is_ajax_request ()) {
			$del_data = $this->slide_model->get_one ( $this->model_name, array (
					'id' => $this->uri->segment ( 3 ) 
			) );
            if($del_data){
			if ($del_data && $this->slide_model->update ($this->model_name,array('status'=>-1), array (
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
				$this->slide_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) .  '('.$this->model_name.'),' . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
        $data ['title'] = $this->check_power ( lang ( 'slide_recycle_bin' ) );
        $this->load->helper('my_text');
        $offset = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['datas'] = $this->slide_model->get_slides(-1,20,$offset);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . $this->controller_name. '?show';
        $config['total_rows'] = $this -> slide_model -> get_count_num($this->model_name);
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();
        $this->load->view ( $this->config->item ( 'admin_folder' ) . 'slides_recycle_bin', $data );
    }

    // ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------

/* End of file slide.php */
/* Location: ./app/admin/controllers/slide.php */
