<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Sms extends M_Controller {

	private $service = 'http://sms.dayrui.com/index.php';

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('账号设置') => array('admin/sms/index', 'envelope'),
		    fc_lang('发送短信') => array('admin/sms/send', 'send'),
		    fc_lang('发送日志') => array('admin/sms/log', 'calendar'),
		)));
    }
	
	/**
     * 账号
     */
    public function index() {
	
		$file = WEBPATH.'config/sms.php';
		
		if (IS_POST) {
		
			$data = $this->input->post('data');
			if (strlen($data['note']) > 30 ) {
                $this->admin_msg(fc_lang('短信签名超出了范围'));
            }
			if ($_POST['aa'] == 0) {
                unset($data['third']);
            }
			
			$this->load->library('dconfig');
			$size = $this->dconfig
						 ->file($file)
						 ->note('短信配置文件')
						 ->space(8)
						 ->to_require_one($data);
			if (!$size) {
                $this->admin_msg(fc_lang('网站域名文件创建失败，请检查config目录权限'));
            }
            $this->system_log('配置短信接口'); // 记录日志
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('sms/index'), 1);
		}
		
		$data = is_file($file) ? require $file : array();
		$this->template->assign(array(
			'data' => $data,
			'service' => $this->service,
		));
		$this->template->display('sms_index.html');
    }
	
	/**
     * 发送
     */
    public function send() {
	
		$file = WEBPATH.'config/sms.php';
		if (!is_file($file)) {
            $this->admin_msg(fc_lang('您还没有配置短信账号呢'));
        }
		
		$this->template->display('sms_send.html');
    }
	
	/**
     * 发送
     */
    public function ajaxsend() {
	
		$file = WEBPATH.'config/sms.php';
		if (!is_file($file)) {
            exit(dr_json(0, fc_lang('您还没有配置短信账号呢')));
        }
		
		$data = $this->input->post('data', true);
		if (strlen($data['content']) > 150) {
            exit(dr_json(0, fc_lang('短信内容过长，不得超过70个汉字')));
        }
		
		$mobile = $data['mobile'];
		if ($data['mobiles'] && !$data['mobile']) {
			$mobile = str_replace(array(PHP_EOL, chr(13), chr(10)), ',', $data['mobiles']);
			$mobile = str_replace(',,', ',', $mobile);
			$mobile = trim($mobile, ',');
		}
		if (substr_count($mobile, ',') > 40) {
            exit(dr_json(0, fc_lang('群发一次不得超过40个，数量过多时请分批发送')));
        }

        $this->system_log('发送系统短信'); // 记录日志
		$result = $this->member_model->sendsms($mobile, $data['content']);
		if ($result === FALSE) {
			 exit(dr_json(0, '#0'.fc_lang('发送失败')));
		} else {
			 exit(dr_json($result['status'], $result['msg']));
		}
    }
	
	/**
     * 日志
     */
    public function log() {
	
		if (IS_POST) {
			@unlink(WEBPATH.'cache/sms_error.log');
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
		
		$data = $list = array();
		$file = @file_get_contents(WEBPATH.'cache/sms_error.log');
		if ($file) {
			$data = explode(PHP_EOL, $file);
			$data = $data ? array_reverse($data) : array();
			unset($data[0]);
			$page = max(1, (int)$this->input->get('page'));
			$limit = ($page - 1) * SITE_ADMIN_PAGESIZE;
			$i = $j = 0;
			foreach ($data as $v) {
				if ($i >= $limit && $j < SITE_ADMIN_PAGESIZE) {
					$list[] = $v;
					$j ++;
				}
				$i ++;
			}
		}
		
		$total = count($data);
		$this->template->assign(array(
			'list' => $list,
			'total' => $total,
			'pages'	=> $this->get_pagination(dr_url('sms/log'), $total)
		));
		$this->template->display('sms_log.html');
    }
	
}