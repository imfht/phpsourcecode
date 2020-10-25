<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后端系统日志管理控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Manager_log extends Admin_Controller {

    var $model_name='manager_logging';

    var $controller_name='manager_log';

	function __construct() {
        parent::__construct ();
		$this->load->model ( 'manager_log_model' );
		$this->lang->load ( $this->controller_name );
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 类别列表
	 */
	function index() {
        $data ['title'] = $this->check_power ( lang ( 'manager_log_list' ) );
        $this->load->helper('my_text');
        $rows = 20;
        $page = $this -> input -> get('page', TRUE) ? $this -> input -> get('page', TRUE) : 0;
        $data ['manager_log_datas'] = $this->manager_log_model->get_manager_logs($rows,$page);
        // 加载分页
        $this -> load -> library('mypagination');
        $config['base_url'] = $this->config->item ( 'admin_folder' ) . 'manager_log'. '?show';
        $config['total_rows'] = $this -> manager_log_model -> get_count_num($this->model_name);
        $config['per_page']			= $rows;
        $this -> mypagination -> init($config);
        $data['pagination'] = $this -> mypagination -> create_links();
		$this->load->view ( $this->config->item ( 'admin_folder' ) . 'manager_logs', $data );
	}
	
	// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------

/* End of file manager_log.php */
/* Location: ./app/admin/controllers/manager_log.php */
