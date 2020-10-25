<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Application extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('应用管理') => array('admin/application/index', 'cloud'),
            fc_lang('更新缓存') => array('admin/home/cache', 'refresh'),
		)));
		$this->load->model('application_model');
    }
	
	/**
     * 管理
     */
    public function index() {
	
		$store = $data = array();
		$local = dr_dir_map(FCPATH.'app/', 1); // 搜索本地应用
		$application = $this->application_model->get_data(); // 库中已安装应用
		
		if ($local) {
			foreach ($local as $dir) {
				if (is_file(FCPATH.'app/'.$dir.'/config/app.php')) {
					if (isset($application[$dir])) {
						$config = $data[1][$dir] = array_merge($application[$dir], require FCPATH.'app/'.$dir.'/config/app.php');
						$config['key'] && (
							isset($store[$config['key']])
								? (version_compare($config['version'], $store[$config['key']], '<') && $store[$config['key']] = $config['version'])
								: $store[$config['key']] = $config['version']
						);
					} else {
						$data[0][$dir] = require FCPATH.'app/'.$dir.'/config/app.php';
					}
				}
			}
		}
		
		$this->template->assign(array(
			'list' => $data,
			'store' => dr_base64_encode(dr_array2string($store)),
		));
		$this->template->display('application_index.html');
    }
    
	/**
     * 禁用/可用
     */
    public function disabled() {
	
		if ($this->is_auth('admin/application/config')) {
			$id = (int)$this->input->get('id');
			$data = $this->db->where('id', $id)->get('application')->row_array();
            $value = $data['disabled'] == 1 ? 0 : 1;
			$this->db->where('id', $id)->update('application', array(
                'disabled' => $value
            ));
            $this->clear_cache('app');
            $this->system_log(($value ? '禁用' : '启用').'应用【'.$data['dirname'].'】'); // 记录日志
		}
		
		exit(dr_json(1, fc_lang('操作成功')));
    }
	
	/**
     * 删除
     */
    public function delete() {

		$this->admin['adminid'] != 1 && $this->admin_msg(fc_lang('您无权限操作(%s)', 'delete'));

		$dir = $this->input->get('dir');

        $this->load->helper('file');
		delete_files(FCPATH.'app/'.$dir.'/', TRUE);

		is_dir(FCPATH.'app/'.$dir.'/') && @rmdir(FCPATH.'app/'.$dir.'/');

		is_dir(FCPATH.'app/'.$dir.'/') && $this->admin_msg(fc_lang('无文件删除权限，建议通过FTP等工具删除此目录'));

        // 删除菜单
        $this->db->where('mark', 'app-'.$dir)->delete('admin_menu');
        $this->db->where('mark', 'app-'.$dir)->delete('member_menu');

        $this->clear_cache('app');
        $this->system_log('删除应用【'.$dir.'】'); // 记录日志

		$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('application/index'), 1);
    }
	


    /**
     * 缓存
     */
    public function cache() {
        $this->application_model->cache();
        (int)$_GET['admin'] or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
    }
}