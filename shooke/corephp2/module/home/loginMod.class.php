<?php
namespace module\home;
use lib\Action,lib\RBAC;
class loginMod extends Action{
    
	public function login() {
		$this->display();
	}
	
	public function f(){
	    $db = model('tt');
	    $f = $db->formatFields();
	    $db->data(['t'=>'df'])->insert();
	    var_dump($f);
	}
}