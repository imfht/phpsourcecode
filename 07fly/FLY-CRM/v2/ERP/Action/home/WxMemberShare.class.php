<?php	 
/*
 * 分离
 */	 
class WxMemberShare extends Action{	
	private $cacheDir='c_home';//缓存目录
	//检查是否有登录
	public function member_share() {
		$member		=$this->L('home/WxMember')->member_get_info();
		$member_acct=$member['account'];
		$conf		=$this->L('home/WxConfig')->get_sys_info();
		$url		=$conf['cfg_basehost']."/index.php/home/Register/main/recommend_acct/$member_acct/";
		$smarty =$this->setSmarty();
		$smarty->assign(array('url'=>$url));
		$smarty->display('home/member_share.html');	
	}

	public function member_share_save() {
		$img = file_get_contents('http://www.baidu.com/img/baidu_logo.gif'); 
		file_put_contents('1.gif',$img); 
		$rtn_msg=array('code'=>'sucess','message'=>'保存成功');
		echo json_encode($rtn_msg);
	}

	public function member_share_log() {
		$list	=$this->L('home/WxMember')->member_get_son_list();
		$smarty =$this->setSmarty();
		$smarty->assign(array('list'=>$list));
		$smarty->display('home/member_share_log.html');	
	}
	
	

}//
?>