<?php 
/**
*
*@author:PJY
*@date:2015/10/03
*
**/
class Admin extends CI_Controller{
	public function __construct()
    {
        parent::__construct();
		session_start();
		$this->load->helper('url');
		$this->load->model('admin_model');

    }
	function top(){
		$this->load->view('admin/admindex/top');
	}
	function left(){
		$this->load->view('admin/admindex/left');	
	}
	function right(){
		$this->load->view('admin/admindex/right');	
	}
	function center(){
		$this->load->view('admin/admindex/center');
	}
	function down(){
		$this->load->view('admin/admindex/down');
	}
	function admIndex(){	
		
		if(isset($_SESSION['ok']) && $_SESSION['ok']=='yes'){
			$data['web'] = $this->admin_model->getSet();	
			$this->load->view('admin/admindex/index',$data);			
		}else{
			$this->admin_model->msg("请先登录！");
		}				
	}

/*
*设置信息管理
*/	
	function setInfo(){
		$data['set'] = $this->admin_model->getSet();
		$this->load->view('admin/set/index',$data);
	}
	function update(){
		$this->admin_model->updSet();
	}

/*
*后台登录
*/
		
	function logindex(){
		$this->load->view('admin/login/index');
	}
	function checkLogin(){
		if(!isset($_SESSION)) {
			exit;
		}
		
		$user = $this->input->post("user");
		$psw = md5($this->input->post("passwd"));
		$code = $this->input->post("code");
		
		$check_query = $this->db->query("SELECT username,gid FROM user WHERE username='$user' AND password='$psw'")->row();

		if(($check_query) && (strcasecmp($code,$_SESSION['code'])==0) && ($check_query->gid == 1)) {  

			//登录成功  
			$_SESSION['user'] = $check_query->username;  
			$_SESSION['ok'] = 'yes';
			redirect("admin/admindex/index");			
		}else{
			$data['error'] = "登录失败，请重新登录！";
			$this->load->view("admin/login/index",$data);
		}
	}
	function logout(){
		unset ($_SESSION['user']);
		unset ($_SESSION['passwd']);
		unset ($_SESSION['ok']);
		session_unset();
		session_destroy();
		redirect("admin/logindex");
		
	}
	
