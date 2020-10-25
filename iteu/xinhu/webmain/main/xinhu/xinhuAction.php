<?php
//服务端设置
class xinhuClassAction extends Action
{
	public function initAction()
	{
		$this->xinhuobj = c('xinhuapi');
	}
	
	public function setsaveAjax()
	{
		$this->option->setval('reimhostsystem', $this->post('host'));
		$this->option->setval('reimrecidsystem', $this->post('receid'));
		$this->option->setval('reimpushurlsystem', $this->post('push'));
		$this->option->setval('reimchehuisystem', $this->post('chehui'));
		$this->option->setval('reimservertype', $this->post('servertype'));
		$this->option->setval('reimappwxsystem', $this->post('reimappwx'));
		$this->backmsg();
	}
	
	public function getsetAjax()
	{
		$arr= array();
		$arr['reimhost']= $this->option->getval('reimhostsystem');
		$arr['reimrecid']= $this->option->getval('reimrecidsystem');
		$arr['reimpushurl']= $this->option->getval('reimpushurlsystem');
		$arr['reimchehui']= $this->option->getval('reimchehuisystem');
		$arr['servertype']= $this->rock->repempt($this->option->getval('reimservertype'),'1');
		if(isempt($arr['reimhost']))$arr['servertype']='1';
		$arr['reimappwx']= $this->rock->repempt($this->option->getval('reimappwxsystem'),'0');
		echo json_encode($arr);
	}
	
	//测试地址
	public function yibutestAjax()
	{
		$rand 	= time();
		$arr['krand'] = $rand;
		$runurl	= m('base')->getasynurl('asynrun','asyntest', $arr);
		m('reim')->asynurl('asynrun','asyntest', $arr);
		$msg 	= '<font color="green">测试成功可以使用</font>';
		sleep(10);
		$mkey 	= $this->option->getval('asyntest');
		if($mkey!=$rand)$msg 	= '<font color="red">测试失败不能使用，说明你服务端上是不能访问这地址的</font>';
		echo '异步地址【'.$runurl.'】'.$msg.'';
	}
	
	//测试队列
	public function testqueueAjax()
	{
		$rand 	= 'queue'.time();
		$barr 	= c('rockqueue')->push('cli,test', array(
			'rand' => $rand
		));
		if(!$barr['success'])return '队列测试失败：<font color="red">'.$barr['msg'].'</font>';
		sleep(3);
		$mkey 	= $this->option->getval('asyntest');
		$msg 	= '<font color="green">队列测试成功，可以使用</font>';
		if($mkey!=$rand)$msg 	= '<font color="red">无法运行“'.$barr['cmdurl'].'”测试失败不能使用，去<a href="'.URLY.'view_rockservice.html" target="_blank">看看帮助</a>。</font>';
		echo $msg;
	}
	
	public function testsendAjax()
	{
		$barr  = m('reim')->sendpush($this->adminid, $this->adminid,array(
			'cont' 	=> $this->jm->base64encode('测试内容:'.$this->now.''),
			'type' 	=> 'user',
			'optdt' => $this->now,
			'messid' => 0
		));
		$msg 	= '';
		if($barr['success']){
			$msg='服务端推送地址可以使用';
		}else{
			$msg='<font color=red>服务端推送地址不能使用：'.$barr['msg'].'</font>';
		}
		echo $msg;
	}
	
	public function testsendsAjax()
	{
		$obj = m('reim');
		$url = $obj->serverhosturl;
		$msg = '';
		if(isempt($url))$msg = '请先设置地址后保存在测试';
		if($msg==''){
			$url = str_replace('ws:','http:', $url);
			$cont = c('curl')->getcurl($url);
			if(!contain($cont,'400 Bad Request'))$msg = '通信地址不能用，请看当前页面提示';
		}
		if($msg=='')$msg = '通信地址可以用';
		echo $msg;
	}
	
	
	/**
	*	获取列表
	*/
	public function getoainfoAjax()
	{
		$barr = $this->xinhuobj->getdata('dengji','getdata');
		$rows = array();
		if($barr['success']){
			$rows = $barr['data'];
		}
		return array(
			'rows' => $rows
		);
	}
	public function deldengjiAjax()
	{
		$id   = (int)$this->post('id');
		$barr = $this->xinhuobj->getdata('dengji','deldengji', array('id'=>$id));
		return $barr;
	}
	public function savedengjiAjax()
	{
		$id   	= (int)$this->post('id');
		$name   = str_replace(' ','',$this->post('name'));
		$url    = str_replace(' ','',$this->post('url'));
		if(substr($url,0,4)!='http')return returnerror('地址必须http开头');
		if(contain($url,'127.0.0.1') || contain($url,'localhost'))return returnerror('本地地址是不能登记到官网的');
		if(substr($url,-1)!='/')$url.='/';
		$carr 	= m('task')->pdlocal($url);
		if(!$carr['success'])return returnerror('系统地址无法打开');
		$barr 	= $this->xinhuobj->postdata('dengji','savedata', array(
			'id'	=> $id,
			'name'	=> $name,
			'url'	=> urlencode($url),
		));
		return $barr;
	}
}