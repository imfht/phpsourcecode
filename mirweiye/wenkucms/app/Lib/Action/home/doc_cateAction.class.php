<?php

class doc_cateAction extends frontendAction {

	
  public function ajax_getsubcate() {
  	
        $id = $this->_get('id', 'intval',0);
        
        $return = D('doc_cate')->field('id,name')->where(array('pid'=>$id,'status'=>1))->select();
        if ($return) {
            $this->ajaxReturn(1, L('operation_success'), $return);
        } else {
            $this->ajaxReturn(0, L('operation_failure'));
        }
    }
}