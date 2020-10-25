<?php
class txl_jiucuoAction extends backendAction {
	public function _before_index() {
        //默认排序
        $this->sort = 'add_time';
        $this->order = 'ASC';

        //取公司名
        $gsname=D('txl')->select();
        foreach ($gsname as $val) {
            $gsname[$val['id']] = $val['title'];
        }
         $this->assign('gsname',$gsname);
    }

     
}
