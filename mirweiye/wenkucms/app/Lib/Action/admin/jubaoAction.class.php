<?php
class jubaoAction extends backendAction {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('jubao');
    }

    public function _before_index() {
        //默认排序
        $this->sort = 'add_time';
        $this->order = 'DESC';
    }
}