<?php

class Controller_Welcome extends Soter_Controller {

	public function do_index() {
		Sr::view()->load('index');
	}

}
