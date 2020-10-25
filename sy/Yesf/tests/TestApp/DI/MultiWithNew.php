<?php
namespace YesfApp\DI;

class MultiWithNew {
	public $id;
	public function __construct() {
		$this->id = uniqid();
	}
}