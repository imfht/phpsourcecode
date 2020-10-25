<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */

class System extends M_Controller {
	
    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('系统配置') => array('admin/system/index', 'cog'),
				fc_lang('分离存储') => array('admin/system/file', 'cubes'),
				fc_lang('操作日志') => array('admin/system/oplog', 'calendar'),
				fc_lang('错误日志') => array('admin/system/debug', 'bug'),
			))
		));
    }

	private function _save($is_memcache = 0, $action = '') {

		$page = (int)$this->input->get('page');
		$data = require WEBPATH.'config/system.php'; // 加载网站系统配置文件
		!$data['SYS_TEMPLATE'] && $data['SYS_TEMPLATE'] = 'templates';

		if (IS_POST) {
			$this->system_model->save_config($data, $this->input->post('data'), $action);
			$this->system_log('修改系统配置'); // 记录日志
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('system/'.$this->router->method, array('page' => (int)$this->input->post('page'))), 1);
		}

		$data['SYS_ONLINE_NUM'] = isset($data['SYS_ONLINE_NUM']) ? $data['SYS_ONLINE_NUM'] : 10000;
		$data['SYS_ONLINE_TIME'] = isset($data['SYS_ONLINE_TIME']) ? $data['SYS_ONLINE_TIME'] : 7200;
		$data['SYS_UPLOAD_DIR'] = isset($data['SYS_UPLOAD_DIR']) && $data['SYS_UPLOAD_DIR'] ? $data['SYS_UPLOAD_DIR'] : 'uploadfile';
		$data['SYS_UPLOAD_DIR'] == '/member/uploadfile/' && $data['SYS_UPLOAD_DIR'] = 'member/uploadfile';
		!$data['SYS_THUMB_DIR'] && $data['SYS_THUMB_DIR'] = 'api/thumb';
		$data['SYS_CMS'] = $data['SYS_CMS'] ? $data['SYS_CMS'] : DR_NAME;
		$data['SYS_NAME'] = $data['SYS_NAME'] ? $data['SYS_NAME'] : 'FineCMS';

		$this->template->assign(array(
			'page' => $page,
			'data' => $data,
			'config' => $this->system_model->config,
			'is_upload' => is_dir(strpos($data['SYS_UPLOAD_DIR'], '/') === 0 || strpos($data['SYS_UPLOAD_DIR'], ':') !== false ? $data['SYS_UPLOAD_DIR'] : WEBPATH.$data['SYS_UPLOAD_DIR']),
		));
	}
	
    /**
     * 配置
     */
    public function index() {

		$this->_save(1);
		$this->template->display('system_index.html');
	}

    /**
     * 文件分离
     */
    public function file() {

		exit('体验版不支持');
	}
	
	/**
     * 系统操作日志
     */
    public function oplog() {

		$time = isset($_POST['data']['time']) && $_POST['data']['time'] ? (int)$_POST['data']['time'] : (int)$this->input->get('time');
        $time = $time ? $time : SYS_TIME;
        $file = WEBPATH.'cache/optionlog/'.date('Ym', $time).'/'.date('d', $time).'.log';

        $list = array();
        $data = @explode(PHP_EOL, file_get_contents($file));
        $data = @array_reverse($data);

        $page = IS_POST ? 1 : max(1, (int)$this->input->get('page'));
        $total = count($data);
        $limit = ($page - 1) * SITE_ADMIN_PAGESIZE;

        $i = $j = 0;

        foreach ($data as $v) {
            if ($v && $i >= $limit && $j < SITE_ADMIN_PAGESIZE) {
                $list[] = $v;
                $j ++;
            }
            $i ++;
        }

        $this->load->library('dip');

        $this->template->assign(array(
            'time' => $time,
            'list' => $list,
            'total' => $total,
            'pages'	=> $this->get_pagination(dr_url('system/oplog', array('time' => $time)), $total)
        ));
        $this->template->display('system_oplog.html');
	}

	/**
     * debug
     */
    public function debug() {

		$time = isset($_POST['data']['time']) && $_POST['data']['time'] ? (int)$_POST['data']['time'] : (int)$this->input->get('time');
        $time = $time ? $time : SYS_TIME;
        $total = 0;
        $file = WEBPATH.'cache/errorlog/log-'.date('Y-m-d', $time).'.php';
        if (is_file($file)) {


            $log = trim(str_replace("<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>", '', file_get_contents($file)), PHP_EOL);

            $data = @explode(PHP_EOL, str_replace(chr(10), PHP_EOL, $log));

            $data && $data = @array_reverse($data);

            $page = IS_POST ? 1 : max(1, (int)$this->input->get('page'));
            $total = count($data);
            $limit = ($page - 1) * SITE_ADMIN_PAGESIZE;

            $i = $j = 0;

            foreach ($data as $t) {
                if ($t && $i >= $limit && $j < SITE_ADMIN_PAGESIZE) {
                    $v = @explode(' --> ', $t);
                    $time2 = $v ? @explode(' - ', $v[0]) : array(1=>'');
                    $list[] = array(
                        'time' => $time2[1],
                        'error' => $v[1],
                    );
                    $j ++;
                }
                $i ++;
            }

        }

        $this->template->assign(array(
            'time' => $time,
            'list' => $list,
            'total' => $total,
            'pages'	=> $this->get_pagination(dr_url('system/debug', array('time' => $time)), $total)
        ));
        $this->template->display('system_debug.html');
	}

	/**
     * 生成安全码
     */
    public function syskey() {
		echo 'CI3'.strtoupper(substr((md5(SYS_TIME)), rand(0, 10), 13));exit;
	}

	/**
     * 生成来路随机字符
     */
    public function referer() {
		$s = strtoupper(base64_encode(md5(SYS_TIME).md5(rand(0, 2015).md5(rand(0, 2015)))).md5(rand(0, 2009)));
		echo str_replace('=', '', substr($s, 0, 64));exit;
	}
	
	/**
     * memcache 检查
     */
	public function memcache() {
	

	}

}