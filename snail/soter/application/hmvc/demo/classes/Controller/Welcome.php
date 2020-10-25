<?php

class Controller_Welcome extends Soter_Controller {

	public function do_index() {
		echo "from hvmc Demo<br>";
		return Sr::view()->loadParent('index');
	}

}
