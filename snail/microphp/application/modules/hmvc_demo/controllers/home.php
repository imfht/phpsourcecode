<?php

/**
 * Description of index
 *
 * @author Administrator
 */
class Home extends MpController {

    public function doIndex($name = '') {
        $this->view("welcome", array('msg' => $name, 'ver' => $this->config('myconfig', 'app')));
    }


}

