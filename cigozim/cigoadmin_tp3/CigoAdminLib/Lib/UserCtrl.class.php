<?php

namespace CigoAdminLib\Lib;

interface UserCtrl {
	/**
	 * 检查是否登陆
	 */
	public function isLogIn();

	/**
	 * 执行登陆操作
	 *
	 * @param $userInfo 用户信息
	 */
	public function doLogIn($userInfo);

	/**
	 * 执行退出登陆操作
	 */
	public function doLogOut();

	/**
	 * 修改昵称
	 *
	 * @param $params
	 *
	 * @return
	 */
	public function modifyNickName($params);

	/**
	 * 修改密码
	 *
	 * @param $params 参数
	 */
	public function modifyPwd($params);
}