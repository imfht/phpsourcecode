<?php

namespace API\Controller;

use Think\Controller;
use API\Service\MainMenuApiService;

/**
 * 主菜单 Controller
 * 
 * @author 李静波
 *        
 */
class MainMenuController extends Controller {

	/**
	 * 返回主菜单
	 */
	public function mainMenuItems() {
		if (IS_POST) {
			$params = [
					"tokenId" => I("post.tokenId")
			];
			
			$service = new MainMenuApiService();
			
			$this->ajaxReturn($service->mainMenuItems($params));
		}
	}
}