	public function userinfo() {
		$cou = count($this->db->query("select id from user where gid=0")->result_array());
		$data['usernums'] = $cou;
		$data['userpage'] = $this->admin_model->page('http://localhost/zhijiaobang/index.php/admin/userinfo/',$cou,8);
		$data['userinfo']=$this->admin_model->getUserInfo(8,intval($this->uri->segment(3)));
		$this->load->view("admin/user/user",$data);
	}
	public function activityLook() {
		$id = $this->uri->segment(3);
		$data['baoming'] = $this->db->query("select aid, baomingtime from baoming where uid='$id'")->result_array();
		$this->load->view("admin/user/look", $data);
	}
	public function baomingLook() {
		$id = $this->uri->segment(3);
		$uid = $this->db->query("select uid from baoming where aid='$id'")->result_array();
		$str='';
		foreach($uid as $uid) {
			$uuid = $uid['uid'];
			$userinfo = $this->db->query("select * from user where id='$uuid'")->row();
			$str .= '
			<tr>
				<td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center">'.$userinfo->truename.'</div></td>
				<td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center">'.$userinfo->email.'</div></td>
				<td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center">'.$userinfo->sex.'</div></td>
				<td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center">'.$userinfo->school.'</div></td>
				<td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center">'.$userinfo->phone.'</div></td>
				<td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center">'.$userinfo->qq.'</div></td>
				<td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center"><a class="blues" href="'.site_url('admin/delBaoming/'.$userinfo->id.'/'.$id).'">取消报名</a></div></td>
			</tr>' ;
		}
		$data['userinfo'] = $str;
		$this->load->view("admin/baoming/index",$data);
	}
	public function delBaoming() {
		$uid = $this->uri->segment(3);
		$aid = $this->uri->segment(4);
		$query = $this->db->query("delete from baoming where uid='$uid' and aid='$aid'");
		if($query) {
			$this->admin_model->msg("取消报名成功！");
		}else {
			$this->admin_model->msg("取消报名失败！");
		}
	}
	public function deluserinfo() {
		$id = $this->uri->segment(3);
		$query = $this->db->query("delete from user where id='$id'");
		if($query) {
			$this->admin_model->msg("删除成功！");
		}else {
			$this->admin_model->msg("删除失败！");
		}
	}
/*
*支教活动发布
*/	
	function actIndex(){
		$data['actlist']=$this->db->query("select * from activity order by status asc")->result_array();
		$this->load->view('admin/activity/index',$data);
	}	
	function actAdd(){
		$this->load->view('admin/activity/add');
	}	
	function actMod(){
		$id = $this->uri->segment(3);
		$data['detail'] = $this->db->query("select * from activity where id='$id'")->row();
		$this->load->view('admin/activity/mod',$data);
	}	
	function updact(){
		/*----图片上传-----*/
			$save_path = 'uploads/zhijiao/';//上传路径

			$config['overwrite']  = true;//开启文件名重写功能
	  		$config['encrypt_name']  = true;

			$config['upload_path'] = './' . $save_path;
	  		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  		$config['max_size'] = '2048';
	  		$config['max_width']  = 3000;
	  		$config['max_height']  = 1500;
	  		$config['file_name'] = date("Ymdhis");//重写的文件名
	  
	  		$this->load->library('upload', $config);
	 
/*	 	*/	$up = $this->upload->do_upload('pic');
	if ( ! $up ) {
				$error = array('error' => $this->upload->display_errors());
	  		 	
	  		} else {
	 	  		$data = array('upload_data' => $this->upload->data());
		}
/*----图片上传-----*/
		
			if(isset($error)){		
				$id = $this->input->post("id");
				$title = $this->input->post("title");
				$author = $this->input->post("author");
				$duringtime = $this->input->post("duringtime");
				$address = $this->input->post("address");
				$content = $this->input->post("editor");
				$addtime = date("Y-m-d G:i:s");
				$status = $this->input->post("status");
				$query = $this->db->query("update activity set title='$title', author='$author',duringtime='$duringtime',address='$address',content='$content',addtime='$addtime', status='$status' where id='$id'");
				if($query) {
					$this->admin_model->msg("修改成功!");
				}else{
					$this->admin_model->msg("修改失败!");
				}

			}else{
				$file_name = $data['upload_data']['file_name'];
				$picurl = $up?$save_path . $file_name:"";//路径加文件名
				
				$id = $this->input->post("id");
				$title = $this->input->post("title");
				$author = $this->input->post("author");
				$duringtime = $this->input->post("duringtime");
				$address = $this->input->post("address");
				$content = $this->input->post("editor");
				$addtime = date("Y-m-d G:i:s");
				$status = $this->input->post("status");
				$query = $this->db->query("update activity set title='$title', author='$author',duringtime='$duringtime',address='$address',content='$content',addtime='$addtime', pic='$picurl', status='$status' where id='$id'");
				if($query) {
					$this->admin_model->msg("修改成功!");
				}else{
					$this->admin_model->msg("修改失败!");
				}	
			}
	}
	function delact(){
		$id = $this->uri->segment(3);
		$query = $this->db->query("delete from activity where id='$id'");
		if($query){
			$this->admin_model->msg("删除资讯成功！");
		}else{
			$this->admin_model->msg("删除数据失败！");
		}
	}
	function bookIndex() {
		$data['book'] = $this->db->query("select * from book")->result_array();
		$this->load->view('admin/book/index',$data);
	}
	function bookMod(){
		$id = $this->uri->segment(3);
		$data['detail'] = $this->db->query("select * from book where id='$id'")->row();
		$this->load->view('admin/book/mod',$data);
	}
	//添加书籍
	public function addBook() {
			/*----图片上传-----*/
			$save_path = 'uploads/book/';//上传路径

			$config['overwrite']  = true;//开启文件名重写功能
	  		$config['encrypt_name']  = true;

			$config['upload_path'] = './' . $save_path;
	  		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  		$config['max_size'] = '2048';
	  		$config['max_width']  = 3000;
	  		$config['max_height']  = 1500;
	  		$config['file_name'] = date("Ymdhis");//重写的文件名
	  
	  		$this->load->library('upload', $config);
	 
/*	 	*/	$up = $this->upload->do_upload('pic');
	if ( ! $up ) {
				$error = array('error' => $this->upload->display_errors());
	  		 	exit("必须选择一张图片！");
	  		} else {
	 	  		$data = array('upload_data' => $this->upload->data());
		}
/*----图片上传-----*/
			$file_name = $data['upload_data']['file_name'];
			$picurl = $up?$save_path . $file_name:"";//路径加文件名
			
			
			$title = $this->input->post("title");
			$donator = $this->input->post("donator");
			$num = $this->input->post("num");
			$content = $this->input->post("editor");
			$addtime = date("Y-m-d G:i:s");
		
			$addb = $this->db->query("INSERT INTO book (title,donator,num,content,addtime,pic) VALUES ('$title','$donator','$num','$content','$addtime','$picurl')");
			if($addb){
				$this->admin_model->msg("插入数据成功！");
			}else{
				$this->admin_model->msg("插入数据失败！");
			}				
	}
	function updBook(){
		/*----图片上传-----*/
			$save_path = 'uploads/book/';//上传路径

			$config['overwrite']  = true;//开启文件名重写功能
	  		$config['encrypt_name']  = true;

			$config['upload_path'] = './' . $save_path;
	  		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  		$config['max_size'] = '2048';
	  		$config['max_width']  = 3000;
	  		$config['max_height']  = 1500;
	  		$config['file_name'] = date("Ymdhis");//重写的文件名
	  
	  		$this->load->library('upload', $config);
	 
/*	 	*/	$up = $this->upload->do_upload('pic');
	if ( ! $up ) {
				$error = array('error' => $this->upload->display_errors());
	  		 	
	  		} else {
	 	  		$data = array('upload_data' => $this->upload->data());
		}
/*----图片上传-----*/
		
			if(isset($error)){		
				$id = $this->input->post("id");
				$title = $this->input->post("title");
				$donator = $this->input->post("donator");
				$num = $this->input->post("num");
				$content = $this->input->post("editor");
				$query = $this->db->query("update book set title='$title', donator='$donator',num='$num',content='$content' where id='$id'");
				if($query) {
					$this->admin_model->msg("修改成功!");
				}else{
					$this->admin_model->msg("修改失败!");
				}

			}else{
				$file_name = $data['upload_data']['file_name'];
				$picurl = $up?$save_path . $file_name:"";//路径加文件名
				
				$id = $this->input->post("id");
				$title = $this->input->post("title");
				$donator = $this->input->post("donator");
				$num = $this->input->post("num");
				$content = $this->input->post("editor");
				$query = $this->db->query("update book set title='$title', donator='$donator',num='$num',content='$content',pic='$picurl' where id='$id'");
				if($query) {
					$this->admin_model->msg("修改成功!");
				}else{
					$this->admin_model->msg("修改失败!");
				}
			}
			
	
	}

/*****************************图片上传开始*************************************************/
	function addAct(){
		/*----图片上传-----*/
			$save_path = 'uploads/zhijiao/';//上传路径

			$config['overwrite']  = true;//开启文件名重写功能
	  		$config['encrypt_name']  = true;

			$config['upload_path'] = './' . $save_path;
	  		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  		$config['max_size'] = '2048';
	  		$config['max_width']  = 3000;
	  		$config['max_height']  = 1500;
	  		$config['file_name'] = date("Ymdhis");//重写的文件名
	  
	  		$this->load->library('upload', $config);
	 
/*	 	*/	$up = $this->upload->do_upload('pic');
	if ( ! $up ) {
				$error = array('error' => $this->upload->display_errors());
	  		 	exit("必须选择一张图片！");
	  		} else {
	 	  		$data = array('upload_data' => $this->upload->data());
		}
/*----图片上传-----*/
			$file_name = $data['upload_data']['file_name'];
			$picurl = $up?$save_path . $file_name:"";//路径加文件名
			
			
			$title = $this->input->post("title");
			$author = $this->input->post("author");
			$address = $this->input->post("address");
			$duringtime = $this->input->post("duringtime");
			$content = $this->input->post("editor");
			$addtime = date("Y-m-d G:i:s");
		
			$this->admin_model->addArt($title, $author, $address, $duringtime, $picurl, $content, $addtime);
	}
	public function about() {
		$data['about'] = $this->db->query("select * from about where id='1'")->row();
		$this->load->view("admin/about/index",$data);
	} 
	public function updateAbout() {
		$content = $this->input->post("editor");
		$query = $this->db->query("update about set content='$content' where id='1'");
		if($query) {
			$this->admin_model->msg("修改成功！");
		}else {
			$this->admin_model->msg("修改失败！");
		}
	}
	public function bookAdd() {
		$this->load->view("admin/book/add");
	}	
	function delBook() {
		$id = $this->uri->segment(3);
		$query = $this->db->query("delete from book where id='$id'");
		if($query){
			$this->admin_model->msg("删除资讯成功！");
		}else{
			$this->admin_model->msg("删除数据失败！");
		}		
	}

/*
*后台登录管理
*/	
	public function heartAdd() {
		$this->load->view('admin/heart/add');
	}
	public function addHeart() {
			/*----图片上传-----*/
			$save_path = 'uploads/heart/';//上传路径

			$config['overwrite']  = true;//开启文件名重写功能
	  		$config['encrypt_name']  = true;

			$config['upload_path'] = './' . $save_path;
	  		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  		$config['max_size'] = '2048';
	  		$config['max_width']  = 3000;
	  		$config['max_height']  = 1500;
	  		$config['file_name'] = date("Ymdhis");//重写的文件名
	  
	  		$this->load->library('upload', $config);
	 
/*	 	*/	$up = $this->upload->do_upload('pic');
	if ( ! $up ) {
				$error = array('error' => $this->upload->display_errors());
	  		 	exit("必须选择一张图片！");
	  		} else {
	 	  		$data = array('upload_data' => $this->upload->data());
		}
/*----图片上传-----*/
			$file_name = $data['upload_data']['file_name'];
			$picurl = $up?$save_path . $file_name:"";//路径加文件名
			
			
			$uname = $this->input->post("uname");
			$edit = $this->input->post("edit");
			$info = $this->input->post("info");
			$addtime = date("Y-m-d G:i:s");
		
			$addb = $this->db->query("INSERT INTO heart (uname,edit,info,addtime,pic) VALUES ('$uname','$edit','$info','$addtime','$picurl')");
			if($addb){
				$this->admin_model->msg("插入数据成功！");
			}else{
				$this->admin_model->msg("插入数据失败！");
			}				
	}
	function heartIndex() {
		$data['heart'] = $this->db->query("select * from heart")->result_array();
		$this->load->view('admin/heart/index',$data);
	}
	function delHeart() {
		$id = $this->uri->segment(3);
		$query = $this->db->query("delete from heart where id='$id'");
		if($query){
			$this->admin_model->msg("删除资讯成功！");
		}else{
			$this->admin_model->msg("删除数据失败！");
		}		
	}
	function heartMod() {
		$id = $this->uri->segment(3);
		$data['detail'] = $this->db->query("select * from heart where id='$id'")->row();
		$this->load->view('admin/heart/mod',$data);
	}
	function updHeart(){
		/*----图片上传-----*/
			$save_path = 'uploads/heart/';//上传路径

			$config['overwrite']  = true;//开启文件名重写功能
	  		$config['encrypt_name']  = true;

			$config['upload_path'] = './' . $save_path;
	  		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  		$config['max_size'] = '2048';
	  		$config['max_width']  = 3000;
	  		$config['max_height']  = 1500;
	  		$config['file_name'] = date("Ymdhis");//重写的文件名
	  
	  		$this->load->library('upload', $config);
	 
/*	 	*/	$up = $this->upload->do_upload('pic');
	if ( ! $up ) {
				$error = array('error' => $this->upload->display_errors());
	  		 	
	  		} else {
	 	  		$data = array('upload_data' => $this->upload->data());
		}
/*----图片上传-----*/
		
			if(isset($error)){		
				$id = $this->input->post("id");
				$uname = $this->input->post("uname");
				$info = $this->input->post("info");
				$query = $this->db->query("update heart set uname='$uname', info='$info' where id='$id'");
				if($query) {
					$this->admin_model->msg("修改成功!");
				}else{
					$this->admin_model->msg("修改失败!");
				}

			}else{
				$file_name = $data['upload_data']['file_name'];
				$picurl = $up?$save_path . $file_name:"";//路径加文件名
				
				$id = $this->input->post("id");
				$uname = $this->input->post("uname");
				$info = $this->input->post("info");
				$query = $this->db->query("update heart set uname='$uname', info='$info',pic='$picurl' where id='$id'");
				if($query) {
					$this->admin_model->msg("修改成功!");
				}else{
					$this->admin_model->msg("修改失败!");
				}
			}
			
	
	}
	//众筹
	public function zcAdd() {
		$this->load->view("admin/zhongchou/add");
	}
	public function addZc() {
			/*----图片上传-----*/
			$save_path = 'uploads/zhongchou/';//上传路径

			$config['overwrite']  = true;//开启文件名重写功能
	  		$config['encrypt_name']  = true;

			$config['upload_path'] = './' . $save_path;
	  		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  		$config['max_size'] = '2048';
	  		$config['max_width']  = 3000;
	  		$config['max_height']  = 1500;
	  		$config['file_name'] = date("Ymdhis");//重写的文件名
	  
	  		$this->load->library('upload', $config);
	 
/*	 	*/	$up = $this->upload->do_upload('pic');
	if ( ! $up ) {
				$error = array('error' => $this->upload->display_errors());
	  		 	exit("必须选择一张图片！");
	  		} else {
	 	  		$data = array('upload_data' => $this->upload->data());
		}
/*----图片上传-----*/
			$file_name = $data['upload_data']['file_name'];
			$picurl = $up?$save_path . $file_name:"";//路径加文件名
			
			
			$title = $this->input->post("title");
			$url = $this->input->post("url");
			$author = $this->input->post("author");
			$money = $this->input->post("money");
			$zctime = $this->input->post("zctime"); 
			$content = $this->input->post("editor"); 
			$addtime = date("Y-m-d G:i:s");
		
			$query = $this->db->query("insert into zhongchou (title, url, author, money, zctime, pic, content, addtime) values ('$title', '$url','$author', '$money', '$zctime', '$picurl', '$content', '$addtime')");
			if($query) {
				$this->admin_model->msg("添加成功!");
			}else {
				$this->admin_model->msg("添加失败!");
			}
		
	}
	
