<?php 

class MobileCheckBehavior extends Behavior {
	protected $options = array();
	
	public function run(&$params) {
		if (isMobile()) {
			C('ismobile', 1);
		}
	}
}