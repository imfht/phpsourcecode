<?php
class indexController extends adminController{
	protected $layout = 'layout';
	
	public function ppacountlist(){
		$userinfo = $this->userinfo;
		$url = url('index/ppacountlist',array(p=>'{page}'));
		$limit=$this->pageLimit($url);
		if( $userinfo['pid'] == 0 ){
			$count=$this->model->table('ppacount')->where( array('adminid'=>$userinfo['id']) )->count();
			$this->pplist=$this->model->table('ppacount')->where( array('adminid'=>$userinfo['id']) )->limit($limit)->select();
		}else{
			$manage = json_decode($userinfo['manage'],true);
			$count=count($manage);
			
			$conditon = array();
			for($i = 0;$i < $count;$i++){
				$conditon[$i]= 'id ='.$manage[$i]['ppid'];
			}
			$strcon = implode(" or ",$conditon);
			$this->pplist=$this->model->table('ppacount')->where( $strcon )->limit($limit)->select();
		}
		$this->page = $this->pageShow($count);
		$this->display();
	}
	
	public function ppacountadd(){
		if( !$this->isPost() ){
			$this->display();
		}else{
			$data = $_POST;
			$data['createtime'] = time();
			$data['adminid'] = $this->userinfo['id'];
			$data['hash'] = getcode('7');
			$data['token'] = getcode();
			if( !empty($_POST['appid']) && !empty($_POST['appsecret']) ){
			$options = array(
				'appid'=>$_POST['appid'],
				'appsecret'=>$_POST['appsecret'],
			);
			$weObj = new Wechat($options);
			$result = $weObj->checkAuth();
			if($result){
				
			}else{
				$data['appid'] = "";
				$data['appsecret'] = "";
				$checkapp = "（错误的appid和appsecret，原因：".$weObj->errMsg."）";
			}
			}
			if( model('ppacount')->insert($data)){
				$this->alert('添加成功'.$checkapp, url('index/ppacountlist'));
			}else{
				$this->alert('添加失败'.$checkapp);
			}
		}
	}
	
	public function ppacountedit(){
		$id = intval($_GET['id']);
		if( !$this->isPost() ){
			$ppacountinfo = model('ppacount')->find( array('id'=>$id) );
			if( empty($ppacountinfo) ){
				$this->alert('该条数据不存在或者已被删除');
			}
			$this->ppacountinfo = $ppacountinfo;
			$this->display();
			
		}else{
			$data = $_POST;
			if( !empty($_POST['appid']) && !empty($_POST['appsecret']) ){
			$options = array(
				'appid'=>$_POST['appid'],
				'appsecret'=>$_POST['appsecret'],
			);
			$weObj = new Wechat($options);
			$result = $weObj->checkAuth();
			if($result){
				
			}else{
				$data['appid'] = "";
				$data['appsecret'] = "";
				$checkapp = "（错误的appid和appsecret，原因：".$weObj->errMsg."）";
			}
			}
			if( model('ppacount')->update(array('id'=>$id), $data) ){
				$this->alert('修改成功'.$checkapp, url('index/ppacountlist'));
			}else{
				$this->alert('修改失败'.$checkapp);
			}
		}
	}
	
	public function ppacountdel(){
		$id = intval($_GET['id']);
		$ppacountinfo = model('ppacount')->find( array('id'=>$id) );
		if( empty($ppacountinfo) ){
			$this->alert('该条数据不存在或者已被删除');
		}
		if( model('ppacount')->delete( array('id'=>$id) ) ){
			$this->alert('删除成功', url('index/ppacountlist'));
		}else{
			$this->alert('删除失败');
		}
	}
	
	public function ppacountpay(){
		$id = intval($_GET['id']);
		if( !$this->isPost() ){
			$this->display();
		}else{
			$data = $_POST;
			$data['createtime'] = time();
			$data['adminid'] = $this->userinfo['id'];
			$data['hash'] = getcode('7');
			$data['token'] = getcode();
			if( !empty($_POST['appid']) && !empty($_POST['appsecret']) ){
			$options = array(
				'appid'=>$_POST['appid'],
				'appsecret'=>$_POST['appsecret'],
			);
			$weObj = new Wechat($options);
			$result = $weObj->checkAuth();
			if($result){
				
			}else{
				$data['appid'] = "";
				$data['appsecret'] = "";
				$checkapp = "（错误的appid和appsecret，原因：".$weObj->errMsg."）";
			}
			}
			if( model('ppacount')->insert($data)){
				$this->alert('添加成功'.$checkapp, url('index/ppacountlist'));
			}else{
				$this->alert('添加失败'.$checkapp);
			}
		}
	}
	