	public function zcIndex() {
		$data['zc'] = $this->db->query("select * from zhongchou order by status asc")->result_array();
		$this->load->view("admin/zhongchou/index", $data);
	}
	public function delZc() {
		$id = $this->uri->segment(3);
		$query = $this->db->query("delete from zhongchou where id='$id'");
		if($query) {
			$this->admin_model->msg("删除成功!");
		}else {
			$this->admin_model->msg("删除失败!");
		}
	}
	public function zcMod() {
		$id = $this->uri->segment(3);
		$data['zc'] = $this->db->query("select * from zhongchou where id='$id'")->row();
		$this->load->view("admin/zhongchou/mod", $data);
	}
	function updZc(){
		/*----图片上传-----*/
			$save_path = 'uploads/zhongchou/';//上传路径

			$config['overwrite']  = true;//开启文件名重写功能
	  		$config['encrypt_name']  = true;

			$config['upload_path'] = './' . $save_path;
	  		$config['allowed_types'] = 'jpg|jpeg|gif|png';
	  		$config['max_size'] = '2048';
	  		$config['max_width']  = 3000;
	  		$config['max_height']  = 1500;
	  		$config['file_name'] = date("Ymdhis");//重写的文件名
	  
	  		$this->load->library('upload', $config);
	 
/*	 	*/	$up = $this->upload->do_upload('pic');
	if ( ! $up ) {
				$error = array('error' => $this->upload->display_errors());
	  		 	
	  		} else {
	 	  		$data = array('upload_data' => $this->upload->data());
		}
/*----图片上传-----*/
		
			if(isset($error)){		
				$id = $this->input->post("id");
				$title = $this->input->post("title");
				$url = $this->input->post("url");
				$author = $this->input->post("author");
				$money = $this->input->post("money");
				$zctime = $this->input->post("zctime");
				$status = $this->input->post("status");
				$content = $this->input->post("editor");
				$query = $this->db->query("update zhongchou set title='$title',url='$url',author='$author', money='$money', zctime='$zctime',status='$status',content='$content' where id='$id'");
				if($query) {
					$this->admin_model->msg("修改成功!");
				}else{
					$this->admin_model->msg("修改失败!");
				}

			}else{
				$file_name = $data['upload_data']['file_name'];
				$picurl = $up?$save_path . $file_name:"";//路径加文件名
				
				$id = $this->input->post("id");
				$title = $this->input->post("title");
				$url = $this->input->post("url");
				$author = $this->input->post("author");
				$money = $this->input->post("money");
				$zctime = $this->input->post("zctime");
				$status = $this->input->post("status");
				$content = $this->input->post("editor");
				$query = $this->db->query("update zhongchou set title='$title',url='$url',author='$author', money='$money', zctime='$zctime',status='$status',content='$content',pic='$picurl' where id='$id'");
				if($query) {
					$this->admin_model->msg("修改成功!");
				}else{
					$this->admin_model->msg("修改失败!");
				}
			}
	}

