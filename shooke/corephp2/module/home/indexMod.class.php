<?php
namespace module\home;
use lib\Action,lib\RBAC;
class indexMod extends Action{
    
	public function index() {
		$this->redirect(url('meeting/index'));
	}
	
	
}