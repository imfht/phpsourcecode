<?php

namespace Common\Controller;

use Common\Controller\AppframeController;

class HomeBaseController extends AppframeController {

	function _initialize() {

		parent::_initialize();

		//site_options
		$site_options = F("site_options");
		if (empty($site_options)) {
			$site_options = get_site_options();
			F("site_options", $site_options);
			$this->assign($site_options);
		} else {
			$this->assign($site_options);
		}
	}

}
