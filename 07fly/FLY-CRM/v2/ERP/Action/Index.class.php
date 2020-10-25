<?php
class Index extends Action {
	private $cacheDir = ''; //缓存目录
	private $smarty = '';
	private $assign =array();
	
	public function __construct() {
		_instance('Action/sysmanage/Auth');
	}
	public function main(){
		$this->location('','/sysmanage/Index/');
/*		$member=$this->L('home/WxMember')->member_get_info();
		$notice=$this->L('home/WxNotice')->notice();
		$banner=$this->L('home/WxNoticeBanner')->notice_banner();
		$goods =$this->L('home/WxGoods')->goods();
		$smarty =$this->setSmarty();
		$smarty->assign(array('member'=>$member,'notice'=>$notice['list'],'banner'=>$banner['list'],'goods'=>$goods['list']));
		$smarty->display('home/index.html');*/
	}

} //
?>