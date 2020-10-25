<?php

/**
 * 获取一些基本信息
 * 
 * @author ShuangYa
 * @package Blog
 * @category Controller
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\controller;
use \Sy;
use \blog\libs\ApiController;
use \blog\libs\Common;
use \blog\model\Meta;

class Index extends ApiController {
	public function actionMeta() {
		$this->success(Meta::getList(['type' => 1])->getAll());
	}
}
