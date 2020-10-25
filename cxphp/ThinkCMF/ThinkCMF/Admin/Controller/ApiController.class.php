<?php

/**
 * 参    数：
 * 作    者：lht
 * 功    能：OAth2.0协议下第三方登录数据报表
 * 修改日期：2013-12-13
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ApiController extends AdminbaseController {

	//用户列表
	function index() {
		$rst = M('OauthMember')->where("status=1")->select();
		$this->assign('lists', $rst);
		//dump($rst);die;
		$this->display();
	}

	//删除用户
	function delete() {
		$id = intval($_GET['id']);
		if ($id) {
			$rst = M("OauthMember")->where("status=1 and ID=$id")->setField('status', '0');
			if ($rst) {
				$this->success("保存成功！", U("api/index"));
			} else {
				$this->error('会员删除失败！');
			}
		} else {
			$this->error('数据传入失败！');
		}
	}

	//设置
	function setting() {
		if ($_POST) {
			extract($_POST);
			$host = !C('site_host') ? '' : '@' . C('site_host');
			$config = array(
				'THINK_SDK_QQ'			 => array(
					'APP_KEY'	 => $qq_key,
					'APP_SECRET' => $qq_sec,
					'CALLBACK'	 => U('api/oauth/callback' . $host, array('type' => 'qq'), true, true),
				),
				'THINK_SDK_SINA'		 => array(
					'APP_KEY'	 => $sina_key,
					'APP_SECRET' => $sina_sec,
					'CALLBACK'	 => U('api/oauth/callback' . $host, array('type' => 'sina'), true, true),
				),
				'WECHAT_TOKEN'			 => $wx_tok,
				'WECHAT_APPID'			 => $wx_id,
				'WECHAT_APPSECRET'		 => $wx_sec,
				'WECHAT_AUTO_REPLY'		 => $wx_auto_reply,
				'WECHAT_AUTO_DEFAULT'	 => $wx_auto_default,
			);
			if (false !== F('sdk_options', $config, C('CMF_CONF_PATH'))) {
				$this->success("更新成功！");
			} else {
				$this->error("更新失败！");
			}
			exit;
		}
		$this->display();
	}

}
