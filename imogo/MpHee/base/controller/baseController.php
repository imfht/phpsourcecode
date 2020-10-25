<?php
class baseController extends controller{
	
	//T-Team增加base的某些验证
	public function __construct(){
		$ppid = $_GET['ppid'];
		$uuid = $_GET['uuid'];
		if( !empty($ppid) ){
			$this->ppid = $ppid;
			if( !isset( $_SESSION )) session_start();
			if( empty($uuid) ){
				$this->checkUuid($ppid);
			}else{
				$this->checkUrl($uuid,$ppid);
			}
		}else{
			/*$gourl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."&ppid=".$this->ppid;
			$this->redirect($gourl,true);*/
		}
		parent::__construct();
	}
	
	protected function checkUrl($uuid,$ppid){
		set_session('uuinfo_'.$ppid,array('uuid'=>$uuid));
		$gourl = $this->newurl('uuid','http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$this->redirect($gourl,true);
	}
	
	protected function checkUuid($ppid){
		$this->uuinfo = get_session('uuinfo_'.$ppid);
		$this->uuid = $this->uuinfo['uuid'];
	}
	
	//删除url中某个参数
	protected function newurl($param, $url) {
        return preg_replace(
            array("/{$param}=[^&]*/i", '/[&]+/', '/\?[&]+/', '/[?&]+$/',),
            array('', '&', '?', '',),
            $url
        );
    }
	
}