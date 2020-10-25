<?php
namespace App;

use Swoole;

/**
 * Description of BasicController
 * 基础控制器
 * @author Xiang dongdong<xiangdong198719@gmail.com>
 */
class BasicController extends Swoole\Controller {

	public $_auth;

	/**
	 * 加入认证
	 * @param Swoole $swoole
	 */
	function __construct(Swoole $swoole) {
		parent::__construct($swoole);
		Swoole::$php->session->start();
		Swoole\Auth::loginRequire();
	}

	/**
	 * 获取用户信息
	 * @return type
	 */
	function getUid() {

		if ($_SESSION["user_id"]) {
			$user = model("User");
			$user->select = "id,role";
			$info = $user->getUserInfo($_SESSION["user_id"]);
			return $info;
		} else {
			return false;
		}
	}

	/**
	 * 返回成功结果
	 * @param type $data
	 * @return type
	 */
	public function returnSucess($data) {
		$result["code"] = 1;
		$result["data"] = $data;
		return $result;
	}

	/**
	 * 返回失败结果
	 * @param type $message
	 * @return type
	 */
	public function returnFailure($message, $data = "") {
		$result["code"] = 0;
		$result["message"] = $message;
		if ($data) {
			$result["data"] = $data;
		}
		return $result;
	}

	/**
	 * ajax 表单返回
	 * @param type $message
	 * @param type $statusCode
	 * @param type $callbackType closeCurrent / forward  关闭当前 / 跳转
	 * @param type $forwardUrl   跳转url
	 * @param type $navTabId     要重新载入的navTaId
	 * @param type $rel
	 * @return type
	 */
	public function ajaxFromReturn($message, $statusCode = 200, $callbackType = "closeCurrent", $forwardUrl = "", $navTabId = "", $rel = "") {
		$data = [
			"statusCode" => $statusCode,
			"message" => $message,
			"navTabId" => $navTabId,
			"rel" => $rel,
			"callbackType" => $callbackType,
			"forwardUrl" => $forwardUrl,
		];
		return $data;
	}

}
