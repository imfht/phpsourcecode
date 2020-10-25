<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/* v3.1.0  */
	
class Form extends M_Controller {

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
		$this->load->model('form_model');
    }
	
	/**
     * 管理
     */
    public function index() {
		$this->template->assign(array(
			'list' => $this->form_model->link->get($this->form_model->prefix)->result_array(),
			'menu' => $this->get_menu_v3(array(
				fc_lang('表单管理') => array('admin/form/index', 'table'),
				fc_lang('添加') => array('admin/form/add', 'plus')
			)),
		));
		$this->template->display('form_index.html');
    }
	
	/**
     * 添加
     */
    public function add() {
	
		if (IS_POST) {
			$data = $this->input->post('data');
			$result = $this->form_model->add($data);
			if ($result === TRUE) {
				$this->form_model->cache();
                $this->system_log('添加网站表单【#'.$data['table'].'】'); // 记录日志
				$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('form/index'), 1);
			}
		}
		
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('表单管理') => array('admin/form/index', 'table'),
				fc_lang('添加') => array('admin/form/add', 'plus'),
				fc_lang('更新缓存') => array('admin/form/cache', 'refresh'),
			)),
			'data' => $data,
			'result' => $result,
		));
		$this->template->display('form_add.html');
    }
	
	/**
     * 修改
     */
    public function edit() {
	
		$id = (int)$this->input->get('id');
		$data = $this->db->where('id', $id)->limit(1)->get($this->form_model->prefix)->row_array();
		if (!$data) {
            $this->admin_msg(fc_lang('对不起，数据被删除或者查询不存在'));
        }
		
		if (IS_POST) {
            $this->system_log('修改网站表单【#'.$data['table'].'】'); // 记录日志
			$data = $this->input->post('data');
			$this->form_model->edit($id, $data);
			$this->form_model->cache();
			$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('form/index'), 1);
		}
		
		$data['setting'] = dr_string2array($data['setting']);
		
		$this->template->assign(array(
			'menu' => $this->get_menu_v3(array(
				fc_lang('表单管理') => array('admin/form/index', 'table'),
				fc_lang('添加') => array('admin/form/add', 'plus'),
				fc_lang('更新缓存') => array('admin/form/cache', 'refresh'),
			)),
			'data' => $data,
		));
		$this->template->display('form_add.html');
    }
	
	/**
     * 删除
     */
    public function del() {
        $id = (int)$this->input->get('id');
		$this->form_model->del($id);
        $this->system_log('删除网站表单【#'.$id.'】'); // 记录日志
		$this->admin_msg(fc_lang('操作成功，正在刷新...'), dr_url('form/index'), 1);
	}
	
	/**
     * 生成表单
     */
    public function toform() {
		
		$id = (int)$this->input->get('id');
		$data = $this->get_cache('form-'.SITE_ID, $id);
		if (!$data) {
            exit('<div style="color:red;padding:20px;">'.fc_lang('表单不存在，请更新表单缓存').'<br>&nbsp;</div>');
        }
		
		$string = '';
		$string.= '<form class="form-horizontal form-bordered" action="'.SITE_URL.'index.php?c=form_'.$data['table'].'" method="post" name="myform" id="myform">'.PHP_EOL;
		$string.= '<div class="form">'.PHP_EOL;
		$string.= $this->field_input($data['field']).PHP_EOL;
		
		if ($data['setting']['code']) {
			$code = SITE_URL.'index.php?s=member&c=api&m=captcha&width=100&height=40';
			$string.= '<div class="form-group">'.PHP_EOL;
			$string.= '<label class="col-sm-2 control-label">验证码：</label>'.PHP_EOL;
			$string.= '<div class="col-sm-9"><input name="code" id="dr_code" class="form-control" type="text" /><img align="absmiddle" style="cursor:pointer;" onclick="this.src=\''.$code.'&\'+Math.random();" src="'.$code.'" /></div>'.PHP_EOL;
			$string.= '</div>'.PHP_EOL;
		}
		
		$string.= '<div class="form-group">'.PHP_EOL;
		$string.= '<label class="col-sm-2 control-label"></label>'.PHP_EOL;
		$string.= '<div class="col-sm-9"><button type="submit" class="btn green"> <i class="fa fa-check"></i> 提交 </button></div>'.PHP_EOL;
		$string.= '</div>'.PHP_EOL;
		$string.= '</div>'.PHP_EOL;
		$string.= '</form>'.PHP_EOL;
		
		$string = htmlspecialchars(str_replace(array('					', '				'), '', $string));
		
		echo '<div class="explain-col"><font color="gray">将以下表单代码放到<b>你想显示表单地方</b>，比如首页、单页、内容页等等，你说了算！</font></div><div class="bk10"></div><textarea style="width:500px;height:300px;">'.$string.'</textarea>';
	}
	
	/**
     * 缓存
     */
    public function cache() {
		$this->form_model->cache($site = isset($_GET['site']) && $_GET['site'] ? (int)$_GET['site'] : SITE_ID);
        (int)$_GET['admin'] or $this->admin_msg(fc_lang('操作成功，正在刷新...'), isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 1);
	}
	
}