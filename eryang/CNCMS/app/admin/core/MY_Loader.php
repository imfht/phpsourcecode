<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 扩展 Loader 类
 * @author      二　阳°(QQ:707069100)
 * @link        http://weibo.com/513778937?topnav=1&wvr=5
 */
class MY_Loader extends CI_Loader {
	function __construct() {
		parent::__construct();
	}

	// ------------------------------------------------------------------------

	/**
	 * 设置后台视图路径
	 *
	 * @access   public
	 * @param    string   模板名
	 */
	function set_admin_template($template) {
		$temp = array_keys($this -> _ci_view_paths);
		$this -> _ci_view_paths = array($temp[0] . $template . '/' => TRUE);
	}

	// ------------------------------------------------------------------------

}

/* End of file MY_Loader.php */
/* Location: ./app/admin/core/MY_Loader.php */