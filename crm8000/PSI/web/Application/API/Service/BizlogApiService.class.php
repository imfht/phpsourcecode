<?php

namespace API\Service;

use API\DAO\BizlogApiDAO;
use Home\Service\IPService;

/**
 * 业务日志API Service
 *
 * @author 李静波
 */
class BizlogApiService extends PSIApiBaseService {

	private function getClientIP() {
		return get_client_ip();
	}

	/**
	 * 记录业务日志
	 *
	 * @param string $log
	 *        	日志内容
	 * @param string $category
	 *        	日志分类
	 */
	public function insertBizlog($tokenId, $log, $category = "系统") {
		if ($this->tokenIsInvalid($tokenId)) {
			return;
		}
		
		$ip = $this->getClientIP();
		
		$params = array(
				"loginUserId" => $this->getUserIdFromTokenId($tokenId),
				"log" => $log,
				"category" => $category,
				"ip" => $ip,
				"ipFrom" => (new IPService())->toRegion($ip),
				"dataOrg" => $this->getDataOrgFromTokenId($tokenId),
				"companyId" => $this->getCompanyIdFromTokenId($tokenId)
		);
		
		$dao = new BizlogApiDAO($this->db());
		$result = $dao->insertBizlog($params);
		if (! $result) {
			$result = $this->ok();
		}
		
		return $result;
	}
}