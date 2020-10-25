<?php
class pageAction extends backendAction {
	public function _before_index() {
        //默认排序
        $this->sort = 'ordid';
        $this->order = 'ASC';
    }
}
