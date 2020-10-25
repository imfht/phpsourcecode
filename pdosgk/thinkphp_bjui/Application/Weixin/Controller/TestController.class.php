<?php
namespace Weixin\Controller;
use Think\Controller;
class TestController extends Controller {
	public function index(){
		$data['openID'] = 'oDoh3txDkIqYwY3_GNxxsoMc-tn8';
		$data['toUserName'] = 'gh_75c7443685dd';
		$data['content'] = 'sdfasf';
		$data['createTime'] = 1231234;
		$data['msgType'] = 2;
		
		//保存信息
		M('weixin')->add($data);
	}
	
}