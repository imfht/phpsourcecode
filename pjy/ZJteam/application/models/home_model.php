<?php
/**
*
*@author:PJY
*@date:
*
**/
class Home_model extends CI_Model {

    public function __construct()
    {
		parent::__construct();
        $this->load->database();
    }
//提示跳转消息
	public function msg($msg,$url ,$delay=2000) {
		if ($url) {
			$msg .= '<script type="text/javascript">';
			if ($url == 'back') $url = 'javascript:history.go(-1);';
			elseif ($url == 'close') $url = 'javascript:window.close();';
			elseif ($url == 'reload') $url = 'javascript:location.reload();';
			if ($delay > 0) $msg .= 'setTimeout("document.location=\'' . $url . '\'", ' . $delay . ');';
			else $msg .= 'document.location="' . $url . '";';
			$msg .= '</script>';
		}
		$data['msg'] = $msg;
		$data['surl'] = $url;
		$this->load->view('admin/public/msg',$data);
	}
//分页
	public function page($base_url,$total_rows,$perpage){
		$this->load->library('pagination');

		$config['base_url'] = $base_url;
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $perpage;
		$config['uri_segment'] = 3;
		$config['full_tag_open'] = '<ul class="pagination myul">';//整个分页开始标签
		$config['full_tag_close'] = '</ul>';//整个分页结束标签
		$config['num_tag_open'] = '<li>';//自定义数字链接开始
		$config['num_tag_close'] = '</li>';//自定义数字链接结束
		$config['cur_tag_open'] = '<li style="height:44px;width:44px;background-color:#077ac7;float:left;text-align:center;line-height:44px;color:#fff;font-size:16px;">';//当前页面数字标签
		$config['cur_tag_close'] = '</li>';//当前页面数字结束标签
		$config['first_tag_open'] = '<li>';
		$config['first_link'] = '首页';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_link'] = '尾页';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_link'] = '&raquo;';
		$config['next_tag_close'] = '</span></a></li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = '&laquo;';
		$config['prev_tag_close'] = '</span></a></li>';	

		$this->pagination->initialize($config);

		return $this->pagination->create_links();

	}
}
