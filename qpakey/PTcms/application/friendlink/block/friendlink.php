<?php

class FriendlinkBlock extends PT_Base {

    public function run($param) {
        $num  = $this->input->param('num', 'int', 10, $param);
        $list = $this->model('friendlink')->where(array('status' => 1))->field('name,url,description,logo,isbold,color')->order('ordernum asc')->limit($num)->getlist();
        return $list;

    }
}