<?php

namespace API\Service;

use API\DAO\MainMenuApiDAO;

/**
 * 主菜单Service
 *
 * @author 李静波
 */
class MainMenuApiService extends PSIApiBaseService {

	public function mainMenuItems($params) {
		$tokenId = $params["tokenId"];
		if ($this->tokenIsInvalid($tokenId)) {
			return $this->emptyResult();
		}
		
		$params["userId"] = $this->getUserIdFromTokenId($tokenId);
		
		$dao = new MainMenuApiDAO($this->db());
		return $dao->mainMenuItems($params);
	}
}