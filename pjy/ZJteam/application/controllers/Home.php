<?php 
/**
*
*@author:PJY
*@date:2015/10/1
*
**/
class Home extends CI_Controller{
	
	public function __construct()
    {
        parent::__construct();
		$this->load->helper(array('form', 'url'));		
		$this->load->model('home_model');
		session_start();
    }
//首页

	public function logindex() {
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$this->load->view("home/login",$data);
	}
		
	public function register() {
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$this->load->view("home/register",$data);
	}
	
	public function checkRegister() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[user.email]',array(
			'required'  => '邮箱不能为空',
			'valid_email' => '邮箱格式不对',
			'is_unique' => '该邮箱已经被注册'
		));
        $this->form_validation->set_rules(
			'password', 'Password',
			'required|min_length[6]|max_length[15]',
			array(
				'required'  => '密码不能为空',
				'min_length' => '密码长度必须在大于6',
				'max_length' => '名字长度必须在小于15'
			)
		);
		$this->form_validation->set_rules(
			'password2', 'Password2',
			'required|matches[password]',
			array(
				'required'  => '密码不能为空',
				'matches' => '两次输入的密码不一致！'
			)
		);
		$this->form_validation->set_rules(
			'truename', 'Truename',
			'required|min_length[2]|max_length[10]',
			array(
				'required'  => '真实姓名不能为空',
				'min_length' => '真实姓名长度必须在大于2',
				'max_length' => '真实姓名长度必须在小于10',
			)
		);
		$this->form_validation->set_rules(
			'school', 'School',
			'required|min_length[3]|max_length[100]',
			array(
				'required'  => '学校不能为空',
				'min_length' => '学校长度必须在大于3',
				'max_length' => '学校长度必须在小于100',
			)
		);
		$this->form_validation->set_rules(
			'phone', 'Phone',
			'required|numeric',
			array(
				'required'  => '电话不能为空',
				'numeric' => '请正确输入电话'
			)
		);	
		$this->form_validation->set_rules(
			'qq', 'QQ',
			'required|numeric',
			array(
				'required'  => 'QQ不能为空',
				'numeric' => '请正确输入QQ'
			)
		);
		if($this->form_validation->run() == FALSE)
        {
			$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
			$this->load->view('home/register',$data);

        }else{
			$email = $this->input->post("email");
			$password = md5($this->input->post("password"));
			$truename = $this->input->post("truename");
			$phone = $this->input->post("phone");
			$school = $this->input->post("school");
			$qq = $this->input->post("qq");
			$sex = $this->input->post("sex");
			$regtime = date("Y-m-d H:m:s");
			
			$query = $this->db->query("insert into user (email, password, truename, school, phone, qq, sex, regtime) values ('$email', '$password', '$truename', '$school', '$phone', '$qq', '$sex', '$regtime')"); 
			if($query) {
				$this->home_model->msg("注册成功！", site_url("home/logindex"));
			}else {
				$this->home_model->msg("注册失败！", site_url("home/register"));
			}
		}
	}
	
	public function checkLogin(){
		$email = $this->input->post("email");
		$pwd = md5($this->input->post("password"));
		
		$check_query = $this->db->query("SELECT email FROM user WHERE email='$email' AND password='$pwd' AND gid='0'")->row();
		$id = $this->db->query("SELECT id FROM user WHERE email='$email' AND password='$pwd' AND gid='0'")->row();
		if($check_query){
			//登录成功  
			$_SESSION['email'] = $check_query->email;  
			$_SESSION['zjb'] = 'zjb';
			$_SESSION['id'] = $id->id;
			$this->home_model->msg("登录成功！", site_url("home/index"));
		}else{
			$this->home_model->msg("账号密码错误，请重新输入！", site_url("home/logindex"));
		}	
	}
	public function logout(){
		unset ($_SESSION['email']);
		unset ($_SESSION['zjb']);
		unset ($_SESSION['id']);
		$this->home_model->msg("退出成功！", site_url("home/index"));
	}
	public function myzoom() {
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$str='';
		if(isset($_SESSION['id'])){
			$uid = $_SESSION['id'];				
			
			$aid = $this->db->query("select aid from baoming where uid='$uid'")->result_array();
			foreach($aid as $aid) {
				$id = $aid['aid'];
				$result = $this->db->query("select * from activity where id='$id'")->row();
				
				$str .=  '<li class="recomment_li"> <a href="'.site_url("home/zjdetail/".$result->id).'"><img height="180px" src="'.base_url().$result->pic.'" class="recommend-focus"/></a> 
				  <div class="recommend_wrap">
				   <span class="icon2">支教活动</span>
				   <h3><a href="'.site_url("home/zjdetail/".$result->id).'">'.$result->title.'</a></h3>
				  </div> 
				  <div class="donate_infor clearfix"> 
				   <p class="donate_content"> 活动时间：<span class="m_num">'.$result->duringtime.'</span><br /> 
				   支教地址: <span>'.$result->address.'</span>'; 
				if($result->status==0){$str .= '<a href="javascript:void(0)" class="donate_btn">报名中</a> ';}elseif($result->status==1){$str .= '<a href="javascript:void(0)" class="donate_btn2">活动进行中</a> ';}elseif($result->status==2){$str .= '<a href="javascript:void(0)" class="donate_btn3">已结束</a> ';}  
				$str .= ' </p></div> 
				</li> ';
			}
		}
		$data['actinfo'] = $str;
		$this->load->view("home/myzoom",$data);
	}	
	public function modInfo() {
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$uid = $_SESSION['id'];
		$data['userinfo'] = $this->db->query("select * from user where id='$uid'")->row();
		$this->load->view("home/modinfo",$data);
	}
	public function addBaoMing() {
		$uid = $_SESSION['id'];
		$aid = $this->uri->segment(3);
		$baomingtime = date("Y-m-d H:m:s");
		$query = $this->db->query("insert into baoming (uid, aid, baomingtime) values ('$uid', '$aid', '$baomingtime')");
		if($query) {
			$this->home_model->msg("报名成功！",'back');
		}else {
			$this->home_model->msg("报名失败！",'back');
		}
	}
	public function cancelBaoming() {
		$uid = $_SESSION['id'];
		$aid = $this->uri->segment(3);
		$query = $this->db->query("delete from baoming where uid='$uid' and aid='$aid'");
		if($query) {
			$this->home_model->msg("取消报名成功!",'back');
		}else {
			$this->home_model->msg("取消报名失败!",'back');
		}
	}
	public function modUserInfo() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules(
			'truename', 'Truename',
			'required|min_length[2]|max_length[10]',
			array(
				'required'  => '真实姓名不能为空',
				'min_length' => '真实姓名长度必须在大于2',
				'max_length' => '真实姓名长度必须在小于10',
			)
		);
		$this->form_validation->set_rules(
			'school', 'School',
			'required|min_length[3]|max_length[100]',
			array(
				'required'  => '学校不能为空',
				'min_length' => '学校长度必须在大于3',
				'max_length' => '学校长度必须在小于100',
			)
		);
		$this->form_validation->set_rules(
			'phone', 'Phone',
			'required|numeric',
			array(
				'required'  => '电话不能为空',
				'numeric' => '请正确输入电话'
			)
		);	
		$this->form_validation->set_rules(
			'qq', 'QQ',
			'required|numeric',
			array(
				'required'  => 'QQ不能为空',
				'numeric' => '请正确输入QQ'
			)
		);
		if($this->form_validation->run() == FALSE)
        {
			$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
			$this->load->view('home/modInfo',$data);

        }else{
			$uid = $_SESSION['id'];
			$truename = $this->input->post("truename");
			$school = $this->input->post("school");
			$phone = $this->input->post("phone");
			$qq = $this->input->post("qq");
			$sex = $this->input->post("sex");
			
			$query = $this->db->query("update user set truename='$truename', school='$school',phone='$phone',qq='$qq',sex='$sex' where id='$uid'"); 
			if($query) {
				$this->home_model->msg("修改成功！", 'back');
			}else {
				$this->home_model->msg("修改失败！", 'back');
			}
		}
	}
	//首页
	public function index(){
		$this->db->query("update zjbset set hits=hits+1");
		$data['zhijiao'] = $this->db->query("select * from activity order by status asc limit 2")->result_array();
		$data['zhongchou'] = $this->db->query("select * from zhongchou order by status asc limit 2")->result_array();
		$data['book'] = $this->db->query("select * from book order by addtime desc limit 3")->result_array();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['heart'] = $this->db->query("select * from heart")->result_array();
		$this->load->view('home/index',$data);
	}	

	//支教列表
	public function zhiJiaoList(){
		$count = count($this->db->query("select * from activity")->result_array());
		$data['count'] = $count;
		$data['page'] = $this->home_model->page(site_url("home/zhijiaolist"),$count,4);
		$num = intval($this->uri->segment(3));
		$data['zhijiaolist'] = $this->db->query("select * from activity order by status asc limit $num , 4 ")->result_array();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$this->load->view('home/zhijiaolist',$data);
	}	
	
	//支教详情
	public function zjDetail() {
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$id = $this->uri->segment(3);
		$this->db->query("update activity set views=views+1 where id='$id'");
		$data['zhijiao'] = $this->db->query("select * from activity where id='$id'")->row();
		
		if(isset($_SESSION['zjb'])){
			$uid = $_SESSION['id'];
			$query = $this->db->query("select * from baoming where uid='$uid' and aid='$id'")->row();
			if($query) {
				$data['baoming'] = 0;
			}else {
				$data['baoming'] = 1;
			}
		}
		$this->load->view('home/zjdetail',$data);		
	}
	
	//众筹列表
	public function zcList(){
		$count = count($this->db->query("select * from zhongchou")->result_array());
		$data['count'] = $count;
		$data['page'] = $this->home_model->page(site_url("home/zclist"),$count,4);
		$num = intval($this->uri->segment(3));
		$data['zclist'] = $this->db->query("select * from zhongchou order by status asc limit $num , 4 ")->result_array();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$this->load->view('home/zclist',$data);
	}	
	//众筹详情 
	public function zcDetail() {
		$id = $this->uri->segment(3);
		$data['zhongchou'] = $this->db->query("select * from zhongchou where id='$id'")->row();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$this->load->view('home/zcdetail',$data);
	}
	
	//捐书列表
	public function bookList(){
		$count = count($this->db->query("select * from book")->result_array());
		$data['count'] = $count;
		$data['page'] = $this->home_model->page(site_url("home/booklist"),$count,6);
		$num = intval($this->uri->segment(3));
		$data['booklist'] = $this->db->query("select * from book order by addtime desc limit $num , 6 ")->result_array();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$this->load->view('home/booklist',$data);
	}	
	//捐书详情 
	public function bookDetail() {
		$id = $this->uri->segment(3);
		$data['book'] = $this->db->query("select * from book where id='$id'")->row();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$this->load->view('home/bookdetail',$data);
	}

	//保险列表
	public function safeList(){
		$count = count($this->db->query("select * from safe")->result_array());
		$data['count'] = $count;
		$data['page'] = $this->home_model->page(site_url("home/safelist"),$count,10);
		$num = intval($this->uri->segment(3));
		$data['safelist'] = $this->db->query("select * from safe order by addtime desc limit $num , 10 ")->result_array();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$this->load->view('home/safelist',$data);
	}	
	//保险详情 
	public function safeDetail() {
		$id = $this->uri->segment(3);
		$data['safedetail'] = $this->db->query("select * from safe where id='$id'")->row();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$this->load->view('home/safedetail',$data);
	}
	//支教有感列表
	public function youganList(){
		$count = count($this->db->query("select * from yougan")->result_array());
		$data['count'] = $count;
		$data['page'] = $this->home_model->page(site_url("home/youganlist"),$count,10);
		$num = intval($this->uri->segment(3));
		$data['youganlist'] = $this->db->query("select * from yougan order by addtime desc limit $num , 10 ")->result_array();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$this->load->view('home/youganlist',$data);
	}
	//支教有感详情 
	public function youganDetail() {
		$id = $this->uri->segment(3);
		$data['yougandetail'] = $this->db->query("select * from yougan where id='$id'")->row();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$data['safe'] = $this->db->query("select * from safe order by addtime desc limit 4")->result_array();
		$data['yougan'] = $this->db->query("select * from yougan order by addtime desc limit 4")->result_array();
		$this->load->view('home/yougandetail',$data);
	}

	public function about(){
		$data['about'] = $this->db->query("select * from about where id='1'")->row();
		$data['set'] = $this->db->query("select * from zjbset where id='1'")->row();
		$this->load->view('home/about',$data);
	}	
	
}
?>