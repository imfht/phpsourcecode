<?php

namespace Model;

class User extends \Model {

	public function getDesc() {
		return 'I am User Model';
	}

	public function realtimeLoadInvoke() {
		$this->load->model('dir/another');
		printr('--do invoke--');
		echo $this->another->test();
		$this->load->library('testLib');
		echo $this->testLib->invoke();
	}

}

