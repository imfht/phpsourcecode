<?php

/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/05 0015
 * Time: 上午 10:23
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: 后台主页控制器
 * ==========================================
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Pkadmin_Controller {

	public function __construct() {
		parent::__construct();
		$this -> load -> model('login_model', 'admin');
		$this -> load -> library('pagination');
	}

	/**
	 * Pkadmin 后台首页action
	 * @param int $offset 偏移量，用于分页
	 */
	public function index($offset = '') {
		$data = $this -> data;		
		//删除三十天前的日志记录
		$t = time() - 3600 * 24 * 30;
		$this -> admin -> del_month_ago_log($t);
		//配置分页信息
		$config['base_url'] = site_url('Pkadmin/Admin/index/');
		$config['total_rows'] = $this -> admin -> get_log_count($this -> ADMINISTRSTORS['admin_id']);
		$config['per_page'] = 10;
		//初始化分类页
		$this -> pagination -> initialize($config);
		//生成分页信息
		$data['pageinfo'] = $this -> pagination -> create_links();
		$data['admin_log'] = $this -> admin -> get_admin_log_list($this -> ADMINISTRSTORS['admin_id'], $config['per_page'], $offset);
		$this -> load -> view('admin.html', $data);
	}

}
