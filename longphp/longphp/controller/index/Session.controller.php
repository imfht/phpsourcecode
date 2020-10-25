<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

if(!defined('DIR')){
    exit('请正确访问URL');
}

class Action_Session extends Libs{
    function __construct(){
        $this->db = 'db1';
    }

    function index(){
        global $key;
        $this->load_class('session');
        $session = new Session($this->db1);

        $sid = $session->generate_sid();

        /*$test_data = array(
            'uid' => 111,
            'username' => '于文龙'
        );

        $session->set_session($sid, $test_data);*/
        //$session->del_session($sid);
        //$content = $session->get_session($sid);
    }
}
