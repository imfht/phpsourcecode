<?php
namespace blog\libs\template;
use \sy\base\Router;
use \blog\libs\Html;
class ItemBase {
	protected $sub;
	public function __construct($sub) {
		$this->sub = $sub;
	}
	public function __call($name, $args) {
		if (isset($this->sub[$name])) {
			if (is_array($args) && count($args) > 0) {
				echo $this->sub[$name];
			} else {
				echo Html::encode($this->sub[$name]);
			}
		} else {
			throw new SYException('Method ' . $name . ' not exists', '20000');
		}
	}
	public function __get($name) {
		if (isset($this->sub[$name])) {
			return $this->sub[$name];
		} else {
			return NULL;
		}
	}
	public function __set($k, $v) {
		if (isset($this->sub[$k])) {
			$this->sub[$k] = $v;
		} elseif ($k !== 'sub') {
			$this->$k = $v;
		} else {
			throw new SYException('Sub is readonly', '20001');
		}
	}
}