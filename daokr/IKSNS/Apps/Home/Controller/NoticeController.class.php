<?php
/*
 * IKPHP 爱客开源社区 @copyright (c) 2012-3000 IKPHP All Rights Reserved 
 * @author 小麦
 * @Email:810578553@qq.com
 * @爱客网 网站升级提醒
 */
namespace Home\Controller;
use Common\Controller\FrontendController;

class NoticeController extends FrontendController {
	public function _initialize() {
		parent::_initialize ();
	}
    //网站提醒
    public function isupdate(){
    	//$ref = $_SERVER['HTTP_REFERER'];
    	//$ip = get_client_ip();
    	$v = I('get.v'); 
    	if($v<IKPHP_VERSION){
			$html = '<tr><td width="100" style="color:red">发现有新版本：</td><td style="color:red">IKPHP 1.5.5 版本 <a href="http://www.ikphp.cn/home/help/download" target="_blank">[下载升级包]</a></td></tr>
    		<tr><td width="100" style="color:red">官方消息：</td><td style="color:red">IKPHP 1.5.5 版本修复了相册得批量上传；QQ联合登录</a></td></tr>';
    		$this->ajaxReturn($html,'JSONP');
    	}else{
    		$html = '<tr><td width="100" style="color:red">官方消息：</td><td style="color:red">暂时还没有发现新的升级包</a></td></tr>';
    		$this->ajaxReturn($html,'JSONP');
    	}
    }
    //自动获取升级包信息给本地服务器
    public function getVersionInfo(){
		$result = M('system_update')->field('id,title,version,package')->where('status=0')->select();
	    //替换键值
		foreach ($result as $k=>$v){
			$list[$v['id']] = $v; 
			unset($result[$k]);
		}
		echo json_encode ( $list );   	
    }
}