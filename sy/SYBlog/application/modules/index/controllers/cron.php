<?php

/**
 * Cron
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
use \sy\base\Controller;
use \blog\libs\Common;
use \blog\model\Sitemap;

class Cron extends Controller {
	protected $password;
	public function __construct() {
		$this->password = Common::option('cronPassword');
		if ($_REQUEST['password'] !== $this->password) {
			header(Sy::getHttpStatus(403));
			exit;
		}
	}
	public function actionSitemap() {
		$sitemap = new Sitemap();
		$action = $_REQUEST['action'];
		if ($action === 'make') {
			$sitemap->make();
		}
	}
}