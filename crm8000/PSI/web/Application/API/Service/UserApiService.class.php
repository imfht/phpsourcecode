<?php

namespace API\Service;

use API\DAO\UserApiDAO;

/**
 * 用户Service
 *
 * @author 李静波
 */
class UserApiService extends PSIApiBaseService {

	public function doLogin($params) {
		$dao = new UserApiDAO($this->db());
		
		$userId = $dao->doLogin($params);
		if ($userId) {
			$result = $this->ok();
			
			$tokenId = session_id();
			session($tokenId, $userId);
			
			$result["tokenId"] = $tokenId;
			
			$fromDevice = $params["fromDevice"];
			if (! $fromDevice) {
				$fromDevice = "移动端";
			}
			
			$service = new BizlogApiService();
			$log = "从{$fromDevice}登录系统";
			$service->insertBizlog($tokenId, $log);
			
			return $result;
		} else {
			return $this->bad("用户名或密码错误");
		}
	}

	public function doLogout($params) {
		$result = $this->ok();
		
		$tokenId = $params["tokenId"];
		if (! $tokenId) {
			return $result;
		}
		
		if ($this->tokenIsInvalid($tokenId)) {
			return $result;
		}
		
		$fromDevice = $params["fromDevice"];
		if (! $fromDevice) {
			$fromDevice = "移动端";
		}
		
		$service = new BizlogApiService();
		$log = "从{$fromDevice}退出系统";
		$service->insertBizlog($tokenId, $log);
		
		// 清除session
		session($tokenId, null);
		
		return $result;
	}

	public function getDemoLoginInfo() {
		$result = $this->ok();
		
		if ($this->isDemo()) {
			$result["msg"] = "当前处于演示环境，请勿保存正式数据，默认的登录名和密码均为 admin";
		} else {
			$result["msg"] = "";
		}
		
		return $result;
	}
}