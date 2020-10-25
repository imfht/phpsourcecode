<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * 登录验证模型
 *
 * @category    Models
 * @author      二　阳°(QQ:707069100)
 * @link        http://weibo.com/513778937?topnav=1&wvr=5
 */
class Check_log_model extends CI_Model {

    var $model_table='check_log';

	/**
	 * 构造函数
	 *
	 * @access  public
	 * @return  void
	 */
	function __construct() {
		parent::__construct();
	}

	// ------------------------------------------------------------------------

	/**
	 * 获取一个ip在15分钟内的登录验证记录
	 *
	 * @access  public
	 * @param   array
	 * @return  bool
	 */
	function get_logs_by_ip($ip = '') {
		return $this -> db -> where('ip_address', $ip) -> where('add_time >= ', now() - 60 * 15) -> count_all_results($this -> db -> dbprefix($this->model_table));
	}

	// ------------------------------------------------------------------------

}

/* End of file check_log_model.php */
/* Location: ./app/admin/models/check_log_model.php */
