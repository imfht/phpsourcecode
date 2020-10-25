<?php
class txl_cateAction extends backendAction {
	public function _before_index() {
        //默认排序
        $this->sort = 'ordid';
        $this->order = 'ASC';
    }

    protected function _search() {
        $map = array();
    
        ($keyword = $this->_request('keyword', 'trim')) && $map['name'] = array('like', '%'.$keyword.'%');
        $this->assign('search', array(
            'keyword' => $keyword,
            
        ));
        return $map;
    }
}
