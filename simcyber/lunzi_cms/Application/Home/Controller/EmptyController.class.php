<?php
namespace Home\Controller;
use Think\Controller;
class EmptyController extends Controller {
	public function _empty(){
		$this->display('Empty/404');
	}
}