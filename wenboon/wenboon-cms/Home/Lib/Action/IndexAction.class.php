<?php
class IndexAction extends Action {
    public function index(){
           $this->site=M('site')->find(1);
           $this->type=null;
	       $this->display();
    }
}