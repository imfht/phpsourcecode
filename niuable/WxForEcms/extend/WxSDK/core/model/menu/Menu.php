<?php

namespace WxSDK\core\model\menu;


class Menu {
	public $button;
	public $matchrule;
	function __construct(MatchRule $matchRule = null, Button... $button) {
		$this->button = $button;
		$this->matchrule = $matchRule;
	}
}
