<?php

!defined('FRAMEWORK_PATH') && exit('Access Deined.');

class index_control extends base_control {

    function __construct(&$conf) {
        parent::__construct($conf);
    }

    public function on_index() {
        $this->show('index.htm');
    }
    public function on_about() {
        $this->show('about.htm');
    }

    /**
     * 查看icon font
     * 访问地址：?c=index-iconfont
     */
    public function on_iconfont() {
        $this->show('iconfont.htm');
    }
}

?>