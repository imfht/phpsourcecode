<?php

class CommonController extends PT_Controller {

    public function init() {
        if (!$this->config->get('app_status', true)) {
            $this->error($this->config->get('app_closemsg', '网站升级中，请稍后访问！'), 0, 0);
        }
    }
}