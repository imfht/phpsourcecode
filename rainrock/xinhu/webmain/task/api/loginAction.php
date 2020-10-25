<?php 
class loginClassAction extends apiAction
{
	public function checkAction()
	{
		$adminuser	= str_replace(' ','',$this->rock->jm->base64decode($this->post('user')));
		$adminpass	= $this->rock->jm->base64decode($this->post('pass'));
		$arr 		= m('login')->start($adminuser, $adminpass);
		if(is_array($arr)){
			
			if(isset($arr['mobile'])){
				$this->showreturn($arr, $arr['msg'], 205);
			}
			
			$arrs = array(
				'uid' 	=> $arr['uid'],
				'name' 	=> $arr['name'],
				'user'	=> $arr['user'],
				'ranking'	=> $arr['ranking'],
				'deptname'  => $arr['deptname'],
				'deptallname' => $arr['deptallname'],
				'face'  	=> $arr['face'],
				'apptx'  	=> $arr['apptx'],
				'loginyzm'  => (int)getconfig('loginyzm','0'),
				'token'  	=> $arr['token'],
				'iskq'  	=> (int)m('userinfo')->getmou('iskq', $arr['uid']), //判断是否需要考勤
				'title'		=> getconfig('apptitle'),
				'weblogo'	=> getconfig('weblogo')
			);
			
			$uid 	= $arr['uid'];
			$name 	= $arr['name'];
			$user 	= $arr['user'];
			$token 	= $arr['token'];
			m('login')->setsession($uid, $name, $token, $user);
			$this->showreturn($arrs);
		}else{
			$this->showreturn('', $arr, 201);
		}
	}
	
	public function loginexitAction()
	{
		m('login')->exitlogin('', $this->token);
		$this->showreturn('');
	}
	
	/**
	*	app登录页面初始化
	*/
	public function appinitAction()
	{
		$arrs = array(
			'loginyzm'  => (int)getconfig('loginyzm','0'),
			'title'		=> getconfig('apptitle'),
			'regtype'	=> getconfig('regtype','0'), //是否可注册1,可注册
		);
		
		$this->showreturn($arrs);
	}
	
	/**
	*	下载图片
	*/
	public function downimgAction()
	{
		$paths= $this->getvals('path');
		$path = str_replace(URL, '', $paths);
		$obj  = c('upfile');
		$str  = '';
		$ext  = $obj->getext($path);
		if($obj->isimg($ext) && file_exists($path)){
			$str = base64_encode(file_get_contents($path));
		}
		$this->showreturn(array(
			'result' => $str,
			'path'	 => $paths
		));
	}
	
	/**
	*	下载图片新
	*/
	public function downimgnewAction()
	{
		$paths= urldecode($this->get('path'));
		$path = str_replace(URL, '', $paths);
		$obj  = c('upfile');
		$str  = '';
		$ext  = $obj->getext($path);
		if($obj->isimg($ext) && (file_exists($path) || substr($path,0,4)=='http')){
			$str = base64_encode(file_get_contents($path));
		}
		$this->showreturn(array(
			'result' => $str,
			'path'	 => $paths
		));
	}
	
	/**
	*	读取可上传最大M
	*/
	public function getmaxupAction()
	{
		$maxup = c('upfile')->getmaxzhao();
		$this->showreturn(array(
			'maxup' => $maxup
		));
	}
	
	/**
	*	钉钉jssdk签名
	*/
	public function ddsignAction()
	{
		$bo		= m('dingding:signjssdk');
		$corpId	= $bo->readwxset();
		$agentid= $this->post('agentid');
		if(isempt($agentid))$agentid = $this->rock->session('wxqyagentid');
		if(isempt($corpId) || isempt($agentid)){
			$arr['corpId'] = '';
		}else{
			$url = $this->getvals('url');
			$arr = $bo->getsignsdk($url);
			$arr['agentId'] = $agentid;
		}
		$this->showreturn($arr);
	}
	
	/**
	*	获取钉钉企业Id
	*/
	public function ddqiyeidAction()
	{
		$this->showreturn(array(
			'qiyeid' => $this->option->getval('dingding_qiyeid')
		));
	}
	
	/**
	*	钉钉获取登录
	*/
	public function dingcheckAction()
	{
		$code = $this->post('code');
		$barr = m('dingding:user')->getuserjssdk($code);
		if($barr['errcode']!=0){
			$this->showreturn('', $barr['msg'], 201);
		}else{
			$this->showreturn($barr);
		}
	}
	
	//初始化验证
	public function initsetAction()
	{
		$call = $this->get('callback');
		$barr['title'] 	= getconfig('reimtitle','REIM');
		//$this->showreturn($barr);
		echo ''.$call.'('.json_encode($barr).')';
	}
	
	public function inauthAction()
	{
		$call = $this->get('callback');
		$barr['host'] 		= HOST;
		//$key  = $this->get('key', $this->jm->getRandkey());
		//$barr['mykey'] 	= $this->jm->encrypt(getconfig('randkey'), $key);
		echo ''.$call.'('.json_encode($barr).')';
	}
	
	//获取二维码
	public function getewmAction()
	{
		$randkey = $this->get('randkey');
		$dfrom   = $this->get('dfrom');
		$this->option->setval($randkey, '');
		header("Content-type:image/png");
		$url = ''.getconfig('outurl', URL).'?m=logn&d=we&randkey='.$randkey.'&dfrom='.$dfrom.'';
		$img = c('qrcode')->show($url);
		echo $img;
	}
	public function checkewmAction()
	{
		$randkey 		= $this->get('randkey');
		$val 	 		= $this->option->getval($randkey);
		
		$data['val'] 	= $val;
		if(isempt($randkey))$this->showreturn($data);
		if($val>'0'){
			$dbs 		= m('admin');
			$urs 		= $dbs->getone("`id`='$val' and `status`=1",'`id`,`name`,`user`,`face`,`pass`');
			if(!$urs){
				$val = '-1';
			}else{
				c('cache')->set('login'.$urs['user'].'', $urs['id'], 60);
				$data['user'] = $urs['user'];
				$data['face'] = $dbs->getface($urs['face']);
				$data['pass'] = md5($urs['pass']);
			}
			$this->option->delete("`num`='$randkey'");
		}
		$data['val'] 	= $val;
		$this->showreturn($data);
	}
	
	/**
	*	创建二维码
	*/
	public function ewmAction()
	{
		header("Content-type:image/png");
		$url = $this->jm->base64decode($this->get('url'));
		if(substr($url,0,4)!='http')$url =''.$this->rock->getouturl().''.$url.'';
		$img = c('qrcode')->show($url);
		echo $img;
	}
	
	/**
	*	安卓检查是否有app更新
	*/
	public function appupdateAction()
	{
		$nowver = getconfig('app_version');//app的版本
		$ver 	= $this->get('ver');
		$barr['success'] = false;
		$path	= getconfig('app_verpath','images/app.apk');//app文件版本
		if(!isempt($nowver) && file_exists($path) && $ver<$nowver){
			$barr['success'] = true;
			$barr['version'] = $nowver;
			$barr['size'] 	 = '3.2M';
			$barr['updateurl'] = ''.URL.''.$path.'';
			$barr['explain']  = getconfig('app_verremark','完善推送功能');
		}
		echo json_encode($barr);
	}
}