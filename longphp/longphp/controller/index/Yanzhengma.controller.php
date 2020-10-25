<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

class Action_Yanzhengma extends Libs{
    function index(){
        $this->load_class('verification_code');
    }
}
