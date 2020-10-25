<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 后台默认界面
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 *      
 */
class Default_view extends Admin_Controller {
	
	/**
	 * 构造函数
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		parent::__construct ();
	}
	
	// ------------------------------------------------------------------------
	
	/**
	 * 后台主界面
	 */
	function index() {
		redirect($this -> config -> item('admin_folder') . 'main');
	}
	
	// ------------------------------------------------------------------------
}

/* End of file default_view.php */
/* Location: ./app/admin/controllers/default_view.php */
