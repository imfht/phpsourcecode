<?php
/**
*
*@author:PJY
*@date:
*
**/
class Admin_model extends CI_Model {

    public function __construct()
    {
		parent::__construct();
		$this->load->database();
    }
/*
*网站基本设置
*/
	public function getSet(){
		return $query = $this->db->query("SELECT * FROM zjbset")->row();
	}
	public function updSet(){
		$webname = $this->input->post("webname");
		$keyword = $this->input->post("keyword");
		$description = $this->input->post("description");
		$copyright = $this->input->post("copyright");
		$query = $this->db->query("UPDATE zjbset SET webname='$webname',keywords='$keyword',description='$description',copyright='$copyright' WHERE id='1'");
		if($query){
			$this->admin_model->msg("修改成功！");
		}else{
			$this->admin_model->msg("修改失败！");
		}
	}
	public function getUserInfo($num,$offset){
		return $query = $this->db->query("select id,email,truename,school,phone,qq,sex,regtime from user where gid=0 order by regtime limit $offset,$num")->result_array();
	}
/*
*资讯设置
*/
	public function addArt($title, $author,$address, $duringtime, $picurl, $content, $addtime){
		$adda = $this->db->query("INSERT INTO activity (title,author,address,duringtime,pic,content,addtime) VALUES ('$title','$author','$address','$duringtime','$picurl','$content','$addtime')");
		if($adda){
			$this->admin_model->msg("插入数据成功！");
		}else{
			$this->admin_model->msg("插入数据失败！");
		}
	}

//关于我们

	public function getAbout(){
		return $query = $this->db->query("select * from about where id='1'")->row();
	}
	public function updateAbout($id,$author,$created,$content,$posttime){
		$query = $this->db->query("update about set author='$author',created='$created',content='$content',posttime='$posttime' where id='$id'");
		if($query){
			$this->admin_model->msg("修改成功！");
		}else{
			$this->admin_model->msg("修改失败！");
		}
	}

/*
*提示跳转消息
*/
	public function msg($msg,$url='back',$delay=2000) {
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
		$config['num_tag_open'] = '<div style="height:20px;width:10px;background-color:#E6F3FB;float:left;border:1px solid #9CC9E0">';//自定义数字链接开始
		$config['num_tag_close'] = '</div>';//自定义数字链接结束
		$config['cur_tag_open'] = '<div style="height:20px;width:10px;background-color:#077ac7;float:left;border:1px solid #9CC9E0">';//当前页面数字标签
		$config['cur_tag_close'] = '</div>';//当前页面数字结束标签
		$config['first_tag_open'] = '<div style="height:20px;width:45px;background-color:#E6F3FB;border:1px solid #9CC9E0;float:left;">';
		$config['first_link'] = '首页';
		$config['first_tag_close'] = '</div>';
		$config['last_tag_open'] = '<div style="height:20px;width:45px;background-color:#E6F3FB;float:left;border:1px solid #9CC9E0">';
		$config['last_link'] = '尾页';
		$config['last_tag_close'] = '</div>';
		$config['next_tag_open'] = '<div style="height:20px;width:45px;background-color:#E6F3FB;float:left;border:1px solid #9CC9E0">';
		$config['next_link'] = '下一页>';
		$config['next_tag_close'] = '</div>';
		$config['prev_tag_open'] = '<div style="height:20px;width:45px;background-color:#E6F3FB;float:left;border:1px solid #9CC9E0">';
		$config['prev_link'] = '<上一页';
		$config['prev_tag_close'] = '</div>';	

		$this->pagination->initialize($config);

		return $this->pagination->create_links();

	}
}