<?php

/**
 * Description of index
 *
 * @author Administrator
 */
class Welcome extends MpController {

    public function doIndex($name = '') {
        $this->helper('config');
        $this->view("welcome", array('msg' => $name, 'ver' => $this->config('myconfig', 'app')));
    }

}