	public function ppacountselect(){
		$id = intval($_GET['id']);
		$ppinfo = model('ppacount')->find( array('id'=>$id) );
		$this->setPPinfo($ppinfo);
		$this->redirect(url('admin/index/index'),true);
	}
	
	public function managelist(){
		$userinfo = $this->userinfo;
		$this->managelist = $this->model->table('admin')->where( array('pid'=>$userinfo['id']) )->select();
		$this->pplist = $this->model->field('id,name')->table('ppacount')->where( array('adminid'=>$userinfo['id']) )->select();
		foreach(getApps(array('install','admin','default','appmanage','ppacount')) as $app){
			$apps[$app]= appConfig( $app );
		}
		$this->apps = $apps;
		$this->display();
	}
	
	public function manageaddedit(){
		$id = $_GET['id'];
		$userinfo = $this->userinfo;
		if( !$this->isPost() ){
			if( !empty($id) ){
				$this->manageinfo = model('ppacount')->admininfo( array('id'=>$id) );
			}
			$this->pplist = $this->model->field('id,name')->table('ppacount')->where( array('adminid'=>$userinfo['id']) )->select();
			foreach(getApps(array('install','admin','default','appmanage','ppacount')) as $app){
				$apps[$app]= appConfig( $app );
			}
			$this->apps = $apps;
			$this->display();
		}else{
			$data = array_filter($_POST);
			$data['username'] = $userinfo['username'].'_'.$_POST['username'];
			if( !empty($data['password']) ){
				$data['password'] = md5($data['password']);
			}
			if(empty($id)){
			$data['pid'] = $userinfo['id'];
			$data['createtime'] = time();
			if( model('ppacount')->adminadd($data)){
				$this->alert('添加成功', url('index/managelist'));
			}else{
				$this->alert('添加失败');
			}
			}else{
			if( model('ppacount')->adminupdate(array('id'=>$id),$data)){
				$this->alert('修改成功', url('index/managelist'));
			}else{
				$this->alert('修改失败');
			}
			}			
		}
	}
	
	public function actionadd(){
		$ppid = $_GET['ppid'];
		$this->okppid = json_encode( explode('_',$ppid) );
		$userinfo = $this->userinfo;
		$this->pplist = $this->model->field('id,name')->table('ppacount')->where( array('adminid'=>$userinfo['id']) )->select();
		foreach(getApps(array('install','admin','default','appmanage','ppacount')) as $app){
			$apps[$app]= appConfig( $app );
		}
		$this->apps = $apps;
		$this->display();
	}
	
	public function actionedit(){
		$nowppid = $_GET['ppid'];
		$this->actions = $_GET['actions'];
		$userinfo = $this->userinfo;
		$this->ppinfo = $this->model->field('id,name')->table('ppacount')->where( array('id'=>$nowppid) )->find();
		foreach(getApps(array('install','admin','default','appmanage','ppacount')) as $app){
			$apps[$app]= appConfig( $app );
		}
		$this->apps = $apps;
		$this->display();
	}
	
	public function managedel(){
		$id = intval($_GET['id']);
		$userinfo = $this->userinfo;
		if($id == $userinfo['id']){
			$this->alert('禁止自杀', url('index/managelist'));
			break;
		}
		$info = model('ppacount')->admininfo( array('id'=>$id) );
		if( empty($info) ){
			$this->alert('该条数据不存在或者已被删除');
		}
		if( model('ppacount')->admindel( array('id'=>$id) ) ){
			$this->alert('删除成功', url('index/managelist'));
		}else{
			$this->alert('删除失败');
		}
	}
	
	public function validusername(){
		$userinfo = $this->userinfo;
		$username = $userinfo['username'].'_'.$_POST['param'];
		if( $this->model->table('admin')->where( array('username'=>$username) )->find() ){
			echo "此用户名已经存在";
		}else{
			echo '{"info":"用户名可用！","status":"y"}';
		}
	}
}