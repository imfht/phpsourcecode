<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后端文章管理控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Article extends Admin_Controller {

    var $article_id = false;

    var $model_name='article';

    var $controller_name='article';

	function __construct() {
        parent::__construct ();
		$this->load->model ( 'article_model' );
		$this->lang->load ( $this->controller_name );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 文章列表
	 */
	function index() {
		$data ['title'] = $this->check_power ( lang ( 'article_list' ) );
        $this->load->helper('my_text');
        $rows = 20;
        $page = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['datas'] = $this->article_model->get_articles(1,$rows,$page);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . $this->controller_name. '?show';
        $config['total_rows'] = $this -> article_model -> get_count_num($this->model_name);
        $config['per_page']			= $rows;
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();
		$this->load->view ( $this->config->item ( 'admin_folder' ) . 'articles', $data );
	}
	
	// ------------------------------------------------------------------------
	
	/*
	 * 文章表单
	 */
	function form($id = false) {
		$this->check_power ( lang ( 'article_add_title' ) );
		$this->load->helper ( 'form' );
		$this->load->library ( 'form_validation' );

        // 获取子级类别下拉菜单数据
        $this->load->model ( 'category_model' );
        $data ['categorys'] = $this->category_model->get_children_data ();
		$data ['id'] = '';
		$data ['article_title'] = '';
		$data ['article_content'] = '';
		$data ['status'] = '';
        $data ['categoryid'] = '';

		
		if ($id) {
            $this->article_id = $id;
			$data ['title'] = lang ( 'article_edit_title' );
			$article = $this->article_model->get_one ( $this->model_name, array (
					'id' => $id 
			) );
            if (!$article) {
				$this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$data ['id'] = $article ['id'];
			$data ['article_title'] = $article ['title'];
            $data ['article_content'] = $article ['content'];
			$data ['categoryid'] = $article ['categoryid'];
			$data ['status'] = $article ['status'];
		} else {
            $data ['title'] = lang ( 'article_add_title' );
		}

        $save ['categoryid'] = $this->input->post ( 'categoryid' );

        $this->form_validation->set_rules ( 'article_title', 'lang:article_title', 'trim|required|alpha_chinese_dash_bias|max_length[50]|callback_check_title['.$save ['categoryid'].']');
		$this->form_validation->set_rules ( 'article_content', 'lang:article_content', 'trim|required|min_length[10]');
		$this->form_validation->set_rules ( 'status', 'lang:article_status', 'trim|integer|max_length[2]' );
        $this->form_validation->set_rules ( 'categoryid', 'lang:article_category', 'trim|required|integer' );
		
        if ($this->form_validation->run () == FALSE) {
            if(!$id){
                $data ['article_title'] = $this->input->post ( 'article_title' );
                $data ['article_content'] = $this->input->post ( 'article_content' );
                $data ['categoryid'] = $this->input->post ( 'categoryid' );
            }
			$this->load->view ( $this->config->item ( 'admin_folder' ) . 'article_form', $data );
		} else {
			$save ['title'] = $this->input->post ( 'article_title' );
            $save ['content'] = $this->input->post ( 'article_content' );
            $save ['writer'] =$this -> _manager->username;

			if ($id) {
				$save ['status'] = $this->input->post ( 'status' );
                $save ['updatetime']=now();
                $save ['updateip']=$this->input->ip_address();

                $action = $this->article_model->update ( $this->model_name, $save, array (
						'id' => $id 
				) );
                $change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->article_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_update_data_table_span' ) . '('.$this->model_name.'),' . lang ( 'loggin_manager_update_data_span' ) . $change_str );
				unset ( $change_str );
			} else {
                $save ['status'] = 1;
                $save ['addtime']=now();
                $save ['addip']=$this->input->ip_address();
                $action = $this->article_model->insert ( $this->model_name, $save );
				$change_str = '';
				foreach ( $save as $str ) {
					$change_str = $change_str . '[' . $str . ']';
				}
				$this->article_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_add_data_table_span' ) . '('.$this->model_name.'),' . lang ( 'loggin_manager_add_data_span' ) . $change_str );
				unset ( $change_str );
			}
			
			if ($action) {
				$this->session->set_flashdata ( 'message', lang ( 'save_success' ) );
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			
			$this->session->set_flashdata ( 'error', lang ( 'save_fail' ) );
			redirect ( $this->config->item ( 'admin_folder' ) . 'article/form/' . $id, $data );
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 检查名称
	 */
	function check_title($str) {
		$title = $this->article_model->check_table_field ( $this->model_name,'title',$str, $this->article_id );
		
		if ($title) {
			$this->form_validation->set_message ( 'check_title', lang ( 'error_article_title_taken' ) );
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// ------------------------------------------------------------------------
	/**
	 * 删除文章
	 */
	public function delete($id) {
		$data ['title'] = $this->check_power ( lang ( 'article_list' ) );
		if ($this->input->is_ajax_request ()) {
			if ($id = 1) {
				redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
			}
			$del_data = $this->article_model->get_one ( $this->model_name, array (
					'id' => $id 
            ) );
			if ($del_data) {
				if ($del_data && $this->article_model->update ( $this->model_name,array('status'=>-1), array (
                        'id' => $del_data ['id']
                    ) )) {
					$this->session->set_flashdata ( 'message', lang ( 'delete_success' ) );
					$change_str = '';
					foreach ( $del_data as $str ) {
						$change_str = $change_str . '[' . $str . ']';
					}
					$this->article_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) .  '('.$this->model_name.'),' . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
	 * 删除文章 用于ajax
	 */
	public function del() {
		$data ['title'] = $this->check_power ( lang ( 'article_list' ) );
		if ($this->input->is_ajax_request ()) {
			$del_data = $this->article_model->get_one ( $this->model_name, array (
					'id' => $this->uri->segment ( 3 ) 
			) );
            if($del_data){
			if ($del_data && $this->article_model->update ($this->model_name,array('status'=>-1), array (
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
				$this->article_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) .  '('.$this->model_name.'),' . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
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
        $data ['title'] = $this->check_power ( lang ( 'article_recycle_bin' ) );
        $this->load->helper('my_text');
        $rows = 20;
        $page = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['datas'] = $this->article_model->get_articles(-1,$rows,$page);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . $this->controller_name. '?show';
        $config['total_rows'] = $this -> article_model -> get_count_num($this->model_name);
        $config['per_page']			= $rows;
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();
        $this->load->view ( $this->config->item ( 'admin_folder' ) . 'articles_recycle_bin', $data );
    }

    // ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------

/* End of file article.php */
/* Location: ./app/admin/controllers/article.php */
