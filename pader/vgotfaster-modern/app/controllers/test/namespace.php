<?php

namespace Controller;

class NamespaceController extends \MyController {

	public function index($p1=null, $p2=null) {
		echo 'The Modern VgotFaster Framework';

		$this->load->database();

		printr($this->input->uri('params'));

		echo '<br />NameSpace';

		$this->load->model('user');
		$this->load->model('dir/another');

		echo $this->another->getModelName();
		echo '<br />';

		$start = microtime(true);
		$mem = memory_get_usage();

		$b = null;
		$a = array('a', 'b', 'c', 'd', 'e', 'f', 'g');

		foreach ($a as $j => $v) {
			if ($v == 'd' || $v == 'f') {
				unset($a[$j]);
			}
		}

		printr($a);

		for ($i=0; $i<1000000; $i++) {
		}

		echo microtime(true) - $start;
		echo '<br />';
		echo memory_get_usage() - $mem;
		echo '<br />';
		echo $b;

		$this->config->abs();

		$this->load->library('image', null, $what, $the, $fuck);

		printr($this->image->godigo());

		echo '\""\n';
		echo '<br />';

		$this->config->set('config', 'default_controller', 'what');

		echo $this->config->get('config', 'default_controller');
	}

	public function library() {
		$class = new \ReflectionClass('Controller\Hello');
		$object = $class->newInstanceArgs(array(55, 65));
		echo $object->show();

		$this->load->database();

		printr($this->db->ping());

		$this->db->get('text');

		while ($row = $this->db->fetch()) {
			printr($row);
		}

		echo APPLICATION_PATH;
		echo SYSTEM_PATH;
	}

	public function y() {
		static $object = null;

		if ($object === null) {
			$object = new Hello;
		}

		return $object;
	}

	public function testLoader() {
		$this->load->model('user');
		$this->load->library('image');

		$this->user->realtimeLoadInvoke();
	}

}

class Hello {

	protected $a = '111';
	private $arg1 = '';
	private $arg2 = '';

	public function __construct($arg1='', $arg2='') {
		$this->arg1 = $arg1;
		$this->arg2 = $arg2;
	}

	public function show() {
		return 'Something'.$this->arg1.'-'.$this->arg2;
	}

}