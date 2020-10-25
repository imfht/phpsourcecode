<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 *  后台管理主界面
 *
 * @category    Controllers
 * @author		　二　阳°(QQ:707069100)
 * @link        http://weibo.com/513778937?topnav=1&wvr=5
 *
 */

class Main extends Admin_Controller {

	/**
	 * $manager
	 * 保存当前登录管理员的信息
	 * @access  public
	 **/
	public $manager = NULL;
	// ------------------------------------------------------------------------

	/**
	 * 构造函数
	 *
	 * @access  public
	 * @return  void
	 */
	function __construct() {
		parent::__construct();
		$this -> load -> helper(array('date'));
	}

	// ------------------------------------------------------------------------

	/**
	 * 后台主界面
	 */
	function index() {
		$data['nav'] = $this -> check_power(lang('set_system'));
		//log_message('info', lang('admin_name') . $this -> _manager -> username . lang('loggin_success'));
        //$this->output->cache(60);//网页缓存 单位：分钟。
		$this -> load -> view($this -> config -> item('admin_folder') .'main', $data);

	}

	// ------------------------------------------------------------------------

}


// ------------------------------------------------------------------------

/* End of file main.php */
/* Location: ./app/admin/controllers/main.php */
