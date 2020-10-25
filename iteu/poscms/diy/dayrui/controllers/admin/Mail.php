<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


	
class Mail extends M_Controller {

    private $cache_file;

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->template->assign('menu', $this->get_menu_v3(array(
		    fc_lang('邮件系统') => array('admin/mail/index', 'envelope'),
		    fc_lang('添加SMTP') => array('admin/mail/add_js', 'plus'),
		    fc_lang('发送邮件') => array('admin/mail/send', 'send'),
		    fc_lang('错误日志') => array('admin/mail/log', 'calendar'),
		)));
        // 缓存文件名称
        $this->cache_file = md5('sendmail'.$this->uid.$this->input->ip_address().$this->input->user_agent());
    }
	
	/**
     * 管理
     */
    public function index() {
	
		if (IS_POST) {
			$ids = $this->input->post('ids');
			if (!$ids) {
                exit(dr_json(0, fc_lang('您还没有选择呢')));
            }
            if ($this->input->post('action') == 'del') {
                // 删除邮件配置
                if (!$this->is_auth('admin/mail/del')) {
                    exit(dr_json(0, fc_lang('您无权限操作')));
                }
                $this->db->where_in('id', $ids)->delete('mail_smtp');
                $this->system_log('删除邮件服务器【#'.@implode(',', $ids).'】'); // 记录日志
            } else {
                // 更新排序号
                if (!$this->is_auth('admin/mail/edit')) {
                    exit(dr_json(0, fc_lang('您无权限操作')));
                }
                $data = $this->input->post('data');
                foreach ($ids as $id) {
                    $this->db->where('id', $id)->update('mail_smtp', array('displayorder' => (int)$data[$id]));
                }
                $this->system_log('排序邮件服务器【#'.@implode(',', $ids).'】'); // 记录日志
            }

			$this->cache(1);
			exit(dr_json(1, fc_lang('操作成功')));
		}
		
		$this->template->assign(array(
			'list' => $this->db->order_by('displayorder asc')->get('mail_smtp')->result_array(),
		));
		$this->template->display('mail_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		if (IS_POST) {
			$data = $this->input->post('data');
			$data['port'] = (int)$data['port'];
			$data['displayorder'] = 0;
			$this->db->insert('mail_smtp', $data);
            $this->system_log('添加邮件服务器【#'.$this->db->insert_id().'】'.$data['host']); // 记录日志
			$this->cache(1);
			exit(dr_json(1, fc_lang('操作成功'), ''));
		}
		
		$this->template->display('mail_add.html');
    }

	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->limit(1)->get('mail_smtp')->row_array();
		if (!$data) {
            exit(fc_lang('对不起，数据被删除或者查询不存在'));
        }
		
		if (IS_POST) {
			$data = $this->input->post('data');
			$data['port'] = (int)$data['port'];
			if ($data['pass'] == '******') {
                unset($data['pass']);
            }
			$this->db->where('id', $id)->update('mail_smtp', $data);
            $this->system_log('修改邮件服务器【#'.$id.'】'.$data['host']); // 记录日志
			$this->cache(1);
			exit(dr_json(1, fc_lang('操作成功'), ''));
		}
		
		$this->template->assign(array(
			'data' => $data,
        ));
		$this->template->display('mail_add.html');
    }
	
	/**
     * 发送
     */
    public function send() {
	
		$this->template->display('mail_send.html');
    }
	
	/**
     * 发送请求
     */
    public function ajaxsend() {

        $i = $j = 0;
		$all = $this->input->post('is_all');
		$data = $this->input->post('data');
		$mail = $data['mail'];
        $this->system_log('发送系统邮件'); // 记录日志

        switch ($all) {

            case 1:
                if ($data['mails']) {
                    $mail = str_replace(array(PHP_EOL, chr(13), chr(10)), ',', $data['mails']);
                    $mail = str_replace(',,', ',', $mail);
                    $mail = trim($mail, ',');
                }
                if (!$mail) {
                    exit(dr_json(0, fc_lang('邮件地址未填写')));
                }
                $mail = @explode(',', $mail);
                if (!$data['title'] || !$data['message']) {
                    exit(dr_json(0, fc_lang('邮件标题或者发送内容未填写')));
                }
                foreach ($mail as $tomail) {
                    if ($this->member_model->sendmail($tomail, $data['title'], $data['message'])) {
                        $i ++;
                    } else {
                        $j ++;
                    }
                }
                exit(dr_json(1, fc_lang('发送成功%s封，失败%s封', $i, $j)));
                break;

            case 2:
                if (!$data['title'] || !$data['message']) {
                    $this->admin_msg(fc_lang('邮件标题或者发送内容未填写'), dr_url('mail/send'));
                }
                $data['total'] = $data['groupid'] ?
                    $this->db->where('groupid', $data['groupid'])->count_all_results('member') :
                    $this->db->count_all_results('member');
                if (!$data['total']) {
                    $this->admin_msg(fc_lang('指定会员组中没有可发布对象'), dr_url('mail/send'));
                }
                // 保存缓存文件
                $this->cache->file->save($this->cache_file, $data, 36000);
                $this->admin_msg(fc_lang('需要发送给%s个会员，请等待（%s）', $data['total'], '...'), dr_url('mail/member'), 2);
                break;

            default:
                if (!$data['mail']) {
                    exit(dr_json(0, fc_lang('邮件地址未填写')));
                }
                if (!$data['title'] || !$data['message']) {
                    exit(dr_json(0, fc_lang('邮件标题或者发送内容未填写')));
                }
                if ($this->member_model->sendmail($data['mail'], $data['title'], $data['message'])) {
                    $i ++;
                } else {
                    $j ++;
                }
                exit(dr_json(1, fc_lang('发送成功%s封，失败%s封', $i, $j)));
                break;
        }
    }

    /**
     * 发送给会员
     */
    public function member() {

        $data = $this->cache->file->get($this->cache_file);
        if (!$data) {
            $this->admin_msg(fc_lang('缓存不存在，请重新开始'));
        }

        $page = max((int)$this->input->get('page'), 1);
        $psize = 5;
        $tpage = ceil($data['total']/$psize);
        if ($data['groupid']) {
            $this->db->where('groupid', $data['groupid']);
        }
        $member = $this->db
                       ->select('email')
                       ->order_by('uid desc')
                       ->limit($psize, $psize * ($page - 1))
                       ->get('member')
                       ->result_array();
        if ($member) {
            foreach ($member as $t) {
                $this->member_model->sendmail($t['email'], $data['title'], $data['message']);
            }
            $this->admin_msg(
                fc_lang('需要发送给%s个会员，请等待（%s）', $data['total'], $tpage.'/'.$page),
                dr_url('mail/member', array('page' => $page + 1)),
                2,1
            );
        } else {
            $this->cache->file->delete($this->cache_file);
            $this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('mail/send'), 1);
        }

    }
	
	/**
     * 日志
     */
    public function log() {
	
		if (IS_POST) {
			@unlink(WEBPATH.'cache/mail_error.log');
			exit(dr_json(1, fc_lang('操作成功，正在刷新...')));
		}
		
		$data = $list = array();
		$file = @file_get_contents(WEBPATH.'cache/mail_error.log');
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
			'pages'	=> $this->get_pagination(dr_url('mail/log'), $total)
		));
		$this->template->display('mail_log.html');
    }
	
	/**
     * test
     */
    public function test() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db
					 ->where('id', $id)
					 ->limit(1)
					 ->get('mail_smtp')
					 ->row_array();
		if (!$data) {
            exit(fc_lang('对不起，数据被删除或者查询不存在'));
        }
		
		$this->load->library('Dmail');
		$this->dmail->set(array(
			'host' => $data['host'],
			'user' => $data['user'],
			'pass' => $data['pass'],
			'port' => $data['port'],
			'from' => $data['user']
		));
		
		if ($this->dmail->send(SYS_EMAIL, 'test', 'test for '.SITE_NAME)) {
			echo 'ok';
		} else {
			echo 'Error: '.$this->dmail->error();
		}
	}
    
    /**
     * 缓存
     */
    public function cache($update = 0) {
	    $this->system_model->email();
		((int)$_GET['admin'] || $update) or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
}