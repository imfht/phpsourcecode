<?php

class advertAction extends frontendAction {

    public function tgo() {
        $id = $this->_get('id', 'intval', 0);
        $url = M('ad')->where(array('id'=>$id))->getField('url');
        !$url && $this->_404();
        redirect($url);
    }
}