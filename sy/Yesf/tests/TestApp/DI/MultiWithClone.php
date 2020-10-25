<?php
namespace YesfApp\DI;

class MultiWithClone {
	public $id;
	public $cloned = false;
	public function __construct() {
		$this->id = '';
	}
	public function __clone() {
		$this->id = uniqid();
		$this->cloned = true;
	}
}