	//保险资金
	public function safeAdd() {
		$this->load->view("admin/safe/add");
	}
	public function addSafe() {
		$title = $this->input->post("title");
		$content = $this->input->post("editor");
		$addtime = date("Y-m-d");
		$query = $this->db->query("insert into safe (title, content, addtime) values ('$title', '$content', '$addtime')");
		if($query) {
			$this->admin_model->msg("添加成功!");
		}else {
			$this->admin_model->msg("添加失败!");
		}
	}
	public function safeIndex() {
		$data['safe'] = $this->db->query("select * from safe")->result_array();
		$this->load->view("admin/safe/index", $data);
	}	
	public function delSafe() {
		$id = $this->uri->segment(3);
		$query = $this->db->query("delete from safe where id='$id'");
		if($query) {
			$this->admin_model->msg("删除成功!");
		}else {
			$this->admin_model->msg("删除失败!");
		}
	}
	public function safeMod() {
		$id = $this->uri->segment(3);
		$data['safe'] = $this->db->query("select * from safe where id='$id'")->row();
		$this->load->view("admin/safe/mod", $data);
	}
	public function updSafe() {
		$id = $this->input->post("id");
		$title = $this->input->post("title");
		$content = $this->input->post("editor");
		$query = $this->db->query("update safe set title='$title',content='$content' where id='$id'");
		if($query) {
			$this->admin_model->msg("修改成功!");
		}else {
			$this->admin_model->msg("修改失败!");
		}
	}	
	//支教有感
	public function youganAdd() {
		$this->load->view("admin/yougan/add");
	}
	public function addYougan() {
		$title = $this->input->post("title");
		$content = $this->input->post("editor");
		$addtime = date("Y-m-d");
		$query = $this->db->query("insert into yougan (title, content, addtime) values ('$title', '$content', '$addtime')");
		if($query) {
			$this->admin_model->msg("添加成功!");
		}else {
			$this->admin_model->msg("添加失败!");
		}
	}
	public function youganIndex() {
		$data['yougan'] = $this->db->query("select * from yougan")->result_array();
		$this->load->view("admin/yougan/index", $data);
	}	
	public function delYougan() {
		$id = $this->uri->segment(3);
		$query = $this->db->query("delete from yougan where id='$id'");
		if($query) {
			$this->admin_model->msg("删除成功!");
		}else {
			$this->admin_model->msg("删除失败!");
		}
	}
	public function youganMod() {
		$id = $this->uri->segment(3);
		$data['yougan'] = $this->db->query("select * from yougan where id='$id'")->row();
		$this->load->view("admin/yougan/mod", $data);
	}
	public function updYougan() {
		$id = $this->input->post("id");
		$title = $this->input->post("title");
		$content = $this->input->post("editor");
		$query = $this->db->query("update yougan set title='$title',content='$content' where id='$id'");
		if($query) {
			$this->admin_model->msg("修改成功!");
		}else {
			$this->admin_model->msg("修改失败!");
		}
	}

}
?>